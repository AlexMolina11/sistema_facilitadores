# Contrato técnico de importaciones SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.1.0  
**Estado:** Vigente  
**Última actualización:** Agosto 2026

---

# 1. Objetivo

Este documento establece el contrato técnico que SAF debe cumplir para enviar instructores y capacitaciones al Sistema de Facilitadores FEPADE.

Define:

- tablas autorizadas;
- campos de negocio;
- tipos y longitudes;
- reglas de recepción;
- traducciones necesarias;
- campos internos que SAF no debe administrar;
- comportamiento esperado ante actualizaciones y errores.

---

# 2. Punto de entrada autorizado

SAF únicamente debe escribir en:

```text
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
```

SAF no debe escribir directamente en:

```text
tbl_consultor
tbl_consultor_documento
tbl_consultor_email
tbl_consultor_capacitacion_fepade
```

---

# 3. Regla de historial de recepciones

Cada envío de SAF debe generar una nueva fila de staging.

No se debe actualizar una fila histórica ya procesada para representar un cambio posterior.

Ejemplo correcto:

```text
recepción 1 → instructor 500 → CREADO
recepción 2 → instructor 500 → ACTUALIZADO
recepción 3 → instructor 500 → SIN_CAMBIOS
```

Las tablas staging no tienen restricción única por instructor/evento porque deben conservar todas las recepciones.

---

# 4. Tabla `tbl_saf_instructor_importacion`

## 4.1. Campos de negocio

| Campo | Tipo acordado | Requerido | Descripción |
|---|---|:---:|---|
| `id_instructor` | int(10) | Sí | Identificador único del instructor en SAF |
| `id_entidad` | int(10) | Sí | Identificador de entidad SAF |
| `nombres` | varchar(50) | Sí | Nombres del instructor |
| `apellidos` | varchar(50) | Sí | Apellidos del instructor |
| `tipo_identificacion` | int(10) | Condicional | Código de tipo de identificación SAF |
| `numero_identificacion` | varchar(50) | Condicional | Número del documento |
| `correo_saf` | varchar(50) | No | Correo principal proveniente de SAF |
| `activo` | int / boolean | Sí | Estado ya traducido para Facilitadores |

El campo anterior `dui` ya no forma parte del contrato. Fue sustituido por:

```text
numero_identificacion
```

---

# 5. Tipos de identificación SAF

SAF enviará:

| Código SAF | Documento |
|---:|---|
| 2 | Número de Identificación Tributaria (NIT) |
| 4 | Pasaporte |
| 5 | Licencia de conducir |
| 7 | DUI |

Cualquier otro código será rechazado por validación.

---

# 6. Traducción al catálogo de Facilitadores

Los IDs de SAF no coinciden necesariamente con `tbl_tipo_documento`.

Laravel realiza la traducción:

| SAF | Documento | Facilitadores |
|---:|---|---:|
| 2 | NIT | 1 |
| 4 | Pasaporte | 4 |
| 5 | Licencia de conducir | 6 |
| 7 | DUI | 2 |

La Licencia de Conducir se encuentra registrada en `tbl_tipo_documento` con ID 6.

---

# 7. Destino del documento

Laravel guarda:

```text
id_consultor       = consultor sincronizado
id_tipo_documento  = código traducido
numero             = numero_identificacion
activo             = 1
```

en:

```text
tbl_consultor_documento
```

SAF no debe escribir directamente en esta tabla.

---

# 8. Destino del correo SAF

`correo_saf` se guarda en:

```text
tbl_consultor_email
```

como:

```text
principal = 1
activo    = 1
```

Si existe otro correo principal, Laravel lo conserva como secundario.

---

# 9. Conversión de `Inactivo`

El sistema origen maneja el campo `Inactivo`, mientras la tabla de importación utiliza `activo`.

La conversión obligatoria es:

```text
Inactivo = 0 → activo = 1
Inactivo = 1 → activo = 0
```

Ejemplo SQL:

```sql
CASE
    WHEN Inactivo = 1 THEN 0
    ELSE 1
END AS activo
```

Laravel utiliza `activo` para actualizar tanto `activo` como `vigente` del consultor.

---

# 10. Ejemplo de recepción de instructor

