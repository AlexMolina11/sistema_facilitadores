# Arquitectura y tablas intermedias de integración SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.1.0  
**Estado:** Vigente  
**Última actualización:** Agosto 2026

---

# 1. Objetivo

Este documento describe la arquitectura implementada para integrar SAF con el Sistema de Facilitadores FEPADE mediante tablas intermedias de importación.

La solución desacopla el origen de los datos del expediente funcional y permite validar, auditar y procesar cada recepción antes de modificar información del consultor.

---

# 2. Arquitectura general

```text
                       SAF
                        │
                        │ Nueva recepción
                        ▼
        ┌─────────────────────────────────────┐
        │ tbl_saf_instructor_importacion      │
        │ tbl_saf_capacitacion_importacion    │
        └─────────────────────────────────────┘
                        │
                        ▼
               Laravel Scheduler
                        │
                        ▼
                  Processors
                        │
                        ▼
                     DTO
                        │
                        ▼
             Servicios de sincronización
                        │
          ┌─────────────┼───────────────┐
          ▼             ▼               ▼
    tbl_consultor   documento/email   capacitaciones
          │             │               │
          └─────────────┴───────────────┘
                        │
                        ▼
               Auditoría / Bitácora
```

Las tablas intermedias son el único punto de entrada de información SAF.

---

# 3. Principios de diseño

La arquitectura aplica:

- separación de responsabilidades;
- tablas staging independientes de las tablas funcionales;
- historial por recepción;
- procesamiento transaccional;
- validación mediante DTO;
- idempotencia funcional;
- detección de cambios mediante hash;
- tolerancia a errores individuales;
- auditoría de ejecución y de errores;
- preservación de campos internos.

---

# 4. Tablas de importación

La integración utiliza:

```text
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
```

Estas tablas actúan como una bandeja histórica de recepciones. Una fila procesada no se reutiliza para representar una actualización posterior.

Cada cambio recibido desde SAF debe insertarse como una nueva fila.

---

# 5. Por qué las tablas staging son append-only

Inicialmente las tablas impedían repetir un instructor o evento mediante índices únicos. Esa restricción fue eliminada porque impedía conservar el historial de sincronización.

Ahora es válido tener:

```text
id_importacion 101 → instructor 450 → CREADO
id_importacion 126 → instructor 450 → ACTUALIZADO
id_importacion 180 → instructor 450 → SIN_CAMBIOS
```

Para conservar rendimiento se mantienen índices normales por los identificadores externos, pero no restricciones únicas de recepción.

Esto permite que cada fila conserve su propio:

- `id_sincronizacion`;
- `resultado_procesamiento`;
- `fecha_procesamiento`;
- `id_registro_local`;
- `mensaje_error`.

---

# 6. Ciclo de vida de una recepción

```text
PENDIENTE
    │
    ▼
EN_PROCESO
    │
    ├──────────────┐
    ▼              ▼
PROCESADO         ERROR
```

Los estados son administrados por Laravel.

`resultado_procesamiento` registra el resultado funcional, por ejemplo:

```text
CREADO
ACTUALIZADO
SIN_CAMBIOS
ERROR
OMITIDO
```

---

# 7. Arquitectura de instructores

```text
tbl_saf_instructor_importacion
            │
            ▼
SafInstructorImportacionProcessor
            │
            ▼
InstructorSafData
            │
            ▼
SafInstructorSyncService
            │
      ┌─────┼───────────┐
      ▼     ▼           ▼
tbl_consultor  tbl_consultor_documento  tbl_consultor_email
            │
            ▼
      Auditoría SAF
```

El servicio sincroniza los tres destinos dentro de una misma transacción.

---

# 8. Datos del instructor

La staging recibe:

- `id_instructor`;
- `id_entidad`;
- `nombres`;
- `apellidos`;
- `tipo_identificacion`;
- `numero_identificacion`;
- `correo_saf`;
- `activo`.

El campo anterior `dui` fue sustituido por `numero_identificacion`.

---

# 9. Documento y correo

Laravel traduce el código SAF del documento al catálogo local y almacena la identificación en `tbl_consultor_documento`.

También registra `correo_saf` en `tbl_consultor_email` como correo principal.

La integración SAF no utiliza `tbl_consultor.tipo_identificacion` ni `tbl_consultor.numero_identificacion` como destino. Esos campos se conservan por compatibilidad con el flujo manual existente.

---

# 10. Arquitectura de capacitaciones

```text
tbl_saf_capacitacion_importacion
            │
            ▼
SafCapacitacionImportacionProcessor
            │
            ▼
CapacitacionSafData
            │
            ▼
SafCapacitacionSyncService
            │
            ▼
tbl_consultor_capacitacion_fepade
            │
            ▼
       Auditoría SAF
```

La capacitación solo puede sincronizarse si existe un consultor asociado con el `id_instructor` recibido.

---

# 11. Nuevo contrato de capacitaciones

La staging utiliza actualmente:

- `id_instructor`;
- `programa_curso_id`;
- `codigo_evento`;
- `curso_nombre`;
- `fecha_inicio`;
- `fecha_fin`;
- `estado_curso_nombre`;
- `no_horas_real`;
- `modalidad`;
- `tipo_evento_nombre`;
- `cliente`;
- `encuesta_id`;
- `encuesta_nombre`;
- `promedio_encuesta`;
- `fecha_evaluacion`.

