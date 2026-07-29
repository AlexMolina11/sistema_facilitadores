##Las tablas iniciales de integración son:

- tbl_saf_instructor_importacion
- tbl_saf_capacitacion_importacion

Podrán agregarse nuevas tablas de integración en futuras fases.
Cada nueva tabla deberá conservar la misma separación de responsabilidades:

- SAF administra datos de negocio.
- Laravel administra campos de control.

##Tabla de instructores

Nombre: tbl_saf_instructor_importacion
Campos de negocio administrados por SAF

SAF puede insertar y actualizar únicamente los siguientes datos:

Campo Tipo Obligatorio Descripción
id_instructor BIGINT UNSIGNED Sí Identificador del instructor en SAF
id_entidad BIGINT UNSIGNED Sí Identificador de la entidad de origen
nombres VARCHAR(150) Sí Nombres del instructor
apellidos VARCHAR(150) Sí Apellidos del instructor
dui VARCHAR(20) No Documento Único de Identidad
activo TINYINT(1) Sí Estado activo del instructor

##Estados internos

Los estados válidos son:

- PENDIENTE
- EN_PROCESO
- PROCESADO
- ERROR

Estos estados son administrados exclusivamente por Laravel.

SAF no debe:

- enviar un valor para estado;
- cambiar un registro a PENDIENTE;
- cambiar un registro a PROCESADO;
- eliminar mensajes de error;
- reiniciar intentos;
- asignar un identificador de sincronización.

Cuando SAF inserta un registro, la base de datos asigna automáticamente:

estado = PENDIENTE
intentos = 0
fecha_recepcion = CURRENT_TIMESTAMP

##Inserción correcta de instructor

SAF debe insertar únicamente los campos de negocio:

INSERT INTO tbl_saf_instructor_importacion (
id_instructor,
id_entidad,
nombres,
apellidos,
dui,
activo
)
VALUES (
990001,
1,
'PRUEBA',
'INTEGRACION SAF',
'00000000-0',
1
);

##Actualización correcta de instructor

Cuando SAF necesite actualizar información de negocio, deberá modificar
únicamente los campos autorizados.

\*Ejemplo:

UPDATE tbl_saf_instructor_importacion
SET
id_entidad = 1,
nombres = 'NOMBRES ACTUALIZADOS',
apellidos = 'APELLIDOS ACTUALIZADOS',
dui = '00000000-0',
activo = 1
WHERE id_instructor = 990001;

\*SAF no debe ejecutar:

UPDATE tbl_saf_instructor_importacion
SET
estado = 'PENDIENTE',
intentos = 0,
mensaje_error = NULL,
fecha_procesamiento = NULL,
id_sincronizacion = NULL
WHERE id_instructor = 990001;

El reprocesamiento de registros será responsabilidad del Sistema de
Facilitadores.

---

##Tabla de capacitaciones

Nombre: tbl_saf_capacitacion_importacion

SAF podrá administrar solamente los campos que representen datos de negocio
de la capacitación.