```sql
INSERT INTO tbl_saf_instructor_importacion
(
    id_instructor,
    id_entidad,
    nombres,
    apellidos,
    tipo_identificacion,
    numero_identificacion,
    correo_saf,
    activo,
    estado,
    intentos,
    fecha_recepcion,
    created_at,
    updated_at
)
VALUES
(
    12345,
    1,
    'JUAN',
    'PEREZ',
    7,
    '01234567-8',
    'juan.perez@ejemplo.com',
    1,
    'PENDIENTE',
    0,
    NOW(),
    NOW(),
    NOW()
);
```

Los campos técnicos incluidos en el ejemplo únicamente se inicializan para recepción. Después del INSERT, Laravel es su único propietario.

Si la configuración de base de datos define valores por defecto para estos campos, SAF debe preferir omitirlos.

---

# 11. Actualización de un instructor

Una actualización no debe modificar la fila anterior.

SAF debe insertar una nueva recepción con el mismo `id_instructor` y los datos actualizados.

Laravel determinará si el resultado es:

```text
ACTUALIZADO
SIN_CAMBIOS
ERROR
```

---

# 12. Tabla `tbl_saf_capacitacion_importacion`

## 12.1. Campos de negocio

| Campo | Tipo acordado | Requerido | Descripción |
|---|---|:---:|---|
| `id_instructor` | int(10) | Sí | Instructor SAF relacionado |
| `programa_curso_id` | int(10) | Sí | Identificador del programa/curso |
| `codigo_evento` | varchar(50) | Sí | Código del evento |
| `curso_nombre` | varchar(250) | Sí | Nombre del curso |
| `fecha_inicio` | date | No | Fecha de inicio |
| `fecha_fin` | date | No | Fecha de finalización |
| `estado_curso_nombre` | varchar(30) | No | Estado de negocio del curso |
| `no_horas_real` | int(10) | No | Número real de horas |
| `modalidad` | varchar(50) | No | Modalidad |
| `tipo_evento_nombre` | varchar(30) | No | Tipo de evento |
| `cliente` | varchar(300) | No | Cliente al que se impartió |
| `encuesta_id` | int(10) | No | Identificador de encuesta |
| `encuesta_nombre` | varchar(100) | No | Nombre de la encuesta |
| `promedio_encuesta` | decimal(10,2) | No | Promedio obtenido |
| `fecha_evaluacion` | datetime | No | Fecha/hora de evaluación |

---

# 13. Campos renombrados y eliminados

El nuevo contrato utiliza:

```text
codigo_evento_externo → codigo_evento
nombre_evento         → curso_nombre
institucion           → cliente
horas                 → no_horas_real
```

Se eliminaron de la staging:

```text
tema
activo
```

`estado_curso_nombre` es un nuevo dato de SAF.

El campo `estado` que ya existía en staging NO corresponde al estado del curso. Es un campo técnico de procesamiento administrado por Laravel.

---

# 14. Ejemplo de recepción de capacitación

```sql
INSERT INTO tbl_saf_capacitacion_importacion
(
    id_instructor,
    programa_curso_id,
    codigo_evento,
    curso_nombre,
    fecha_inicio,
    fecha_fin,
    estado_curso_nombre,
    no_horas_real,
    modalidad,
    tipo_evento_nombre,
    cliente,
    encuesta_id,
    encuesta_nombre,
    promedio_encuesta,
    fecha_evaluacion,
    estado,
    intentos,
    fecha_recepcion,
    created_at,
    updated_at
)
VALUES
(
    12345,
    501,
    'EVT-2026-001',
    'Excel Avanzado',
    '2026-08-01',
    '2026-08-05',
    'Finalizado',
    16,
    'Virtual',
    'Capacitación',
    'Cliente Ejemplo',
    NULL,
    NULL,
    NULL,
    NULL,
    'PENDIENTE',
    0,
    NOW(),
    NOW(),
    NOW()
);
```

---

# 15. Encuesta posterior

Los campos de encuesta pueden llegar después de la primera recepción del curso.

Ejemplo:

```text
Recepción 1
encuesta_id       = NULL
promedio_encuesta = NULL

Recepción 2
encuesta_id       = 80
promedio_encuesta = 4.75
```

La segunda recepción debe ser una nueva fila de staging con el mismo `id_instructor` y `codigo_evento`.

