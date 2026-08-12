# Manual de Integración SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.1.0  
**Estado:** Vigente  
**Última actualización:** Agosto 2026

---

# 1. Introducción

La integración SAF permite incorporar al Sistema de Facilitadores información de instructores y capacitaciones originada externamente, sin permitir que el sistema origen modifique directamente el expediente funcional.

La solución utiliza tablas intermedias, DTO, Processors, servicios transaccionales y una Bitácora de sincronización.

---

# 2. Objetivo

El proceso debe:

- recibir información SAF de forma controlada;
- validar cada recepción;
- crear o actualizar información funcional;
- evitar duplicados;
- preservar campos internos;
- registrar errores individuales;
- conservar trazabilidad histórica;
- permitir ejecución automática y manual.

---

# 3. Arquitectura

```text
SAF
 │
 ▼
Tablas staging
 │
 ▼
Processors
 │
 ▼
DTO
 │
 ▼
Sync Services
 │
 ├── Consultor
 ├── Documento
 ├── Email
 └── Capacitación FEPADE
 │
 ▼
Auditoría / Bitácora SAF
```

---

# 4. Tablas de entrada

```text
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
```

Estas tablas son históricas: cada recepción se almacena como una fila independiente.

No se debe reutilizar una fila ya procesada para una actualización posterior.

---

# 5. Flujo general

1. SAF inserta una nueva recepción.
2. El registro queda `PENDIENTE`.
3. Laravel detecta registros pendientes.
4. El Processor reserva la fila como `EN_PROCESO`.
5. Se construye el DTO.
6. El DTO valida y normaliza los datos.
7. El servicio de sincronización ejecuta la lógica funcional.
8. Se registra auditoría.
9. La fila termina como `PROCESADO` o `ERROR`.

---

# 6. Procesamiento automático

El Scheduler ejecuta la integración diariamente a las:

```text
05:00 a. m.
```

El orden actual es:

```text
Instructores
    ↓
Capacitaciones
    ↓
Cierre de sincronización
```

Esto permite que una capacitación encuentre al consultor que pudo haberse creado en la misma ejecución.

---

# 7. Ejecución manual

Desde la raíz del proyecto:

```bash
php artisan saf:procesar-importaciones
```

Debe utilizarse para:

- pruebas;
- diagnóstico;
- validación posterior a una corrección;
- ejecución administrativa controlada.

Antes de repetir el comando en producción debe revisarse la Bitácora SAF y los registros pendientes.

---

# 8. Componentes del proyecto

## Modelos

- `SafInstructorImportacion`;
- `SafCapacitacionImportacion`;
- `Consultor`;
- `ConsultorDocumento`;
- `ConsultorEmail`;
- `ConsultorCapacitacionFepade`;
- modelos de auditoría SAF.

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

# 9. Flujo de instructor

```text
tbl_saf_instructor_importacion
        ↓
SafInstructorImportacionProcessor
        ↓
InstructorSafData
        ↓
SafInstructorSyncService
        ↓
┌───────────────────────────────┐
│ tbl_consultor                 │
│ tbl_consultor_documento       │
│ tbl_consultor_email           │
└───────────────────────────────┘
```

---

# 10. Datos recibidos del instructor

El DTO utiliza:

```text
id_instructor
id_entidad
nombres
apellidos
tipo_identificacion
numero_identificacion
correo_saf
activo
```

El campo `dui` ya no existe en la staging.

---

# 11. Creación de un consultor SAF

Si no existe un consultor con el `id_instructor` recibido, Laravel crea uno con origen SAF.

En `tbl_consultor` sincroniza los datos generales como:

- `id_instructor`;
- `id_entidad`;
- nombres;
- apellidos;
- `activo`;
- `vigente`;
- origen;
- fecha de última sincronización;
- hash.

El documento y el correo no se guardan directamente en esta tabla.

---

# 12. Sincronización de documento

El documento se guarda en:

```text
tbl_consultor_documento
```

Mapa SAF → Facilitadores:

```text
2 → 1 NIT
4 → 4 Pasaporte
5 → 6 Licencia de conducir
7 → 2 DUI
```

