# Principio de responsabilidad de la integración SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.1.0  
**Estado:** Vigente  
**Última actualización:** Agosto 2026

---

# 1. Objetivo

Este documento define la separación de responsabilidades entre SAF y el Sistema de Facilitadores FEPADE durante el intercambio, validación y sincronización de información de instructores y capacitaciones.

El objetivo es mantener una integración desacoplada, auditable y segura, donde SAF sea responsable de los datos de origen y Laravel sea responsable de su procesamiento dentro del Sistema de Facilitadores.

---

# 2. Principio general

La integración se basa en el siguiente principio:

> **SAF es propietario de los datos de negocio que origina.**  
> **Laravel es propietario del proceso de recepción, validación, sincronización, auditoría y gestión interna.**

SAF no escribe directamente sobre el expediente funcional de un consultor. La información llega primero a tablas intermedias de importación y Laravel decide cómo incorporarla a las tablas funcionales.

---

# 3. Responsabilidad de SAF

SAF es responsable de:

- mantener actualizados los datos de instructores y capacitaciones en su sistema de origen;
- enviar registros a las tablas intermedias definidas para la integración;
- utilizar los nombres, tipos, longitudes y valores establecidos en el contrato técnico;
- mantener consistentes los identificadores externos;
- enviar una nueva recepción cuando un dato previamente enviado cambie;
- enviar únicamente información de negocio y los valores mínimos de recepción acordados técnicamente;
- respetar la semántica del campo `Inactivo` de SAF y convertirlo al campo `activo` utilizado por la tabla de importación de instructores.

SAF no debe:

- escribir directamente sobre `tbl_consultor`;
- escribir directamente sobre `tbl_consultor_documento`;
- escribir directamente sobre `tbl_consultor_email`;
- escribir directamente sobre `tbl_consultor_capacitacion_fepade`;
- modificar resultados de procesamiento;
- modificar errores generados por Laravel;
- alterar sincronizaciones históricas;
- reutilizar una fila histórica de staging para representar una recepción posterior.

---

# 4. Responsabilidad del Sistema de Facilitadores

Laravel es responsable de:

- detectar registros pendientes;
- reservar cada registro antes de procesarlo;
- validar la información recibida;
- normalizar datos mediante DTO;
- traducir los códigos externos cuando los catálogos SAF y Facilitadores no coinciden;
- crear o actualizar consultores;
- registrar documentos en `tbl_consultor_documento`;
- registrar el correo SAF en `tbl_consultor_email` como correo principal;
- crear o actualizar capacitaciones FEPADE;
- preservar campos internos que SAF no administra;
- evitar duplicados funcionales;
- ejecutar cambios dentro de transacciones;
- registrar auditoría de ejecución;
- registrar errores individuales;
- mantener el historial de cada recepción.

---

# 5. Separación de responsabilidades

| Funcionalidad | SAF | Facilitadores |
|---|:---:|:---:|
| Mantener datos de origen de instructores | ✔ | |
| Mantener datos de origen de capacitaciones | ✔ | |
| Insertar nuevas recepciones en staging | ✔ | |
| Validar datos recibidos | | ✔ |
| Traducir catálogos SAF → Facilitadores | | ✔ |
| Crear/actualizar `tbl_consultor` | | ✔ |
| Crear/actualizar `tbl_consultor_documento` | | ✔ |
| Crear/actualizar `tbl_consultor_email` | | ✔ |
| Crear/actualizar `tbl_consultor_capacitacion_fepade` | | ✔ |
| Administrar `activo` interno de una capacitación | | ✔ |
| Calcular hashes de sincronización | | ✔ |
| Gestionar estados de procesamiento | | ✔ |
| Registrar auditoría y errores | | ✔ |
| Resolver incidencias de datos en origen | ✔ | |
| Marcar incidencias de Bitácora como atendidas | | ✔ |

---

# 6. Datos de instructor administrados por SAF

SAF proporciona los siguientes datos de negocio:

- `id_instructor`;
- `id_entidad`;
- `nombres`;
- `apellidos`;
- `tipo_identificacion`;
- `numero_identificacion`;
- `correo_saf`;
- estado de vigencia del instructor.

En SAF el estado original se recibe como `Inactivo` y debe traducirse antes de escribir la tabla de importación:

```text
SAF Inactivo = 0  → staging activo = 1
SAF Inactivo = 1  → staging activo = 0
```

Laravel utiliza ese valor para actualizar `activo` y `vigente` del consultor.

---

# 7. Documento del instructor

La identificación proveniente de SAF no se guarda en los campos legacy de `tbl_consultor`.

Laravel la sincroniza en:

```text
tbl_consultor_documento
```

con la siguiente correspondencia:

| SAF `tipo_identificacion` | Documento SAF | `tbl_tipo_documento.id_tipo_documento` |
|---:|---|---:|
| 2 | NIT | 1 |
| 4 | Pasaporte | 4 |
| 5 | Licencia de conducir | 6 |
| 7 | DUI | 2 |

El número recibido en `numero_identificacion` se almacena en `tbl_consultor_documento.numero`.

