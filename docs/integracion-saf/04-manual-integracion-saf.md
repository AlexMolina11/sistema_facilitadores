# Manual de Integración SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.0.0  
**Estado:** Vigente  
**Última actualización:** Julio 2026

---

# 1. Introducción

El presente manual describe el funcionamiento interno de la integración entre el Sistema de Administración de Facilitadores (SAF) y el Sistema de Facilitadores FEPADE.

Su propósito es servir como guía para desarrolladores y administradores del sistema, facilitando la comprensión de la arquitectura implementada, el flujo de procesamiento de la información y las tareas de mantenimiento relacionadas con la sincronización de datos.

A diferencia del contrato técnico de importaciones, este documento explica cómo está construida la solución dentro del proyecto Laravel y cómo debe mantenerse o ampliarse en futuras versiones.

---

# 2. Objetivo de la integración

La integración tiene como finalidad mantener actualizado el expediente de los consultores utilizando la información registrada en SAF.

Actualmente se sincronizan dos tipos de información:

- Instructores.
- Capacitaciones FEPADE.

Toda la información recibida desde SAF es validada antes de incorporarse a las tablas funcionales del Sistema de Facilitadores.

---

# 3. Arquitectura general

La integración fue diseñada utilizando una arquitectura desacoplada basada en tablas intermedias.

```text
                SAF (SQL Server)
                       │
                       │
        Inserción / actualización
                       │
                       ▼
     tbl_saf_instructor_importacion
     tbl_saf_capacitacion_importacion
                       │
                       ▼
          Comando de sincronización
                       │
                       ▼
              Processors Laravel
                       │
                       ▼
               DTO de negocio
                       │
                       ▼
          Servicios de sincronización
                       │
                       ▼
         Tablas funcionales FEPADE
```

Este diseño evita que SAF modifique directamente las tablas funcionales del sistema y concentra toda la lógica de negocio dentro de Laravel.

---

# 4. Componentes principales

La integración está formada por varios componentes especializados, cada uno con una responsabilidad claramente definida.

| Componente            | Responsabilidad                                         |
| --------------------- | ------------------------------------------------------- |
| Tablas de importación | Recibir la información enviada por SAF.                 |
| Command               | Iniciar el proceso completo de sincronización.          |
| Processors            | Procesar cada tipo de información recibida.             |
| DTO                   | Validar y normalizar los datos antes de sincronizarlos. |
| Sync Services         | Crear, actualizar o desactivar registros funcionales.   |
| Auditoría             | Registrar cada ejecución y sus resultados.              |

Esta separación permite mantener un código más organizado, reutilizable y fácil de mantener.

---

# 5. Flujo general de sincronización

Cada ejecución sigue siempre la misma secuencia de procesamiento.

### Paso 1

SAF inserta o actualiza registros en las tablas de importación.

---

### Paso 2

Laravel ejecuta el comando programado de sincronización.

---

### Paso 3

El sistema identifica los registros pendientes de procesamiento.

---

### Paso 4

Cada registro es reservado cambiando su estado a **EN_PROCESO**, evitando que sea procesado simultáneamente por otra ejecución.

---

### Paso 5

Se construye el DTO correspondiente y se ejecutan todas las validaciones de negocio.

---

### Paso 6

Si la información es válida, el servicio de sincronización determina si debe:

- crear un registro;
- actualizar un registro existente;
- desactivar un registro.

---

### Paso 7

Se registra el resultado en la auditoría y el estado del registro cambia a **PROCESADO** o **ERROR**, según corresponda.

---

# 6. Componentes del proyecto

La implementación se encuentra organizada en componentes independientes para facilitar su mantenimiento.

## Tablas de importación

Son el punto de entrada oficial de la información enviada por SAF.

Actualmente existen dos tablas:

- `tbl_saf_instructor_importacion`
- `tbl_saf_capacitacion_importacion`

---

## DTO

Los DTO (Data Transfer Objects) representan la información de negocio recibida desde SAF.

Sus responsabilidades son:

- validar los datos;
- normalizar formatos;
- garantizar que únicamente información válida llegue a los servicios de sincronización.

---

## Processors

Los Processors coordinan el procesamiento de cada registro.

Entre sus responsabilidades se encuentran:

- localizar registros pendientes;
- reservar registros;
- construir el DTO;
- ejecutar validaciones;
- invocar el servicio correspondiente;
- registrar auditoría;
- actualizar el estado del procesamiento.

