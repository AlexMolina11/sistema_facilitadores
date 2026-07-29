##Responsabilidad de SAF

\*\*SAF es responsable de:

- enviar los datos de negocio;
- utilizar identificadores externos válidos;
- respetar los tipos y longitudes definidos;
- insertar nuevos registros;
- actualizar los datos de negocio previamente enviados;
- no modificar los campos internos del procesamiento.
- Responsabilidad de Laravel

\*\*Laravel es responsable de:

- detectar los registros pendientes;
- validar los datos recibidos;
- controlar los intentos de procesamiento;
- registrar errores;
- sincronizar las tablas funcionales;
- evitar duplicados;
- registrar la ejecución;
- marcar los registros como procesados o con error.

La regla general de la integración es:

##SAF entrega datos.
##Laravel controla la sincronización.
