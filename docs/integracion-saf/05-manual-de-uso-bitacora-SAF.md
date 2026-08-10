# Manual de uso — Bitácora de Sincronización SAF

## 1. Objetivo

La **Bitácora SAF** permite supervisar las sincronizaciones realizadas entre SAF y el Sistema de Facilitadores FEPADE.

Su propósito principal es permitir identificar de forma rápida:

- cuándo se realizó una sincronización;
- cuántos consultores fueron incorporados;
- qué consultores fueron creados, actualizados o procesados;
- qué capacitaciones fueron procesadas;
- qué registros presentaron errores;
- por qué un registro no pudo procesarse;
- qué acción se recomienda para solucionar un error;
- cuáles incidencias ya fueron atendidas.

La Bitácora SAF debe utilizarse principalmente como una herramienta de **seguimiento y control operativo de la integración**, no como una herramienta para modificar información proveniente de SAF.

---

# 2. Flujo general de la integración

La integración funciona bajo el siguiente esquema:

```text
SAF
 ↓
Tablas intermedias de importación
 ↓
Laravel valida los registros
 ↓
Procesamiento
 ↓
┌───────────────────┬───────────────────┐
│                   │                   │
ÉXITO              ERROR
│                   │
↓                   ↓
Sistema de         Bitácora SAF
Facilitadores      registra incidencia
```

Las tablas intermedias utilizadas son:

```text
tbl_saf_instructor_importacion
tbl_saf_capacitacion_importacion
```

Laravel procesa estos registros y sincroniza la información válida con:

```text
tbl_consultor
tbl_consultor_capacitacion_fepade
```

Cada ejecución genera además un registro de sincronización que permite consultar posteriormente qué ocurrió.

---

# 3. Acceso a la Bitácora SAF

La Bitácora se encuentra dentro del módulo:

```text
Seguridad
→ Bitácora SAF
```

El usuario debe contar con el permiso:

```text
seg.bitacora-saf.ver
```

para poder acceder.

---

# 4. Dashboard de Bitácora SAF

La pantalla principal presenta un resumen de la actividad de integración.

El objetivo del dashboard no es mostrar todos los detalles técnicos de una ejecución, sino permitir detectar rápidamente el comportamiento de las sincronizaciones.

## 4.1. Actividad por día

El gráfico **Actividad SAF por día** permite visualizar la evolución de las importaciones.

Presenta información sobre:

- consultores ingresados;
- capacitaciones creadas;
- registros con error.

Esto permite identificar visualmente:

- días con mayor cantidad de importaciones;
- periodos sin actividad;
- incrementos importantes de registros;
- días en los que ocurrieron errores.

---

# 5. Indicadores principales

El dashboard muestra indicadores relacionados con el periodo seleccionado.

Entre ellos:

### Consultores ingresados

Cantidad de nuevos consultores creados mediante SAF.

### Sincronizaciones con error

Cantidad de ejecuciones que finalizaron con errores o fallaron.

### Registros con error

Cantidad de registros individuales que no pudieron procesarse correctamente.

Estos indicadores cambian según los filtros aplicados.

---

# 6. Filtros

La Bitácora permite filtrar las sincronizaciones utilizando:

- fecha inicial;
- fecha final;
- resultado de la sincronización.

Los estados principales son:

```text
Completada
Completada con errores
Fallida
```

Después de seleccionar los criterios se debe presionar:

```text
Aplicar
```

Para regresar al historial completo se puede utilizar:

```text
Limpiar
```

---

# 7. Historial de sincronizaciones

La tabla **Sincronizaciones** muestra las ejecuciones realizadas.

Por cada ejecución se puede visualizar:

- fecha y hora;
- resultado;
- consultores procesados;
- capacitaciones procesadas;
- cantidad de errores.

Cuando una sincronización finalizó correctamente aparece la acción:

```text
Ver registros
```

Cuando contiene incidencias aparece:

```text
Revisar errores
```

---

# 8. Consultar una sincronización

Al ingresar al detalle de una sincronización existen tres vistas principales:

```text
Consultores
Capacitaciones
Errores
```

Cada una permite revisar los registros asociados específicamente a esa ejecución.

---

# 9. Consultores procesados

La pestaña **Consultores** muestra los instructores provenientes de SAF que fueron procesados durante la sincronización.

La información incluye:

- identificador del instructor en SAF;
- nombre del consultor;
- DUI;
- resultado del procesamiento;
- fecha y hora de procesamiento;
- acceso al expediente local cuando corresponda.

Los posibles resultados incluyen:

```text
CREADO
ACTUALIZADO
SIN_CAMBIOS
ERROR
OMITIDO
```

## CREADO

Significa que el instructor proveniente de SAF no existía en Facilitadores y se creó un nuevo expediente.

## ACTUALIZADO

Significa que el consultor ya existía y su información fue actualizada.

## SIN_CAMBIOS

Significa que el consultor ya existía y la información recibida no requería modificaciones.

## ERROR

Significa que el registro no pudo procesarse.

La causa debe revisarse desde la pestaña **Errores**.