Se eliminaron del contrato:

- `tema`;
- `activo`.

Se renombraron:

```text
codigo_evento_externo → codigo_evento
nombre_evento         → curso_nombre
institucion           → cliente
horas                 → no_horas_real
```

El campo técnico `estado` de staging no se renombró. `estado_curso_nombre` es un campo independiente proveniente de SAF.

---

# 12. Identidad funcional y prevención de duplicados

Aunque staging admite múltiples recepciones, las tablas funcionales evitan duplicar la misma entidad lógica.

Para instructores, Laravel localiza al consultor por:

```text
id_instructor
```

Para capacitaciones, la identidad funcional utilizada es:

```text
id_consultor + codigo_evento
```

Por eso un nuevo envío de un evento existente genera una actualización o `SIN_CAMBIOS`, no una segunda capacitación funcional.

---

# 13. Hash de datos SAF

Los DTO generan una representación estable de los datos recibidos y calculan un hash.

El hash del instructor incluye también documento y correo SAF.

El hash de capacitación incluye los campos del nuevo contrato, incluidos los datos de encuesta.

Esto permite detectar una actualización posterior como:

```text
encuesta_id       NULL → 125
promedio_encuesta NULL → 4.75
```

sin crear otro evento funcional.

---

# 14. Preservación de la gestión interna

En `tbl_consultor_capacitacion_fepade`, los campos:

- `activo`;
- `deleted_at`;
- `usuario_elim`;

son internos.

Una actualización SAF no debe cambiar esos valores.

Una capacitación creada por SAF nace activa, pero posteriormente su activación/desactivación pertenece al Sistema de Facilitadores.

---

# 15. Validaciones de instructor

Entre las validaciones principales se encuentran:

- `id_instructor` obligatorio y mayor que cero;
- `id_entidad` obligatorio y mayor que cero;
- nombres obligatorios;
- apellidos obligatorios;
- tipo de identificación permitido;
- número de identificación con longitud válida;
- `correo_saf` con formato válido cuando se envía;
- valor de `activo` válido.

Tipos SAF permitidos:

```text
2 = NIT
4 = Pasaporte
5 = Licencia de conducir
7 = DUI
```

---

# 16. Validaciones de capacitación

Entre las validaciones principales se encuentran:

- instructor válido;
- consultor asociado existente;
- `programa_curso_id` válido;
- `codigo_evento` obligatorio;
- `curso_nombre` obligatorio;
- fechas válidas;
- `fecha_fin` igual o posterior a `fecha_inicio`;
- `no_horas_real` entero y no negativo;
- longitudes máximas acordadas;
- datos de encuesta numéricos y fechas válidas cuando se proporcionan.

Los campos de encuesta pueden permanecer nulos.

---

# 17. Auditoría

Cada ejecución genera un registro en:

```text
tbl_sincronizacion_saf
```

Cada recepción queda asociada a la ejecución mediante:

```text
id_sincronizacion
```

Los errores individuales se registran en:

```text
tbl_sincronizacion_saf_error
```

Esto permite reconstruir qué ocurrió en una ejecución específica sin sobrescribir recepciones anteriores.

---

# 18. Tolerancia a errores

Un error individual no detiene todo el lote.

Cuando una recepción falla:

```text
estado = ERROR
resultado_procesamiento = ERROR
```

Laravel registra el motivo y continúa con la siguiente fila pendiente.

---

# 19. Procesamiento programado

El sistema procesa automáticamente las importaciones una vez al día a las:

```text
05:00 a. m.
```

El orden es:

1. instructores pendientes;
2. capacitaciones pendientes;
3. cierre de auditoría;
4. disponibilidad de resultados en Bitácora SAF.

También puede ejecutarse manualmente:

```bash
php artisan saf:procesar-importaciones
```

---

# 20. Componentes principales

La implementación utiliza:

## Modelos de staging

- `SafInstructorImportacion`;
- `SafCapacitacionImportacion`.

## DTO

- `InstructorSafData`;
- `CapacitacionSafData`.

## Processors

- `SafInstructorImportacionProcessor`;
- `SafCapacitacionImportacionProcessor`.

## Servicios

- `SafInstructorSyncService`;
- `SafCapacitacionSyncService`;
- `SafAuditService`.

---

# 21. Beneficios

La arquitectura permite:

- mantener historial real de cada recepción;
- evitar escrituras directas sobre el expediente;
- detectar cambios con precisión;
- reparar errores sin perder evidencia histórica;
- separar datos SAF de gestión interna;
- incorporar nuevos campos sin rediseñar el flujo completo;
- reutilizar el mismo patrón para futuras integraciones.

---

# 22. Conclusión

Las tablas intermedias constituyen una frontera técnica entre SAF y el Sistema de Facilitadores. SAF entrega datos; Laravel controla su ciclo de vida, validación, sincronización y auditoría.

La eliminación de restricciones únicas en staging y la conservación de cada recepción como una fila independiente permiten mantener una bitácora confiable y soportar correctamente altas, actualizaciones, registros sin cambios y correcciones posteriores.