El servicio:

- busca coincidencia exacta;
- evita duplicados;
- restaura registros eliminados cuando corresponde;
- puede actualizar el número de un documento existente del mismo tipo;
- conserva otros tipos de documento válidos del consultor.

---

# 13. Sincronización de correo

`correo_saf` se almacena en `tbl_consultor_email`.

El correo SAF queda:

```text
principal = 1
activo = 1
```

Los correos anteriores no se eliminan. Si existía otro principal, pasa a secundario.

---

# 14. Transacción del instructor

Consultor, documento y correo se procesan dentro de una única transacción.

Si falla cualquiera de estas operaciones, no se conserva una sincronización parcial.

---

# 15. Actualización del instructor

Cuando ya existe un consultor con el mismo `id_instructor`:

- se calcula el nuevo hash SAF;
- se sincronizan documento y correo;
- se actualizan datos generales si cambiaron;
- se actualiza la fecha de sincronización;
- el resultado es `ACTUALIZADO` o `SIN_CAMBIOS`.

El hash incluye documento y correo, por lo que un cambio en cualquiera de ellos puede generar una actualización.

---

# 16. Instructor inactivo

SAF maneja `Inactivo`; la staging maneja `activo`.

La conversión es:

```text
Inactivo 0 → activo 1
Inactivo 1 → activo 0
```

Laravel refleja el valor en:

```text
consultor.activo
consultor.vigente
```

---

# 17. Flujo de capacitación

```text
tbl_saf_capacitacion_importacion
        ↓
SafCapacitacionImportacionProcessor
        ↓
CapacitacionSafData
        ↓
SafCapacitacionSyncService
        ↓
tbl_consultor_capacitacion_fepade
```

---

# 18. Datos de capacitación

El contrato vigente contiene:

```text
id_instructor
programa_curso_id
codigo_evento
curso_nombre
fecha_inicio
fecha_fin
estado_curso_nombre
no_horas_real
modalidad
tipo_evento_nombre
cliente
encuesta_id
encuesta_nombre
promedio_encuesta
fecha_evaluacion
```

---

# 19. Campos eliminados o renombrados

Ya no se utilizan:

```text
tema
activo (en staging SAF de capacitaciones)
```

Renombres:

```text
codigo_evento_externo → codigo_evento
nombre_evento         → curso_nombre
institucion           → cliente
horas                 → no_horas_real
```

---

# 20. Estado técnico vs estado del curso

No deben confundirse:

```text
estado
```

es el estado técnico de la recepción:

```text
PENDIENTE / EN_PROCESO / PROCESADO / ERROR
```

mientras:

```text
estado_curso_nombre
```

es un dato de negocio recibido desde SAF.

---

# 21. Relación con el consultor

Antes de crear o actualizar una capacitación, Laravel busca un consultor por:

```text
id_instructor
```

Si no existe, la capacitación queda con error y la Bitácora registra `CONSULTOR_NO_ENCONTRADO`.

---

# 22. Identidad de una capacitación

Laravel identifica la capacitación funcional por:

```text
id_consultor + codigo_evento
```

La staging puede contener múltiples recepciones del mismo evento.

---

# 23. Encuestas posteriores

Una capacitación puede recibirse sin encuesta.

Posteriormente SAF puede enviar otra recepción con:

- `encuesta_id`;
- `encuesta_nombre`;
- `promedio_encuesta`;
- `fecha_evaluacion`.

El hash cambia y Laravel actualiza la capacitación existente.

---

# 24. Gestión interna de `activo`

Las capacitaciones SAF nuevas se crean activas en la tabla funcional.

Sin embargo, SAF ya no controla el campo `activo`.

Una actualización SAF no modifica:

```text
activo
deleted_at
usuario_elim
```

Esto evita que una decisión interna de Facilitadores sea revertida por una nueva recepción externa.

---

# 25. Resultados posibles

## Instructor

```text
CREADO
ACTUALIZADO
SIN_CAMBIOS
ERROR
OMITIDO
```

## Capacitación

```text
CREADO
ACTUALIZADO
SIN_CAMBIOS
ERROR
```

