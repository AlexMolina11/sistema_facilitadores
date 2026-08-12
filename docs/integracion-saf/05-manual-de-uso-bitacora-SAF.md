# Manual de uso — Bitácora de Sincronización SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.1.0  
**Estado:** Vigente  
**Última actualización:** Agosto 2026

---

# 1. Objetivo

La Bitácora SAF permite supervisar las sincronizaciones realizadas entre SAF y el Sistema de Facilitadores FEPADE.

Permite identificar:

- cuándo se ejecutó una sincronización;
- qué instructores participaron;
- qué consultores fueron creados o actualizados;
- qué capacitaciones fueron procesadas;
- qué registros no presentaron cambios;
- qué registros fallaron;
- cuál fue la causa de cada error;
- qué acción se recomienda para resolver una incidencia;
- qué incidencias ya fueron atendidas.

La Bitácora es una herramienta de seguimiento y auditoría. No es una pantalla para corregir datos provenientes de SAF.

---

# 2. Flujo general

```text
SAF
 ↓
Tablas de importación
 ↓
Laravel valida
 ↓
Laravel sincroniza
 ↓
┌──────────────────────┬──────────────────────┐
│ ÉXITO                │ ERROR                │
│                      │                      │
▼                      ▼
Expediente             Bitácora registra
actualizado             incidencia
```

Tablas de entrada:

```text
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
```

Tablas funcionales principales:

```text
tbl_consultor
tbl_consultor_documento
tbl_consultor_email
tbl_consultor_capacitacion_fepade
```

---

# 3. Historial de recepciones

Cada envío de SAF genera una fila independiente en staging.

Por ejemplo, el mismo instructor puede aparecer en distintas sincronizaciones:

```text
Sincronización 15 → CREADO
Sincronización 18 → ACTUALIZADO
Sincronización 25 → SIN_CAMBIOS
```

Esto es normal y permite reconstruir la historia completa del registro.

---

# 4. Acceso

La Bitácora se encuentra en:

```text
Seguridad
→ Bitácora SAF
```

El usuario debe poseer el permiso correspondiente, actualmente:

```text
seg.bitacora-saf.ver
```

---

# 5. Dashboard

La pantalla principal resume la actividad de integración del período seleccionado.

Permite observar:

- actividad por día;
- consultores ingresados;
- capacitaciones procesadas;
- sincronizaciones con error;
- registros individuales con error.

El objetivo es detectar rápidamente días sin actividad, incrementos inesperados o incidencias recurrentes.

---

# 6. Filtros

La Bitácora permite filtrar por criterios como:

- fecha inicial;
- fecha final;
- resultado de sincronización.

Estados generales habituales:

```text
Completada
Completada con errores
Fallida
```

`Aplicar` ejecuta el filtro y `Limpiar` restaura la consulta general.

---

# 7. Historial de sincronizaciones

Cada ejecución muestra información como:

- fecha/hora;
- resultado;
- consultores procesados;
- capacitaciones procesadas;
- cantidad de errores.

Según el resultado puede mostrarse una acción como:

```text
Ver registros
```

o:

```text
Revisar errores
```

---

# 8. Detalle de una sincronización

El detalle se organiza principalmente en:

```text
Consultores
Capacitaciones
Errores
```

Cada pestaña muestra únicamente los registros asociados a esa ejecución mediante `id_sincronizacion`.

---

# 9. Consultores procesados

La pestaña Consultores puede mostrar:

- `id_instructor` SAF;
- nombre del consultor;
- tipo y número de identificación recibido;
- resultado individual;
- fecha de procesamiento;
- enlace al expediente local.

La identificación ya no se limita a DUI. Puede corresponder a:

```text
NIT
Pasaporte
Licencia de conducir
DUI
```

---

# 10. Resultados de consultor

## CREADO

El `id_instructor` no tenía un consultor relacionado y Laravel creó un nuevo expediente.

También puede haber creado el documento y correo SAF correspondiente.

## ACTUALIZADO

El consultor ya existía y al menos un dato SAF requirió actualización, por ejemplo:

- nombres;
- apellidos;
- documento;
- correo;
- activo/vigente.

## SIN_CAMBIOS

La recepción fue válida, pero la información coincidía con lo ya sincronizado.

No representa un error.

## ERROR

La recepción no pudo procesarse.

Debe revisarse la pestaña Errores.

## OMITIDO

La recepción fue descartada por una condición funcional, por ejemplo una entidad no permitida.

---

# 11. Expediente del consultor

Cuando existe `id_registro_local`, la Bitácora puede ofrecer acceso al expediente.