Cada tipo de importación posee su propio Processor.

---

## Servicios de sincronización

Los servicios contienen la lógica de negocio encargada de modificar las tablas funcionales.

Sus funciones principales son:

- crear registros;
- actualizar registros existentes;
- desactivar registros;
- evitar duplicados;
- mantener la integridad de la información.

Toda modificación sobre el expediente del consultor se realiza exclusivamente desde estos servicios.

---

# 7. Beneficios de la arquitectura

La arquitectura implementada proporciona múltiples ventajas para el mantenimiento del sistema.

Entre las principales se encuentran:

- desacoplamiento entre SAF y Laravel;
- reutilización de componentes;
- validaciones centralizadas;
- procesamiento transaccional;
- auditoría completa;
- facilidad para incorporar nuevos tipos de importación;
- menor riesgo de inconsistencias en la información.

Gracias a esta estructura, futuras integraciones podrán desarrollarse reutilizando la misma estrategia implementada para instructores y capacitaciones.

---

# 8. Conclusión de la Parte 1

En esta primera parte se presentó la arquitectura general de la integración y los componentes que intervienen en el procesamiento de la información.

En la siguiente sección se describirá el funcionamiento detallado del flujo de sincronización de instructores y capacitaciones, explicando el recorrido completo que sigue cada registro desde su recepción hasta su incorporación en el expediente del consultor.

# 9. Flujo de sincronización de instructores

El proceso de sincronización de instructores inicia cuando SAF inserta o actualiza registros en la tabla `tbl_saf_instructor_importacion`.

A partir de ese momento, Laravel es el responsable de ejecutar todo el flujo de procesamiento hasta reflejar los cambios en el expediente del consultor.

El recorrido completo de un registro es el siguiente:

```text
SAF
 │
 ▼
tbl_saf_instructor_importacion
 │
 ▼
InstructorImportProcessor
 │
 ▼
InstructorSafData (DTO)
 │
 ▼
SafInstructorSyncService
 │
 ▼
tbl_consultor
 │
 ▼
Auditoría
```

Cada uno de estos componentes cumple una responsabilidad específica.

---

# 10. Recepción del registro

SAF inserta o actualiza la información del instructor dentro de la tabla de importación.

Durante esta etapa no se realiza ninguna validación de negocio.

La tabla únicamente actúa como una bandeja de recepción para la información enviada por el sistema origen.

---

# 11. Detección de registros pendientes

Cuando se ejecuta el comando de sincronización, Laravel busca todos los registros cuyo estado sea **PENDIENTE**.

Cada registro localizado es reservado inmediatamente cambiando su estado a **EN_PROCESO**.

Este mecanismo evita que dos ejecuciones procesen simultáneamente el mismo registro.

---

# 12. Construcción del DTO

Una vez reservado el registro, el Processor construye un objeto `InstructorSafData`.

El DTO tiene como objetivo representar al instructor utilizando un modelo de negocio independiente de la base de datos.

Además, centraliza las validaciones y normalizaciones necesarias antes de iniciar la sincronización.

Entre las tareas realizadas por el DTO se encuentran:

- validar campos obligatorios;
- normalizar cadenas de texto;
- convertir tipos de datos;
- normalizar el estado del registro;
- preparar la información para los servicios de sincronización.

De esta manera, los servicios trabajan únicamente con información previamente validada.

---

# 13. Sincronización del instructor

Cuando el DTO es válido, el Processor delega el proceso al servicio `SafInstructorSyncService`.

Este servicio es el responsable de determinar qué acción debe ejecutarse.

Las posibles acciones son:

- crear un nuevo consultor;
- actualizar un consultor existente;
- desactivar un consultor.

Toda la lógica de negocio relacionada con instructores se concentra en este servicio.

---

# 14. Creación de un consultor

Si el instructor aún no existe dentro del Sistema de Facilitadores, el servicio crea un nuevo registro en la tabla `tbl_consultor`.

Durante esta operación únicamente se actualizan los campos administrados por SAF.

Los campos propios del Sistema de Facilitadores permanecen bajo el control de Laravel.

---

# 15. Actualización de un consultor

Si el instructor ya existe, el servicio actualiza la información enviada por SAF.

Entre los datos sincronizados pueden encontrarse:

- nombres;
- apellidos;
- correo electrónico;
- teléfono;
- entidad;
- estado activo.