## OMITIDO

Significa que el registro fue descartado debido a alguna condición funcional definida por la integración.

---

# 10. Expediente del consultor

Cuando un instructor fue procesado correctamente y existe un consultor relacionado, la Bitácora muestra la acción:

```text
Expediente
```

Esta opción permite abrir directamente el expediente del consultor dentro del Sistema de Facilitadores.

La Bitácora no modifica el expediente; únicamente facilita su consulta.

---

# 11. Capacitaciones procesadas

La pestaña **Capacitaciones** muestra las capacitaciones provenientes de SAF que participaron en la sincronización.

Se puede consultar:

- código externo del evento;
- instructor relacionado;
- nombre de la capacitación;
- tema;
- fecha;
- resultado;
- fecha de procesamiento.

Los principales resultados son:

```text
CREADO
ACTUALIZADO
SIN_CAMBIOS
DESACTIVADO
ERROR
```

Esto permite identificar exactamente qué capacitaciones fueron incorporadas o modificadas durante cada ejecución.

---

# 12. Errores de sincronización

La pestaña **Errores** contiene las incidencias detectadas durante el procesamiento.

Esta es una de las secciones más importantes de la Bitácora SAF.

Cada incidencia permite identificar:

- tipo de registro;
- identificador externo;
- mensaje del error;
- código del error;
- fecha del incidente;
- estado de resolución;
- recomendación para resolverlo;
- información técnica adicional.

Los errores pueden corresponder a:

```text
CONSULTOR
CAPACITACION
GENERAL
```

---

# 13. Cómo interpretar un error

Cada error debe analizarse principalmente mediante tres elementos.

## Registro afectado

Permite identificar qué instructor o capacitación presentó el problema.

Ejemplo:

```text
Tipo: CONSULTOR
Registro SAF: 999999
```

## Motivo

Describe por qué Laravel no pudo procesar el registro.

Ejemplo:

```text
El campo nombres es obligatorio.
```

## Cómo resolverlo

La Bitácora presenta una recomendación funcional según el tipo de incidencia.

Ejemplo:

```text
El registro contiene un dato que no cumple las validaciones
del sistema.

Revisar el campo indicado y corregirlo en SAF.

Después de la corrección, el registro debe volver a enviarse
para procesamiento.
```

---

# 14. Principio fundamental para resolver errores

Los datos provenientes de SAF **no deben corregirse directamente desde la Bitácora SAF**.

El principio de responsabilidad de la integración es:

```text
SAF
es responsable de los datos de origen.

Laravel
es responsable de validar y procesar los datos.

Bitácora SAF
es responsable de registrar y explicar el resultado.
```

Por lo tanto, si existe un dato incorrecto como:

- nombre;
- apellido;
- DUI;
- entidad;
- código;
- capacitación;
- fecha;
- instructor relacionado;

la corrección debe realizarse en el sistema de origen correspondiente.

---

# 15. Flujo para resolver una incidencia

Cuando se detecta un error debe seguirse este procedimiento:

```text
1. Revisar la incidencia en Bitácora SAF.

2. Identificar el registro afectado.

3. Leer el motivo del error.

4. Revisar la recomendación "Cómo resolverlo".

5. Corregir el dato en SAF cuando el problema
   corresponda a información de origen.

6. Permitir que SAF vuelva a enviar o actualizar
   el registro en la tabla intermedia.

7. Ejecutar o esperar la siguiente sincronización.

8. Verificar que el registro haya sido procesado
   correctamente.

9. Regresar a la incidencia original.

10. Marcarla como resuelta.
```

---

# 16. Ejemplo de resolución

Supongamos que SAF envía:

```text
Instructor SAF: 999999
Nombres: vacío
Apellidos: Pérez
```

Laravel detectará que el nombre es obligatorio.

El registro quedará:

```text
estado = ERROR
resultado_procesamiento = ERROR
```

La Bitácora mostrará la incidencia.

El responsable debe corregir el instructor en SAF:

```text
Nombres: Juan
Apellidos: Pérez
```

SAF deberá posteriormente actualizar el registro correspondiente para que vuelva a ser procesado.

En una nueva sincronización el resultado podría ser:

```text
estado = PROCESADO
resultado_procesamiento = CREADO
```

El consultor aparecerá entonces dentro de la sincronización exitosa.

---

# 17. Marcar una incidencia como resuelta

Una incidencia no debe marcarse como resuelta únicamente porque fue revisada.

Debe marcarse como resuelta cuando se haya comprobado que la causa fue atendida.

En el campo:

```text
Observación de resolución
```

debe colocarse una descripción breve de la acción realizada.

Ejemplo:

```text
Se corrigió el nombre del instructor en SAF y el registro
fue procesado correctamente en la sincronización posterior.
```

Luego se debe presionar:

```text
Marcar resuelto
```

La Bitácora conservará:

- error original;
- fecha del error;
- sincronización donde ocurrió;
- fecha de resolución;
- usuario que registró la resolución;
- observación de resolución.

---

# 18. Reabrir una incidencia

