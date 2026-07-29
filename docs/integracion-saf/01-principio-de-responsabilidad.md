# Principio de responsabilidad de la integración SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.0.0  
**Estado:** Vigente  
**Última actualización:** Julio 2026

---

# 1. Objetivo

Este documento define la separación de responsabilidades entre el Sistema de Administración de Facilitadores (SAF) y el Sistema de Facilitadores FEPADE durante el proceso de sincronización de información.

El objetivo principal es establecer una arquitectura desacoplada, segura y mantenible, donde cada sistema sea responsable únicamente de las funciones que le corresponden.

Esta separación evita dependencias innecesarias, facilita el mantenimiento de ambas aplicaciones y reduce el riesgo de inconsistencias en la información compartida.

---

# 2. Principio general

La integración se basa en el siguiente principio:

> **SAF es propietario de los datos de negocio.**  
> **Laravel es propietario del proceso de sincronización.**

Esto significa que SAF únicamente proporciona la información que describe al instructor y sus capacitaciones, mientras que el Sistema de Facilitadores controla completamente el procesamiento, validación, auditoría y sincronización de dicha información.

---

# 3. Responsabilidad de SAF

SAF es el sistema origen de la información y tiene la responsabilidad de mantener actualizados los datos de negocio.

Entre sus responsabilidades se encuentran:

- generar los registros de instructores;
- generar los registros de capacitaciones;
- insertar nuevos registros en las tablas de importación;
- actualizar la información previamente enviada cuando existan cambios;
- mantener identificadores externos consistentes;
- enviar únicamente información de negocio;
- respetar los tipos y longitudes definidos para cada campo;
- indicar mediante el campo **activo** si un registro continúa vigente.

SAF **no realiza ningún proceso de sincronización**, únicamente deposita la información en las tablas intermedias.

---

# 4. Responsabilidad del Sistema de Facilitadores

Laravel es responsable de administrar completamente el ciclo de procesamiento de la información recibida.

Entre sus responsabilidades se encuentran:

- detectar registros pendientes;
- reservar registros para procesamiento;
- validar la información recibida;
- construir los DTO de negocio;
- sincronizar los datos con las tablas funcionales;
- crear registros nuevos;
- actualizar registros existentes;
- desactivar registros cuando corresponda;
- evitar duplicidades;
- registrar auditoría de cada ejecución;
- registrar auditoría de errores individuales;
- actualizar los estados del proceso;
- mantener la integridad transaccional.

Toda la lógica de sincronización reside exclusivamente dentro del Sistema de Facilitadores.

---

# 5. Separación de responsabilidades

La siguiente tabla resume la responsabilidad de cada sistema.

| Funcionalidad                     | SAF | Sistema de Facilitadores |
| --------------------------------- | :-: | :----------------------: |
| Mantener instructores             |  ✔  |                          |
| Mantener capacitaciones           |  ✔  |                          |
| Escribir en tablas de importación |  ✔  |                          |
| Validar información               |     |            ✔             |
| Crear consultores                 |     |            ✔             |
| Actualizar consultores            |     |            ✔             |
| Crear capacitaciones FEPADE       |     |            ✔             |
| Actualizar capacitaciones FEPADE  |     |            ✔             |
| Desactivar registros              |     |            ✔             |
| Evitar duplicados                 |     |            ✔             |
| Auditoría                         |     |            ✔             |
| Registro de errores               |     |            ✔             |
| Control de estados                |     |            ✔             |

---

# 6. Campos administrados por SAF

SAF administra únicamente los campos que representan información de negocio.

Por ejemplo:

- nombres;
- apellidos;
- DUI;
- estado activo;
- nombre del evento;
- tema;
- institución;
- modalidad;
- fechas;
- horas.

Estos campos pueden insertarse o actualizarse cuando exista un cambio en la información de origen.

---

# 7. Campos administrados por Laravel

Laravel administra todos los campos relacionados con el procesamiento interno.

Entre ellos:

- estado;
- intentos;
- fecha_recepcion;
- fecha_procesamiento;
- mensaje_error;
- id_sincronizacion;
- fecha_ultima_sincronizacion_saf;
- hash_datos_saf;
- fuente.

Estos campos forman parte del mecanismo interno de sincronización y **no deben ser modificados por SAF**.

---

# 8. Tablas funcionales

SAF nunca escribe directamente sobre las tablas funcionales del Sistema de Facilitadores.

Las tablas funcionales son administradas exclusivamente por Laravel.

Actualmente incluyen:

- **tbl_consultor**
- **tbl_consultor_capacitacion_fepade**

Toda modificación sobre estas tablas es realizada por los servicios de sincronización.

---

# 9. Flujo de responsabilidades

El flujo completo de procesamiento es el siguiente:

```text
SAF
 │
 │ Inserta o actualiza datos de negocio
 ▼
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
 │
 ▼
Laravel detecta registros pendientes
 │
 ▼
Validación
 │
 ▼
DTO
 │
 ▼
Servicios de sincronización
 │
 ▼
Auditoría
 │
 ▼
Tablas funcionales
```

Cada etapa tiene una responsabilidad claramente definida y ninguna de ellas invade las funciones de la etapa anterior.

---

# 10. Manejo de errores

Cuando un registro presenta errores de validación o de sincronización:

- el procesamiento del resto de registros continúa;
- el registro se marca con estado **ERROR**;
- se almacena el mensaje correspondiente en la tabla de importación;
- se registra un detalle individual en la auditoría;
- la ejecución general continúa hasta finalizar todos los registros pendientes.

Este comportamiento garantiza que un único error no interrumpa el procesamiento completo de la sincronización.

---

# 11. Escalabilidad

La arquitectura implementada permite incorporar nuevos tipos de información sin modificar el principio de responsabilidad.

En futuras fases podrán agregarse nuevas tablas de importación, tales como:

- experiencias laborales;
- idiomas;
- certificaciones;
- especialidades;
- documentos;
- disponibilidad;
- cualquier otro conjunto de datos administrado por SAF.

Cada nueva integración deberá respetar exactamente el mismo principio:

- SAF administra datos de negocio.
- Laravel administra el procesamiento.

---

# 12. Beneficios de la arquitectura

La separación de responsabilidades proporciona los siguientes beneficios:

- menor acoplamiento entre sistemas;
- mayor facilidad de mantenimiento;
- procesamiento transaccional;
- validación centralizada;
- auditoría completa;
- recuperación sencilla ante errores;
- prevención de modificaciones directas sobre el expediente del consultor;
- posibilidad de ampliar la integración sin afectar el diseño existente.

---

# 13. Conclusión

La integración entre SAF y el Sistema de Facilitadores FEPADE se basa en una arquitectura donde cada sistema mantiene una única responsabilidad claramente definida.

SAF es responsable de proporcionar información de negocio actualizada mediante las tablas de importación.

El Sistema de Facilitadores es responsable de validar, sincronizar, auditar y mantener la integridad de la información almacenada en las tablas funcionales.

Este principio constituye la base técnica sobre la cual deberán desarrollarse todas las futuras integraciones entre ambos sistemas.