La actualización conserva el resto de información administrada por el Sistema de Facilitadores.

---

# 16. Desactivación

Cuando SAF indica que un instructor ya no se encuentra activo, Laravel actualiza el consultor correspondiente aplicando las reglas definidas por el sistema.

La información histórica del expediente no es eliminada.

Únicamente se modifica el estado correspondiente.

---

# 17. Registro de auditoría

Finalizada la sincronización, Laravel registra el resultado de la operación.

Dependiendo del resultado:

- el registro cambia a **PROCESADO**;
- o cambia a **ERROR** cuando ocurre alguna incidencia.

Adicionalmente, toda la ejecución queda documentada en las tablas de auditoría para facilitar el seguimiento del proceso.

---

# 18. Flujo de sincronización de capacitaciones

El procesamiento de capacitaciones utiliza la misma arquitectura implementada para instructores.

El recorrido general es el siguiente:

```text
SAF
 │
 ▼
tbl_saf_capacitacion_importacion
 │
 ▼
CapacitacionImportProcessor
 │
 ▼
CapacitacionSafData (DTO)
 │
 ▼
SafCapacitacionSyncService
 │
 ▼
tbl_consultor_capacitacion_fepade
 │
 ▼
Auditoría
```

Aunque el flujo es similar, las reglas de negocio aplicadas corresponden específicamente a las capacitaciones.

---

# 19. Procesamiento de una capacitación

Después de localizar un registro pendiente, Laravel construye el DTO `CapacitacionSafData`.

Posteriormente valida información como:

- instructor asociado;
- código del evento;
- nombre del evento;
- fechas;
- horas;
- estado activo.

Si la validación es satisfactoria, el servicio correspondiente decide si debe crear, actualizar o desactivar la capacitación.

---

# 20. Relación entre instructores y capacitaciones

Las capacitaciones no pueden procesarse de forma independiente.

Antes de sincronizar una capacitación, Laravel verifica que el instructor asociado exista dentro del Sistema de Facilitadores.

La relación se establece mediante el identificador externo del instructor.

Si el consultor no existe, la capacitación se marca como **ERROR** y la incidencia queda registrada en la auditoría.

---

# 21. Conclusión de la Parte 2

En esta sección se explicó el recorrido completo que siguen los registros de instructores y capacitaciones desde su recepción hasta su sincronización con las tablas funcionales del Sistema de Facilitadores.

En la siguiente parte se describirá el funcionamiento interno de la auditoría, el manejo de errores, el proceso automático de ejecución y las recomendaciones para el mantenimiento de la integración.

# 22. Auditoría de la sincronización

Uno de los objetivos principales de la integración es garantizar la trazabilidad completa de cada ejecución.

Para ello, el Sistema de Facilitadores registra información tanto del proceso general como de cada error individual detectado durante la sincronización.

Esta información permite conocer qué registros fueron procesados, cuáles presentaron errores y cuál fue la causa de cada incidencia.

---

# 23. Auditoría de ejecución

Cada vez que se ejecuta el comando de sincronización se crea un registro en la tabla:

```text
tbl_sincronizacion_saf
```

Esta tabla almacena un resumen de toda la ejecución.

Entre la información registrada se encuentra:

- fecha de inicio;
- fecha de finalización;
- duración del proceso;
- cantidad de registros detectados;
- cantidad de registros procesados;
- registros exitosos;
- registros con error;
- consultores creados;
- consultores actualizados;
- capacitaciones creadas;
- capacitaciones actualizadas;
- capacitaciones desactivadas;
- resultado general del proceso.

Gracias a esta auditoría es posible revisar el comportamiento histórico de todas las sincronizaciones ejecutadas.

---

# 24. Auditoría de errores

Cuando un registro no puede procesarse correctamente, Laravel crea un registro en:

```text
tbl_sincronizacion_saf_error
```

Cada error queda asociado a la ejecución que lo generó.

La información registrada incluye:

- tipo de registro;
- tipo de operación;
- identificador externo;
- identificador interno (cuando exista);
- código del error;
- mensaje descriptivo;
- detalle técnico;
- archivo;
- línea;
- fecha y hora del error.

Esta información facilita el análisis de incidencias sin necesidad de revisar directamente los archivos de registro del servidor.

---

# 25. Manejo de errores

La integración fue diseñada para que un error individual no detenga el procesamiento completo.

Cuando un registro presenta una incidencia, el flujo es el siguiente:

