# Arquitectura y tablas intermedias de integración SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.0.0  
**Estado:** Vigente  
**Última actualización:** Julio 2026

---

# 1. Objetivo

Este documento describe la arquitectura implementada para la integración entre el Sistema de Administración de Facilitadores (SAF) y el Sistema de Facilitadores FEPADE.

La arquitectura fue diseñada para desacoplar ambos sistemas mediante tablas intermedias de importación, permitiendo validar la información antes de incorporarla al expediente del consultor.

---

# 2. Arquitectura general

La integración está basada en un proceso de sincronización por etapas.

```text
               SQL Server (SAF)
                      │
                      │
      Inserción / actualización de datos
                      │
                      ▼
───────────────────────────────────────────────
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
───────────────────────────────────────────────
                      │
                      ▼
Laravel detecta registros pendientes
                      │
                      ▼
Reserva del registro
                      │
                      ▼
Validación del DTO
                      │
                      ▼
Servicios de sincronización
                      │
                      ▼
Auditoría
                      │
                      ▼
───────────────────────────────────────────────
tbl_consultor
tbl_consultor_capacitacion_fepade
───────────────────────────────────────────────
```

Cada etapa posee una única responsabilidad claramente definida.

---

# 3. Principios de diseño

La arquitectura fue diseñada siguiendo los siguientes principios:

- separación de responsabilidades;
- desacoplamiento entre sistemas;
- procesamiento transaccional;
- validación centralizada;
- auditoría completa;
- tolerancia a errores;
- sincronización incremental;
- reutilización de servicios.

El objetivo es impedir que un error en un registro afecte el procesamiento del resto de la información recibida.

---

# 4. ¿Por qué utilizar tablas intermedias?

SAF no escribe directamente sobre las tablas funcionales del Sistema de Facilitadores.

En su lugar, toda la información es depositada inicialmente en tablas de importación.

Este enfoque permite:

- validar la información antes de sincronizarla;
- detectar inconsistencias;
- mantener auditoría completa;
- controlar intentos de procesamiento;
- continuar el procesamiento aunque existan errores;
- proteger la integridad del expediente del consultor;
- reutilizar la lógica de sincronización desde Laravel.

---

# 5. Tablas de importación

Actualmente la integración utiliza dos tablas intermedias.

## Tabla de instructores

```text
tbl_saf_instructor_importacion
```

Almacena temporalmente la información de instructores proveniente de SAF.

Su finalidad es servir como fuente para la actualización de la tabla **tbl_consultor**.

---

## Tabla de capacitaciones

```text
tbl_saf_capacitacion_importacion
```

Almacena temporalmente las capacitaciones enviadas por SAF.

Su finalidad es sincronizar la información con la tabla **tbl_consultor_capacitacion_fepade**.

---

# 6. Flujo de procesamiento

Cada registro recibido pasa por el mismo ciclo de vida.

```text
PENDIENTE
      │
      ▼
EN_PROCESO
      │
      ├───────────────┐
      ▼               ▼
PROCESADO         ERROR
```

Los estados son administrados exclusivamente por Laravel.

---

# 7. Procesamiento de instructores

El flujo de sincronización de instructores es el siguiente:

1. Laravel detecta instructores pendientes.
2. Reserva el registro mediante transacción.
3. Construye el DTO del instructor.
4. Valida la información.
5. Busca el consultor correspondiente.
6. Determina si debe crear, actualizar o desactivar.
7. Ejecuta la sincronización.
8. Registra auditoría.
9. Actualiza el estado del registro de importación.

---

# 8. Procesamiento de capacitaciones

El flujo de sincronización de capacitaciones sigue el mismo patrón.

1. Laravel detecta capacitaciones pendientes.
2. Reserva el registro.
3. Construye el DTO.
4. Ejecuta las validaciones.
5. Localiza el consultor asociado.
6. Determina si debe crear, actualizar o desactivar la capacitación.
7. Actualiza la información funcional.
8. Registra auditoría.
9. Marca el resultado del procesamiento.

