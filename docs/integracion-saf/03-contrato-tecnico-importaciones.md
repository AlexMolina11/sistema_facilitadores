# Contrato técnico de importaciones SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.0.0  
**Estado:** Vigente  
**Última actualización:** Julio 2026

---

# 1. Objetivo

El presente documento establece el contrato técnico que debe cumplir el Sistema de Administración de Facilitadores (SAF) para integrarse con el Sistema de Facilitadores FEPADE.

Este contrato define la estructura de las tablas de importación, las reglas para el intercambio de información, los campos requeridos y las condiciones que deben cumplirse para que la sincronización sea procesada correctamente.

---

# 2. Alcance

El contrato aplica exclusivamente a las siguientes tablas de importación:

- `tbl_saf_instructor_importacion`
- `tbl_saf_capacitacion_importacion`

Estas tablas representan el único punto de entrada autorizado para la información proveniente de SAF.

---

# 3. Reglas generales

La integración deberá cumplir las siguientes reglas:

- SAF únicamente insertará o actualizará información en las tablas de importación.
- SAF no debe modificar tablas funcionales del Sistema de Facilitadores.
- Laravel es el único responsable de sincronizar la información hacia las tablas finales.
- Cada registro debe poseer un identificador externo único.
- Todos los datos deberán cumplir con los tipos y tamaños definidos para cada columna.
- Los cambios serán procesados mediante la tarea programada diaria de sincronización.

---

# 4. Tabla de importación de instructores

## Nombre de la tabla

```text
tbl_saf_instructor_importacion
```

### Propósito

Recibir la información de instructores proveniente de SAF para posteriormente sincronizarla con la tabla `tbl_consultor`.

---

## Campos de negocio

| Campo                 | Tipo    | Obligatorio | Descripción                                      |
| --------------------- | ------- | :---------: | ------------------------------------------------ |
| id_instructor_externo | BIGINT  |     Sí      | Identificador único del instructor en SAF.       |
| nombres               | VARCHAR |     Sí      | Nombres del instructor.                          |
| apellidos             | VARCHAR |     Sí      | Apellidos del instructor.                        |
| dui                   | VARCHAR |     No      | Documento Único de Identidad.                    |
| correo                | VARCHAR |     No      | Correo electrónico.                              |
| telefono              | VARCHAR |     No      | Teléfono de contacto.                            |
| id_entidad            | INT     |     Sí      | Identificador de la entidad a la que pertenece.  |
| activo                | BOOLEAN |     Sí      | Indica si el instructor continúa vigente en SAF. |

---

## Campos de control

Los siguientes campos son administrados exclusivamente por Laravel.

| Campo               | Descripción                           |
| ------------------- | ------------------------------------- |
| estado              | Estado del procesamiento.             |
| intentos            | Número de intentos realizados.        |
| fecha_recepcion     | Fecha de inserción del registro.      |
| fecha_procesamiento | Fecha del último procesamiento.       |
| mensaje_error       | Descripción del error, cuando exista. |

SAF no debe modificar estos campos una vez que el registro haya sido insertado.

---

# 5. Operaciones permitidas

La tabla admite tres tipos de operaciones.

## Alta

Cuando el instructor no existe previamente en el Sistema de Facilitadores, Laravel creará un nuevo consultor.

---

## Actualización

Si el instructor ya existe, Laravel actualizará únicamente los campos administrados por SAF.

---

## Desactivación

Cuando el campo **activo** tenga el valor **false**, Laravel marcará el consultor como inactivo de acuerdo con las reglas del sistema.

---

# 6. Restricciones

Para que un registro sea considerado válido deberá cumplir las siguientes condiciones:

- poseer un identificador externo;
- contener nombres y apellidos;
- indicar una entidad válida;
- especificar el estado activo;
- cumplir con los tamaños definidos para cada campo.

Los registros que no cumplan estas reglas serán rechazados durante el proceso de validación y registrados en la auditoría de errores.