La identificación SAF se consulta en la sección de documentos del consultor y el correo SAF en sus correos de contacto.

La Bitácora no modifica estos datos.

---

# 12. Capacitaciones procesadas

La pestaña Capacitaciones utiliza el contrato vigente y puede mostrar:

- `codigo_evento`;
- instructor relacionado;
- `curso_nombre`;
- cliente;
- fechas;
- estado del curso;
- modalidad;
- tipo de evento;
- horas reales;
- resultado;
- fecha de procesamiento.

Ya no se utilizan los campos anteriores:

```text
codigo_evento_externo
nombre_evento
tema
institucion
horas
```

---

# 13. Resultados de capacitación

Los resultados vigentes son:

```text
CREADO
ACTUALIZADO
SIN_CAMBIOS
ERROR
```

## CREADO

Se creó una nueva capacitación funcional.

## ACTUALIZADO

El evento ya existía, pero cambió algún dato SAF. Puede ser, por ejemplo, que posteriormente llegara una encuesta.

## SIN_CAMBIOS

El evento recibido coincide con la información ya sincronizada.

## ERROR

No pudo procesarse. Debe revisarse el detalle de la incidencia.

`DESACTIVADO` ya no forma parte del contrato vigente porque SAF dejó de administrar `activo` para capacitaciones.

---

# 14. Encuestas de capacitación

Una capacitación puede registrarse inicialmente sin encuesta y actualizarse posteriormente con:

- `encuesta_id`;
- `encuesta_nombre`;
- `promedio_encuesta`;
- `fecha_evaluacion`.

Si esto ocurre, una nueva recepción del mismo evento aparecerá normalmente como `ACTUALIZADO`.

---

# 15. Activo interno de capacitación

El campo `activo` de `tbl_consultor_capacitacion_fepade` es interno.

Por lo tanto, una sincronización SAF no debe reactivar una capacitación que fue desactivada desde Facilitadores ni desactivarla por información externa.

La Bitácora puede mostrar una actualización de datos SAF sin que cambie el estado interno `activo`.

---

# 16. Errores de sincronización

La pestaña Errores permite identificar:

- tipo de registro;
- identificador externo;
- código de error;
- mensaje;
- fecha;
- detalle técnico;
- estado de resolución;
- observación de resolución.

Tipos generales:

```text
CONSULTOR
CAPACITACION
GENERAL
```

---

# 17. Errores frecuentes de instructor

Ejemplos:

```text
VALIDACION_TIPO_IDENTIFICACION
VALIDACION_CORREO_SAF
ENTIDAD_NO_PERMITIDA
```

Un tipo de identificación diferente de 2, 4, 5 o 7 será rechazado.

Un correo con formato inválido también generará error si `correo_saf` fue informado.

---

# 18. Errores frecuentes de capacitación

Ejemplos:

```text
CONSULTOR_NO_ENCONTRADO
VALIDACION_FECHA_FIN
VALIDACION_CODIGO_EVENTO
VALIDACION_PROGRAMA_CURSO_ID
```

`CONSULTOR_NO_ENCONTRADO` significa que SAF envió una capacitación cuyo `id_instructor` todavía no posee un consultor relacionado en Facilitadores.

---

# 19. Cómo interpretar una incidencia

Revisar tres elementos:

## Registro afectado

Identifica al instructor o evento.

## Motivo

Explica qué validación o proceso falló.

## Cómo resolverlo

Indica si debe corregirse información de SAF o si el equipo técnico debe revisar un problema interno.

---

# 20. Principio para resolver errores

```text
SAF
→ corrige datos de origen

Laravel
→ valida y procesa

Bitácora SAF
→ registra y explica
```

Los datos de origen no se corrigen directamente desde la Bitácora.

---

# 21. Flujo correcto de resolución

```text
1. Abrir la incidencia.
2. Identificar el registro.
3. Revisar código y mensaje.
4. Determinar si el problema es de origen o técnico.
5. Corregir en SAF cuando corresponda.
6. SAF envía una NUEVA recepción.
7. Esperar o ejecutar la sincronización.
8. Verificar el nuevo resultado.
9. Regresar a la incidencia original.
10. Marcarla como resuelta con observación.
```

No se debe modificar la fila histórica con error para volverla `PENDIENTE`.

---

# 22. Ejemplo: tipo de documento inválido

SAF envía:

```text
id_instructor = 990010
tipo_identificacion = 99
```

Laravel rechaza el registro porque los valores permitidos son:

```text
2, 4, 5, 7
```

La recepción original queda `ERROR`.