1. Se detecta el error.
2. Se registra el detalle en la auditoría.
3. El registro cambia al estado **ERROR**.
4. Se continúa con el siguiente registro pendiente.

De esta forma, una ejecución puede finalizar correctamente aunque algunos registros no hayan podido sincronizarse.

---

# 26. Ejecución automática

El procesamiento de las importaciones se realiza mediante una tarea programada de Laravel.

El comando responsable es:

```bash
php artisan saf:procesar-importaciones
```

Este comando ejecuta de forma secuencial el procesamiento de instructores y capacitaciones.

La programación actual establece una ejecución diaria a las **05:00 a. m.**, horario de El Salvador.

El flujo ejecutado por el comando es el siguiente:

```text
Inicio
   │
   ▼
Procesar instructores
   │
   ▼
Procesar capacitaciones
   │
   ▼
Cerrar auditoría
   │
   ▼
Fin
```

Esta programación garantiza que la información enviada por SAF sea incorporada al Sistema de Facilitadores de manera automática y periódica.

---

# 27. Mantenimiento de la integración

La arquitectura implementada facilita la incorporación de nuevos tipos de información provenientes de SAF.

Para agregar una nueva importación se recomienda seguir el mismo patrón utilizado para instructores y capacitaciones.

El proceso general consiste en:

1. Crear la tabla de importación.
2. Crear el DTO correspondiente.
3. Implementar el Processor.
4. Implementar el servicio de sincronización.
5. Registrar la auditoría.
6. Incorporar el procesamiento al comando principal.
7. Realizar pruebas funcionales antes del despliegue.

Mantener esta estructura garantiza consistencia en el desarrollo y simplifica el mantenimiento futuro.

---

# 28. Recomendaciones para mantenimiento

Durante la evolución del sistema se recomienda:

- mantener la separación entre datos de negocio y lógica de sincronización;
- evitar modificaciones directas sobre las tablas funcionales desde sistemas externos;
- documentar cualquier cambio realizado al contrato técnico;
- realizar pruebas completas después de cada modificación;
- revisar periódicamente la auditoría para detectar errores recurrentes;
- conservar la compatibilidad con las estructuras existentes cuando sea posible.

Estas prácticas ayudan a preservar la estabilidad de la integración a largo plazo.

---

# 29. Solución de problemas frecuentes

| Situación                                    | Posible causa                                            | Acción recomendada                                                |
| -------------------------------------------- | -------------------------------------------------------- | ----------------------------------------------------------------- |
| Registros permanecen en estado **PENDIENTE** | El comando no se ha ejecutado                            | Verificar el Scheduler y el Cron del servidor.                    |
| Registros en estado **ERROR**                | Validación o sincronización fallida                      | Revisar `tbl_sincronizacion_saf_error` y corregir la información. |
| Una capacitación no aparece en el expediente | El consultor asociado no existe                          | Confirmar que el instructor haya sido sincronizado previamente.   |
| Cambios realizados en SAF no se reflejan     | El registro no fue marcado nuevamente como **PENDIENTE** | Actualizar el estado para permitir un nuevo procesamiento.        |

---

# 30. Checklist antes de producción

Antes de habilitar una nueva versión de la integración se recomienda verificar los siguientes puntos:

- Migraciones ejecutadas correctamente.
- Tablas de importación disponibles.
- Permisos del usuario de SAF validados.
- Scheduler y Cron configurados.
- Auditoría operativa.
- Procesamiento de instructores validado.
- Procesamiento de capacitaciones validado.
- Escenarios de creación, actualización y desactivación probados.
- Escenarios de error verificados.
- Documentación técnica actualizada.

---

# 31. Conclusión

La integración entre SAF y el Sistema de Facilitadores FEPADE fue diseñada siguiendo una arquitectura modular, desacoplada y orientada a la trazabilidad.

La utilización de tablas intermedias, DTO, Processors, servicios de sincronización y mecanismos de auditoría permite mantener la integridad de la información, reducir el acoplamiento entre sistemas y facilitar el mantenimiento de la solución.

Este manual proporciona la información necesaria para comprender el funcionamiento interno de la integración, realizar tareas de soporte, incorporar nuevas funcionalidades y garantizar la continuidad operativa del proceso de sincronización.

# 32. Guía para incorporar un nuevo tipo de importación