---

# 7. Ejemplo de inserción

```sql
INSERT INTO tbl_saf_instructor_importacion (
    id_instructor_externo,
    nombres,
    apellidos,
    dui,
    correo,
    telefono,
    id_entidad,
    activo
)
VALUES (
    995001,
    'Juan Carlos',
    'Pérez',
    '01234567-8',
    'juan.perez@correo.com',
    '70000001',
    3,
    1
);
```

---

# 8. Ejemplo de actualización

```sql
UPDATE tbl_saf_instructor_importacion
SET
    correo = 'nuevo.correo@correo.com',
    telefono = '71111111',
    estado = 'PENDIENTE'
WHERE id_instructor_externo = 995001;
```

La actualización del estado a **PENDIENTE** permitirá que Laravel procese nuevamente el registro durante la siguiente ejecución de sincronización.

---

# 9. Resumen

La tabla `tbl_saf_instructor_importacion` constituye el mecanismo oficial mediante el cual SAF comunica las altas, modificaciones y desactivaciones de instructores. Laravel valida cada registro, aplica las reglas de negocio correspondientes y sincroniza la información hacia la tabla `tbl_consultor`, manteniendo en todo momento la integridad y trazabilidad del proceso.

> **Continúa en la Parte 2**, donde se documenta el contrato técnico de `tbl_saf_capacitacion_importacion`, incluyendo su estructura, reglas de validación y ejemplos de operaciones.

# 10. Tabla de importación de capacitaciones

## Nombre de la tabla

```text
tbl_saf_capacitacion_importacion
```

### Propósito

Recibir la información de las capacitaciones impartidas por los instructores registrados en SAF para posteriormente sincronizarla con la tabla `tbl_consultor_capacitacion_fepade`.

Cada registro representa una capacitación individual asociada a un consultor.

---

## Campos de negocio

| Campo                 | Tipo    | Obligatorio | Descripción                                                |
| --------------------- | ------- | :---------: | ---------------------------------------------------------- |
| id_instructor_externo | BIGINT  |     Sí      | Identificador único del instructor en SAF.                 |
| codigo_evento_externo | VARCHAR |     Sí      | Código único del evento de capacitación.                   |
| nombre_evento         | VARCHAR |     Sí      | Nombre oficial de la capacitación.                         |
| tema                  | VARCHAR |     No      | Tema principal desarrollado durante la capacitación.       |
| institucion           | VARCHAR |     No      | Institución organizadora o responsable de la capacitación. |
| modalidad             | VARCHAR |     No      | Modalidad de impartición (Presencial, Virtual o Híbrida).  |
| fecha_inicio          | DATE    |     Sí      | Fecha de inicio de la capacitación.                        |
| fecha_fin             | DATE    |     Sí      | Fecha de finalización de la capacitación.                  |
| horas                 | DECIMAL |     Sí      | Cantidad de horas de formación.                            |
| activo                | BOOLEAN |     Sí      | Indica si la capacitación continúa vigente en SAF.         |

---

## Campos de control

Los siguientes campos son administrados exclusivamente por Laravel y no deben ser modificados por SAF.

| Campo               | Descripción                                                       |
| ------------------- | ----------------------------------------------------------------- |
| estado              | Estado del procesamiento.                                         |
| intentos            | Número de intentos realizados.                                    |
| fecha_recepcion     | Fecha de inserción del registro.                                  |
| fecha_procesamiento | Fecha del último procesamiento.                                   |
| mensaje_error       | Mensaje generado durante el procesamiento cuando exista un error. |

---

# 11. Operaciones permitidas

La tabla permite realizar las siguientes operaciones.

## Alta

Cuando una capacitación no exista previamente para el consultor correspondiente, Laravel creará un nuevo registro en `tbl_consultor_capacitacion_fepade`.

---

## Actualización

Si la capacitación ya existe, Laravel actualizará únicamente los campos administrados por SAF.