`DESACTIVADO` ya no forma parte del comportamiento vigente de capacitaciones SAF.

---

# 26. Auditoría de ejecución

Cada ejecución se registra en:

```text
tbl_sincronizacion_saf
```

La auditoría permite conocer:

- inicio y finalización;
- registros detectados;
- registros procesados;
- creados;
- actualizados;
- sin cambios;
- errores;
- resultado global.

---

# 27. Auditoría individual

Cada fila staging guarda:

```text
id_sincronizacion
resultado_procesamiento
id_registro_local
mensaje_error
fecha_procesamiento
```

Los errores detallados se registran en:

```text
tbl_sincronizacion_saf_error
```

---

# 28. Reprocesamiento correcto

No debe cambiarse un registro histórico `ERROR` a `PENDIENTE` manualmente para representar una corrección de origen.

El flujo recomendado es:

```text
Error detectado
    ↓
Corregir dato en SAF
    ↓
SAF envía NUEVA recepción
    ↓
Laravel procesa nueva fila
    ↓
Validar resultado
```

La incidencia original permanece como evidencia histórica.

---

# 29. Pruebas recomendadas

Antes de desplegar cambios de integración deben probarse como mínimo:

- instructor nuevo;
- DUI;
- NIT;
- Pasaporte;
- Licencia de conducir;
- correo principal;
- cambio de documento;
- cambio de correo;
- instructor inactivo;
- `SIN_CAMBIOS`;
- capacitación nueva;
- encuesta posterior;
- actualización de capacitación;
- preservación de `activo` interno;
- tipo de identificación inválido;
- correo inválido;
- capacitación sin consultor;
- fechas inválidas;
- Bitácora SAF.

---

# 30. Diagnóstico básico

## Revisar pendientes de instructor

```sql
SELECT *
FROM tbl_saf_instructor_importacion
WHERE estado = 'PENDIENTE';
```

## Revisar pendientes de capacitación

```sql
SELECT *
FROM tbl_saf_capacitacion_importacion
WHERE estado = 'PENDIENTE';
```

## Revisar errores recientes

```sql
SELECT id_importacion, estado, resultado_procesamiento, mensaje_error
FROM tbl_saf_instructor_importacion
WHERE estado = 'ERROR'
ORDER BY id_importacion DESC;
```

---

# 31. Mantenimiento

Cuando cambie el contrato SAF deben revisarse conjuntamente:

- migraciones;
- modelos staging;
- DTO;
- Processors;
- Sync Services;
- hashes;
- búsquedas;
- expediente;
- CV;
- Bitácora;
- documentación.

No debe modificarse una sola capa de forma aislada si el nombre o significado del dato cambia.

---

# 32. Checklist de despliegue

Antes de producción:

- revisar `git status` y `git diff`;
- respaldar base de datos;
- ejecutar migraciones;
- ejecutar seeders de catálogo requeridos;
- limpiar cachés Laravel;
- verificar Scheduler;
- confirmar permisos de usuario SAF;
- realizar una recepción controlada;
- revisar Bitácora SAF;
- confirmar documento, correo y capacitación en expediente.

---

# 33. Archivos de configuración

La integración utiliza la configuración centralizada de SAF, incluyendo valores como la entidad permitida y parámetros de hash.

Los valores específicos de cada entorno deben mantenerse en configuración y variables de entorno, no codificarse directamente en los servicios.

---

# 34. Buenas prácticas

- no modificar tablas funcionales desde SAF;
- no eliminar filas staging históricas;
- no reutilizar una recepción procesada;
- no modificar manualmente hashes;
- no mezclar `estado` técnico con `estado_curso_nombre`;
- no reactivar capacitaciones desde SAF;
- revisar Bitácora antes de reprocesar;
- corregir datos de origen en SAF;
- mantener documentación y contrato sincronizados con el código.

---

# 35. Conclusión

La integración SAF utiliza una arquitectura de recepción histórica, procesamiento controlado y sincronización transaccional.

El diseño vigente separa correctamente la información de negocio de la gestión interna del Sistema de Facilitadores y permite auditar cada alta, actualización, ausencia de cambios o error sin perder información histórica.