Si posteriormente se determina que el problema no estaba realmente solucionado, puede utilizarse:

```text
Reabrir incidencia
```

Esto devuelve el error al estado pendiente.

La opción debe utilizarse cuando todavía exista una acción necesaria para solucionar completamente el problema.

---

# 19. Errores técnicos

La sección:

```text
Ver información técnica
```

contiene información destinada principalmente al equipo de Tecnología.

Puede incluir:

- operación realizada;
- código del error;
- detalle técnico;
- excepción;
- información relacionada con el procesamiento.

Esta información resulta útil cuando el problema no corresponde a los datos provenientes de SAF, sino a una falla interna.

Ejemplos:

```text
Error de base de datos
Excepción Laravel
Error durante una transacción
Problema interno del servicio de sincronización
```

En estos casos el problema debe ser revisado por el equipo técnico antes de intentar reprocesar el registro.

---

# 20. Sincronizaciones vacías

Es posible que el proceso programado se ejecute cuando no existen registros pendientes.

En ese caso puede generarse una sincronización con:

```text
Instructores procesados: 0
Capacitaciones procesadas: 0
Errores: 0
```

Esto no representa una falla.

Simplemente indica que el proceso se ejecutó correctamente, pero SAF no tenía registros pendientes de procesamiento en ese momento.

---

# 21. Historial y trazabilidad

Cada registro procesado conserva la relación con la sincronización correspondiente mediante:

```text
id_sincronizacion
```

También se conserva el resultado individual mediante:

```text
resultado_procesamiento
```

y, cuando existe un registro creado o actualizado en Facilitadores:

```text
id_registro_local
```

Esto permite reconstruir posteriormente qué ocurrió durante cada sincronización.

---

# 22. Consideración sobre sincronizaciones antiguas

Las sincronizaciones realizadas antes de implementar la trazabilidad individual pueden no contener:

```text
resultado_procesamiento
id_registro_local
```

Por esta razón, algunos registros históricos podrían mostrar únicamente:

```text
PROCESADO
```

sin especificar si fueron:

```text
CREADOS
ACTUALIZADOS
SIN_CAMBIOS
```

Esto es esperado y no debe modificarse manualmente.

No se deben inventar resultados históricos que el sistema no registró originalmente.

---

# 23. Ejecución automática

La integración SAF está programada para ejecutarse automáticamente una vez al día.

La ejecución programada corresponde a:

```text
05:00 a. m.
```

Después de cada ejecución, la Bitácora SAF puede utilizarse para verificar el resultado.

La revisión diaria recomendada consiste en comprobar:

```text
Bitácora SAF
 ↓
Última sincronización
 ↓
¿Tiene errores?
 ↓
NO ──→ No requiere intervención
 ↓
SÍ
 ↓
Revisar errores
 ↓
Identificar registros afectados
 ↓
Gestionar corrección
```

---

# 24. Ejecución manual

Cuando sea necesario ejecutar manualmente el procesamiento, desde la raíz del proyecto puede utilizarse:

```bash
php artisan saf:procesar-importaciones
```

Debe utilizarse principalmente para:

- pruebas;
- verificaciones técnicas;
- reprocesamiento después de una corrección;
- diagnóstico de incidencias.

En producción debe evitarse ejecutar repetidamente el comando sin verificar primero la causa de los errores existentes.

---

# 25. Buenas prácticas

Para mantener una Bitácora SAF confiable:

1. No modificar directamente desde Laravel datos cuyo origen sea SAF.
2. No eliminar errores históricos.
3. No modificar manualmente sincronizaciones anteriores.
4. Registrar una observación clara al resolver una incidencia.
5. Verificar que el registro haya sido procesado correctamente antes de marcar un error como resuelto.
6. Utilizar la información técnica únicamente cuando sea necesario diagnosticar una falla.
7. Mantener los registros históricos como evidencia del funcionamiento de la integración.
8. Revisar periódicamente las incidencias pendientes.
9. Investigar incrementos anormales de errores en el gráfico de actividad.
10. No considerar una sincronización con cero registros como un error si no existían registros pendientes.

---

# 26. Resumen operativo

La Bitácora SAF debe permitir responder rápidamente cuatro preguntas:

### ¿Cuándo se sincronizó?

Consultar el gráfico de actividad y el historial de sincronizaciones.

### ¿Qué ingresó correctamente?

Ingresar al detalle y consultar:

```text
Consultores
Capacitaciones
```

### ¿Qué no pudo ingresar?

Consultar:

```text
Errores
```

### ¿Qué debemos hacer?

Revisar:

```text
Cómo resolverlo
```

y seguir la recomendación indicada.

El principio general de operación puede resumirse como:

```text
DETECTAR
   ↓
IDENTIFICAR
   ↓
CORREGIR EN ORIGEN
   ↓
REPROCESAR
   ↓
VERIFICAR
   ↓
RESOLVER INCIDENCIA
```

De esta manera, la Bitácora SAF funciona como el punto central de seguimiento y auditoría de la integración entre SAF y el Sistema de Facilitadores FEPADE.