Entre ellos:

- nombre del evento;
- tema;
- institución;
- modalidad;
- fechas;
- horas;
- estado activo.

---

## Desactivación

Cuando el campo **activo** tenga el valor **false**, Laravel marcará la capacitación como inactiva dentro del Sistema de Facilitadores, conservando el historial para fines de auditoría.

---

# 12. Restricciones

Antes de procesar una capacitación se verificará que:

- exista un instructor asociado;
- el código del evento sea único;
- el nombre del evento esté informado;
- la fecha de inicio sea válida;
- la fecha de finalización sea válida;
- la fecha de inicio no sea posterior a la fecha final;
- las horas sean mayores que cero;
- el estado activo esté definido.

Si alguna validación falla, el registro será rechazado y se documentará en la auditoría de errores.

---

# 13. Ejemplo de inserción

```sql
INSERT INTO tbl_saf_capacitacion_importacion (
    id_instructor_externo,
    codigo_evento_externo,
    nombre_evento,
    tema,
    institucion,
    modalidad,
    fecha_inicio,
    fecha_fin,
    horas,
    activo
)
VALUES (
    995001,
    'CUR-2026-001',
    'Metodologías Activas de Aprendizaje',
    'Aprendizaje Basado en Problemas',
    'FEPADE',
    'Presencial',
    '2026-06-10',
    '2026-06-12',
    24,
    1
);
```

---

# 14. Ejemplo de actualización

```sql
UPDATE tbl_saf_capacitacion_importacion
SET
    modalidad = 'Virtual',
    horas = 30,
    estado = 'PENDIENTE'
WHERE codigo_evento_externo = 'CUR-2026-001';
```

Al establecer el estado en **PENDIENTE**, Laravel volverá a evaluar el registro durante la siguiente ejecución del proceso de sincronización.

---

# 15. Relación con el consultor

Toda capacitación debe estar asociada a un instructor existente.

La asociación se realiza utilizando el campo:

```text
id_instructor_externo
```

Durante el procesamiento, Laravel localizará al consultor correspondiente y utilizará su identificador interno para registrar la capacitación en el expediente.

Si el instructor no existe, la capacitación no será procesada y se registrará el error correspondiente en la auditoría.

---

# 16. Prevención de duplicados

Para evitar registros duplicados, Laravel utiliza como identificador principal el campo:

```text
codigo_evento_externo
```

Cuando el código ya existe para el mismo consultor, el registro será tratado como una actualización y no como una nueva inserción.

---

# 17. Resumen

La tabla `tbl_saf_capacitacion_importacion` constituye el mecanismo oficial para el intercambio de información relacionada con las capacitaciones impartidas por los instructores registrados en SAF.

Cada registro recibido es validado, asociado al consultor correspondiente y sincronizado con la tabla `tbl_consultor_capacitacion_fepade`, garantizando la integridad de la información y la trazabilidad de todas las operaciones realizadas.

> **Continúa en la Parte 3**, donde se documentan los estados del procesamiento, las reglas de sincronización, la auditoría, el manejo de errores y las buenas prácticas para la integración.

# 18. Estados del procesamiento

Durante el proceso de sincronización, Laravel administra el estado de cada registro recibido desde SAF.

| Estado     | Descripción                                                          |
| ---------- | -------------------------------------------------------------------- |
| PENDIENTE  | Registro disponible para ser procesado.                              |
| EN_PROCESO | Registro reservado por Laravel durante la sincronización.            |
| PROCESADO  | Registro sincronizado correctamente.                                 |
| ERROR      | El registro presentó errores durante la validación o sincronización. |

Estos estados son utilizados únicamente por el Sistema de Facilitadores y no deben ser modificados por SAF.

---

# 19. Flujo de sincronización

Cada ejecución sigue el siguiente proceso:

1. Detectar registros con estado **PENDIENTE**.
2. Cambiar el estado a **EN_PROCESO**.
3. Validar la información recibida.
4. Sincronizar los datos con las tablas funcionales.
5. Registrar la auditoría.
6. Actualizar el estado final del registro.
7. Continuar con el siguiente registro hasta finalizar el lote.

Este flujo garantiza que cada registro sea procesado una única vez por ejecución.

---

# 20. Validaciones aplicadas

Antes de sincronizar un registro, Laravel ejecuta una serie de validaciones para asegurar la calidad de la información.

Entre las principales validaciones se encuentran:

- existencia del instructor;
- obligatoriedad de los campos requeridos;
- formatos válidos para fechas;
- horas mayores que cero;
- consistencia entre fecha de inicio y fecha de finalización;
- identificadores externos válidos;
- prevención de registros duplicados.

Cuando una validación falla, el registro se marca como **ERROR** y no es sincronizado.

---

# 21. Auditoría del proceso

Cada ejecución genera un registro en la tabla:

```text
tbl_sincronizacion_saf
```

La auditoría almacena información como:

- fecha y hora de inicio;
- fecha y hora de finalización;
- registros detectados;
- registros procesados;
- registros exitosos;
- registros con error;
- consultores creados;
- consultores actualizados;
- capacitaciones creadas;
- capacitaciones actualizadas;
- capacitaciones desactivadas;
- observaciones de la ejecución.

Esta información permite conocer el resultado de cada proceso de sincronización.

---

# 22. Auditoría de errores

Cuando un registro presenta errores, Laravel genera un detalle en la tabla:

```text
tbl_sincronizacion_saf_error
```

Cada incidencia almacena información como:

- tipo de registro;
- operación realizada;
- identificador externo;
- código del error;
- mensaje descriptivo;
- detalle técnico;
- archivo;
- línea;
- fecha del error.

Esta información facilita el diagnóstico y la corrección de incidencias.

---

# 23. Ejemplos de errores

| Situación                         | Resultado            |
| --------------------------------- | -------------------- |
| Instructor inexistente            | Registro rechazado.  |
| Código de evento vacío            | Error de validación. |
| Fecha final menor que la inicial  | Error de validación. |
| Horas iguales o menores a cero    | Error de validación. |
| Campo obligatorio sin información | Error de validación. |

En todos los casos el procesamiento continúa con los registros restantes.

---

# 24. Recomendaciones para SAF

Para garantizar una sincronización correcta se recomienda:

- utilizar identificadores externos permanentes;
- no reutilizar códigos de eventos;
- enviar información completa en cada actualización;
- respetar los tipos y longitudes definidos para cada campo;
- actualizar únicamente registros existentes cuando corresponda;
- evitar modificaciones manuales sobre los campos de control.

---

# 25. Buenas prácticas

Durante el desarrollo y mantenimiento de la integración se recomienda:

- mantener un ambiente de pruebas antes de realizar cambios en producción;
- validar nuevas estructuras antes de desplegarlas;
- documentar cualquier modificación al contrato técnico;
- conservar la compatibilidad con versiones anteriores cuando sea posible;
- monitorear periódicamente la auditoría de sincronización para detectar incidencias.

---

# 26. Control de versiones

Toda modificación al presente contrato deberá registrarse mediante una nueva versión del documento.

Los cambios pueden incluir:

- incorporación de nuevos campos;
- nuevas tablas de importación;
- modificación de reglas de validación;
- cambios en el flujo de sincronización;
- mejoras en los procesos de auditoría.

---

# 27. Conclusión

El presente contrato técnico establece las reglas oficiales para el intercambio de información entre SAF y el Sistema de Facilitadores FEPADE.

La utilización de tablas intermedias, junto con un proceso de validación, sincronización y auditoría centralizado en Laravel, garantiza una integración segura, controlada y escalable. Este documento constituye la referencia técnica para el mantenimiento, evolución y futuras ampliaciones del proceso de integración entre ambos sistemas.