Los campos legacy `tipo_identificacion` y `numero_identificacion` de `tbl_consultor` se mantienen temporalmente por compatibilidad con el flujo manual de consultores, pero la integración SAF no los utiliza como destino.

---

# 8. Correo del instructor

El campo `correo_saf` se sincroniza en:

```text
tbl_consultor_email
```

Laravel lo registra como:

```text
principal = 1
activo    = 1
```

Si existe otro correo principal, se conserva pero deja de ser principal. Si el correo SAF ya existe, se reutiliza y se restaura si se encontraba eliminado mediante Soft Delete.

---

# 9. Datos de capacitación administrados por SAF

SAF proporciona:

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

Los campos de encuesta pueden ser nulos mientras la capacitación todavía no haya sido evaluada.

---

# 10. Gestión interna de las capacitaciones

SAF ya no envía el campo `activo` para capacitaciones.

Cuando Laravel crea una capacitación SAF en `tbl_consultor_capacitacion_fepade`, la crea activa. A partir de ese momento, los siguientes campos son propiedad exclusiva de Facilitadores:

- `activo`;
- `deleted_at`;
- `usuario_elim`.

Por lo tanto, una recepción posterior de SAF puede actualizar los datos del curso o la encuesta, pero no reactiva, desactiva ni restaura automáticamente una capacitación cuya gestión interna haya sido modificada en Facilitadores.

---

# 11. Campos administrados exclusivamente por Laravel

En las tablas de importación existen campos técnicos que no forman parte del contrato de negocio de SAF, entre ellos:

- `estado`;
- `resultado_procesamiento`;
- `intentos`;
- `mensaje_error`;
- `fecha_recepcion`;
- `fecha_procesamiento`;
- `id_sincronizacion`;
- `id_registro_local`.

En las tablas funcionales también existen campos internos como:

- `fuente`;
- `fecha_ultima_sincronizacion_saf`;
- `hash_datos_saf`;
- campos de auditoría;
- `activo` interno de la capacitación;
- `deleted_at`.

SAF no debe modificar estos valores después de que Laravel inicia el procesamiento.

---

# 12. Tablas funcionales

Las tablas funcionales administradas por Laravel que intervienen actualmente son:

- `tbl_consultor`;
- `tbl_consultor_documento`;
- `tbl_consultor_email`;
- `tbl_consultor_capacitacion_fepade`.

SAF nunca escribe directamente sobre ellas.

---

# 13. Tablas intermedias como historial de recepciones

Las tablas:

```text
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
```

se utilizan como historial de entradas.

Cada envío o cambio recibido debe generar una nueva fila. No existe una restricción única que impida recibir nuevamente el mismo instructor o el mismo evento.

Esto permite conservar un historial como:

```text
Instructor 990001 → recepción 1 → CREADO
Instructor 990001 → recepción 2 → ACTUALIZADO
Instructor 990001 → recepción 3 → SIN_CAMBIOS
```

La misma regla aplica para capacitaciones.

---

# 14. Flujo de responsabilidades

```text
SAF
 │
 │ Nueva recepción de datos de negocio
 ▼
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
 │
 ▼
Laravel detecta PENDIENTES
 │
 ▼
Reserva EN_PROCESO
 │
 ▼
DTO + validaciones
 │
 ▼
Servicios de sincronización
 │
 ├── tbl_consultor
 ├── tbl_consultor_documento
 ├── tbl_consultor_email
 └── tbl_consultor_capacitacion_fepade
 │
 ▼
Auditoría / Bitácora SAF
 │
 ▼
PROCESADO o ERROR
```

---

# 15. Manejo de errores

Cuando un registro presenta un error:

- la fila queda con estado `ERROR`;
- se almacena `resultado_procesamiento = ERROR`;
- se registra un mensaje funcional;
- se registra el detalle en la Bitácora SAF;
- el resto de registros continúa procesándose.

Si el problema corresponde a un dato de origen, la corrección debe realizarse en SAF y enviarse como una nueva recepción.

No se deben sobrescribir ni eliminar los registros históricos con error.

---

# 16. Integridad transaccional

La sincronización de un instructor se realiza de forma atómica. La creación o actualización del consultor, documento y correo forma parte de una misma transacción.

Esto evita estados parciales como:

```text
consultor creado
+ documento creado
+ correo falló
```

Ante una excepción, la transacción se revierte y la incidencia queda registrada.

---

# 17. Beneficios del diseño

Este principio proporciona:

- desacoplamiento entre sistemas;
- protección del expediente funcional;
- trazabilidad por recepción;
- validación centralizada;
- procesamiento transaccional;
- detección de cambios mediante hash;
- prevención de duplicados funcionales;
- recuperación ante errores;
- mantenimiento más simple;
- facilidad para ampliar la integración.

---

# 18. Conclusión

SAF es responsable de entregar datos de negocio correctos y consistentes. Laravel es responsable de decidir cómo esos datos se incorporan al Sistema de Facilitadores, cómo se validan, cómo se auditan y qué información interna debe preservarse.

Esta separación constituye la regla base de toda integración presente o futura entre SAF y el Sistema de Facilitadores FEPADE.