La arquitectura de integración fue diseñada para ser reutilizable. Si en el futuro SAF necesita sincronizar nuevos tipos de información (por ejemplo, idiomas, experiencias laborales o certificaciones), se recomienda seguir el mismo patrón utilizado para instructores y capacitaciones.

El flujo de desarrollo recomendado es el siguiente:

1. Crear la tabla de importación.
2. Crear el modelo Eloquent correspondiente.
3. Crear el DTO de negocio.
4. Crear el Processor.
5. Crear el Servicio de Sincronización.
6. Registrar la auditoría.
7. Agregar el procesamiento al comando principal.
8. Realizar pruebas funcionales.
9. Actualizar la documentación técnica.

Mantener esta estructura garantiza uniformidad en todo el proceso de integración.

---

# 33. Estructura recomendada del proyecto

Los componentes de la integración deben mantenerse organizados según la siguiente estructura:

```text
app/
└── Modules/
    └── Fac/
        ├── Console/
        │   └── Commands/
        │       └── ProcesarImportacionesSafCommand.php
        │
        ├── DTO/
        │   ├── InstructorSafData.php
        │   └── CapacitacionSafData.php
        │
        ├── Models/
        │   ├── SafInstructorImportacion.php
        │   ├── SafCapacitacionImportacion.php
        │   ├── SincronizacionSaf.php
        │   └── SincronizacionSafError.php
        │
        ├── Processors/
        │   ├── InstructorImportProcessor.php
        │   └── CapacitacionImportProcessor.php
        │
        └── Services/
            ├── SafInstructorSyncService.php
            ├── SafCapacitacionSyncService.php
            └── SafAuditService.php
```

Esta organización facilita la localización del código y mantiene una clara separación de responsabilidades.

---

# 34. Lista de verificación para desarrolladores

Antes de desplegar cualquier cambio relacionado con la integración, se recomienda verificar lo siguiente:

- Las migraciones fueron ejecutadas correctamente.
- Las tablas de importación existen en la base de datos.
- El usuario de SAF mantiene los permisos necesarios.
- El comando de sincronización ejecuta sin errores.
- Los registros cambian correctamente entre los estados **PENDIENTE**, **EN_PROCESO**, **PROCESADO** y **ERROR**.
- La auditoría registra correctamente las ejecuciones.
- Los errores quedan registrados en `tbl_sincronizacion_saf_error`.
- Se probaron escenarios de creación, actualización y desactivación.
- La documentación fue actualizada.

---

# 35. Recomendaciones para futuras mejoras

La arquitectura actual permite ampliar la integración sin modificar los componentes existentes.

Las futuras implementaciones deberían seguir los mismos principios de diseño:

- utilizar tablas intermedias como único punto de entrada;
- mantener DTO independientes para cada tipo de información;
- centralizar la lógica de negocio en los servicios de sincronización;
- registrar toda ejecución en la auditoría;
- documentar cualquier cambio en el contrato técnico antes de su despliegue.

Este enfoque reduce el acoplamiento entre sistemas y facilita la evolución de la plataforma.

---

# 36. Glosario

| Término                  | Descripción                                                                              |
| ------------------------ | ---------------------------------------------------------------------------------------- |
| **SAF**                  | Sistema de Administración de Facilitadores, origen de la información.                    |
| **DTO**                  | Data Transfer Object utilizado para validar y normalizar los datos antes de procesarlos. |
| **Processor**            | Componente encargado de coordinar el procesamiento de los registros de importación.      |
| **Sync Service**         | Servicio que aplica la lógica de negocio para crear, actualizar o desactivar registros.  |
| **Tabla de importación** | Tabla intermedia donde SAF deposita la información para su posterior procesamiento.      |
| **Auditoría**            | Registro histórico de las ejecuciones y errores ocurridos durante la sincronización.     |

---

# 37. Conclusión general

La integración entre SAF y el Sistema de Facilitadores FEPADE implementa una arquitectura desacoplada basada en tablas intermedias, validaciones centralizadas, servicios especializados y mecanismos de auditoría que garantizan la integridad y trazabilidad de la información.

La solución fue diseñada para facilitar su mantenimiento y permitir el crecimiento futuro mediante la incorporación de nuevos procesos de sincronización, reutilizando la misma arquitectura y los mismos principios de desarrollo.

Este manual, junto con el contrato técnico y la documentación de arquitectura, constituye la referencia oficial para el desarrollo, mantenimiento y evolución de la integración entre ambos sistemas.