Laravel actualizará la capacitación funcional existente.

---

# 16. Identidad funcional de capacitaciones

La tabla staging permite recepciones repetidas.

En la tabla funcional Laravel identifica una capacitación por:

```text
id_consultor + codigo_evento
```

Por ello, reenviar un evento no crea automáticamente una capacitación duplicada.

---

# 17. Campo `activo` de capacitaciones

SAF no envía `activo` para capacitaciones.

En `tbl_consultor_capacitacion_fepade` el campo `activo` permanece como dato interno del Sistema de Facilitadores.

Una nueva capacitación SAF se crea con:

```text
activo = 1
```

Una actualización SAF posterior no debe modificar:

- `activo`;
- `deleted_at`;
- `usuario_elim`.

---

# 18. Campos técnicos de staging

Los campos técnicos incluyen, según la tabla:

- `id_importacion`;
- `estado`;
- `resultado_procesamiento`;
- `intentos`;
- `mensaje_error`;
- `fecha_recepcion`;
- `fecha_procesamiento`;
- `id_sincronizacion`;
- `id_registro_local`;
- `created_at`;
- `updated_at`.

Después de la recepción, estos campos son administrados por Laravel.

SAF no debe convertir una fila `PROCESADO` o `ERROR` nuevamente a `PENDIENTE`. Debe insertar una nueva recepción.

---

# 19. Estados técnicos

Valores principales de `estado`:

```text
PENDIENTE
EN_PROCESO
PROCESADO
ERROR
```

`estado_curso_nombre` no debe confundirse con estos valores.

---

# 20. Resultados funcionales

Para instructores:

```text
CREADO
ACTUALIZADO
SIN_CAMBIOS
ERROR
OMITIDO
```

Para capacitaciones:

```text
CREADO
ACTUALIZADO
SIN_CAMBIOS
ERROR
```

El resultado `DESACTIVADO` deja de formar parte del contrato vigente de capacitaciones porque SAF ya no controla su campo `activo`.

---

# 21. Validaciones de instructor

Laravel valida al menos:

- identificador mayor que cero;
- entidad válida;
- nombres y apellidos obligatorios;
- longitud de cadenas;
- tipos de identificación 2, 4, 5 o 7;
- formato de correo cuando se proporciona;
- valor válido de `activo`.

---

# 22. Validaciones de capacitación

Laravel valida al menos:

- `id_instructor`;
- `programa_curso_id`;
- `codigo_evento`;
- `curso_nombre`;
- longitudes máximas;
- fechas válidas;
- `fecha_fin >= fecha_inicio`;
- horas enteras y no negativas;
- promedio numérico cuando se proporciona;
- fecha de evaluación válida.

Además, debe existir un consultor asociado al `id_instructor`.

---

# 23. Auditoría y trazabilidad

Cada recepción procesada conserva:

```text
id_sincronizacion
resultado_procesamiento
id_registro_local
fecha_procesamiento
```

Los errores se registran además en:

```text
tbl_sincronizacion_saf_error
```

Por esta razón no deben eliminarse ni reutilizarse las filas históricas.

---

# 24. Recomendaciones para el equipo SAF

- validar longitudes antes de insertar;
- enviar correo limpio, sin formato HTML ni enlaces;
- mantener `id_instructor` estable;
- mantener `codigo_evento` estable;
- convertir correctamente `Inactivo` a `activo`;
- enviar una nueva fila cuando exista un cambio;
- no corregir directamente tablas funcionales;
- no modificar estados técnicos después de la recepción;
- no eliminar recepciones históricas.

---

# 25. Control de versiones

| Versión | Fecha | Descripción |
|---|---|---|
| 2.0.0 | Julio 2026 | Contrato inicial con staging y procesamiento Laravel |
| 2.1.0 | Agosto 2026 | Nuevo contrato de identificación, correo SAF, capacitaciones, encuestas e historial append-only |

---

# 26. Conclusión

Este contrato es la referencia técnica vigente para cualquier proceso SAF que escriba en las tablas intermedias del Sistema de Facilitadores FEPADE.

Cualquier modificación futura de nombres, tipos, códigos de catálogo o reglas de identidad debe acordarse y documentarse antes de cambiar el proceso de integración.