Después de corregir el código en SAF, debe enviarse una nueva fila. La nueva recepción podrá terminar `CREADO`, `ACTUALIZADO` o `SIN_CAMBIOS`, mientras el error original permanece en el historial.

---

# 23. Ejemplo: capacitación sin consultor

Si SAF envía:

```text
id_instructor = 999999
codigo_evento = EVT-001
```

pero no existe consultor asociado, la Bitácora registrará:

```text
CONSULTOR_NO_ENCONTRADO
```

Primero debe existir/procesarse correctamente el instructor. Después SAF debe enviar nuevamente la capacitación.

---

# 24. Marcar incidencia como resuelta

Una incidencia se marca resuelta únicamente cuando se comprobó que su causa fue atendida.

La observación debe describir brevemente la acción.

Ejemplo:

```text
Se corrigió el tipo de identificación en SAF y la nueva recepción
fue procesada correctamente.
```

La Bitácora conserva el error original, usuario, fecha y observación de resolución.

---

# 25. Reabrir incidencia

Si se descubre que el problema continúa, puede utilizarse la opción de reabrir incidencia.

Debe utilizarse cuando todavía exista una acción pendiente.

---

# 26. Información técnica

La sección de información técnica está orientada al equipo de Tecnología.

Puede incluir:

- operación;
- código interno;
- excepción;
- archivo/línea;
- detalle recibido;
- identificadores de staging y sincronización.

Debe utilizarse para diagnosticar fallas de aplicación, base de datos o transacción.

---

# 27. Sincronizaciones vacías

Una ejecución puede procesar cero registros.

Ejemplo:

```text
Instructores: 0
Capacitaciones: 0
Errores: 0
```

Esto no es una falla si no existían recepciones pendientes.

---

# 28. Trazabilidad

Cada recepción conserva:

```text
id_importacion
id_sincronizacion
resultado_procesamiento
id_registro_local
fecha_procesamiento
```

Gracias al diseño append-only, una recepción posterior no elimina la relación histórica de la recepción anterior.

---

# 29. Sincronizaciones históricas antiguas

Registros creados antes de incorporar `resultado_procesamiento` o `id_registro_local` pueden mostrar información menos detallada.

No deben completarse manualmente con resultados inventados.

---

# 30. Revisión diaria recomendada

```text
Bitácora SAF
 ↓
Última sincronización
 ↓
¿Existen errores?
 ├─ No → Sin intervención
 └─ Sí
     ↓
 Revisar incidencias
     ↓
 Identificar origen
     ↓
 Gestionar corrección
     ↓
 Verificar nueva recepción
```

---

# 31. Ejecución automática

La integración está programada una vez al día a las:

```text
05:00 a. m.
```

La Bitácora debe revisarse después de la ejecución cuando exista una operación crítica o cuando se estén corrigiendo incidencias.

---

# 32. Ejecución manual

Comando:

```bash
php artisan saf:procesar-importaciones
```

Usarlo para:

- pruebas;
- diagnóstico;
- validación posterior a correcciones;
- ejecuciones administrativas controladas.

No ejecutarlo repetidamente sin revisar primero qué registros están pendientes o con error.

---

# 33. Buenas prácticas

1. No modificar datos SAF directamente en tablas funcionales.
2. No eliminar errores históricos.
3. No reutilizar filas staging procesadas.
4. Corregir datos de origen en SAF.
5. Verificar la nueva recepción antes de cerrar una incidencia.
6. Registrar observaciones claras de resolución.
7. Diferenciar errores funcionales de errores técnicos.
8. Revisar incrementos anormales de errores.
9. No considerar una ejecución vacía como fallo por sí sola.
10. Mantener la Bitácora como evidencia histórica de la integración.

---

# 34. Resumen operativo

La Bitácora debe responder cuatro preguntas:

### ¿Cuándo se sincronizó?

Revisar actividad e historial.

### ¿Qué se procesó?

Revisar Consultores y Capacitaciones.

### ¿Qué falló?

Revisar Errores.

### ¿Qué debe hacerse?

Revisar motivo, recomendación y corregir en el origen correspondiente.

Flujo resumido:

```text
DETECTAR
   ↓
IDENTIFICAR
   ↓
CORREGIR EN ORIGEN
   ↓
NUEVA RECEPCIÓN
   ↓
PROCESAR
   ↓
VERIFICAR
   ↓
RESOLVER INCIDENCIA
```

---

# 35. Conclusión

La Bitácora SAF es el punto central de observación y auditoría de la integración. Su función es conservar evidencia de cada recepción y permitir que el equipo identifique con rapidez qué ocurrió, por qué ocurrió y qué acción corresponde realizar.