---

# 9. Validaciones

Antes de sincronizar un registro, Laravel valida la información recibida.

Entre las validaciones implementadas se encuentran:

## Instructores

- identificador del instructor;
- nombres;
- apellidos;
- entidad;
- estado activo.

## Capacitaciones

- instructor existente;
- código de evento;
- nombre del evento;
- fecha de inicio;
- fecha de finalización;
- horas;
- consistencia entre fechas;
- valores numéricos válidos.

Únicamente los registros que superan las validaciones continúan con la sincronización.

---

# 10. Auditoría

Toda ejecución genera un registro en la tabla:

```text
tbl_sincronizacion_saf
```

La auditoría almacena:

- fecha de inicio;
- fecha de finalización;
- registros detectados;
- registros procesados;
- registros exitosos;
- registros con error;
- consultores creados;
- consultores actualizados;
- capacitaciones creadas;
- capacitaciones actualizadas;
- capacitaciones desactivadas;
- mensaje final.

---

# 11. Auditoría de errores

Cada error individual queda registrado en:

```text
tbl_sincronizacion_saf_error
```

Se almacena información como:

- tipo de registro;
- tipo de operación;
- identificador externo;
- identificador local;
- código del error;
- mensaje;
- detalle técnico;
- excepción;
- archivo;
- línea;
- datos recibidos.

Esta información facilita el diagnóstico y la trazabilidad de cada incidencia.

---

# 12. Manejo de errores

Cuando un registro presenta errores:

- se marca como **ERROR**;
- se registra el mensaje correspondiente;
- se almacena el detalle en la auditoría;
- el procesamiento continúa con el siguiente registro.

Esta estrategia evita que un único error interrumpa la sincronización completa.

---

# 13. Prevención de duplicados

Laravel determina automáticamente si un registro debe:

- crearse;
- actualizarse;
- mantenerse sin cambios;
- desactivarse.

La decisión se toma utilizando los identificadores externos enviados por SAF y la información existente en las tablas funcionales.

---

# 14. Procesamiento programado

El procesamiento se ejecuta mediante una tarea programada diaria.

La secuencia es:

1. detectar instructores pendientes;
2. sincronizar instructores;
3. detectar capacitaciones pendientes;
4. sincronizar capacitaciones;
5. cerrar la auditoría;
6. almacenar el resumen de ejecución.

---

# 15. Escalabilidad

La arquitectura permite incorporar nuevas integraciones sin modificar el diseño existente.

En futuras fases podrán añadirse tablas como:

- idiomas;
- experiencias laborales;
- referencias;
- disponibilidades;
- certificaciones;
- documentos.

Cada nueva integración reutilizará la misma arquitectura:

- tablas intermedias;
- DTO;
- validaciones;
- servicios;
- auditoría;
- sincronización.

---

# 16. Beneficios de la arquitectura

La solución implementada ofrece los siguientes beneficios:

- desacoplamiento entre SAF y Laravel;
- protección de las tablas funcionales;
- procesamiento seguro;
- validaciones centralizadas;
- auditoría completa;
- recuperación ante errores;
- sincronización incremental;
- reutilización de componentes;
- facilidad de mantenimiento;
- posibilidad de crecimiento sin rediseñar la arquitectura.

---

# 17. Conclusión

La integración entre SAF y el Sistema de Facilitadores FEPADE está basada en una arquitectura de tablas intermedias que desacopla el origen de los datos del proceso de sincronización.

Las tablas de importación actúan como una capa de recepción controlada, mientras que Laravel concentra toda la lógica de validación, auditoría y sincronización.

Este diseño garantiza la integridad de la información, facilita el mantenimiento del sistema y proporciona una base sólida para futuras integraciones con nuevos módulos o fuentes de datos.
