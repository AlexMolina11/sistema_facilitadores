# Manual Operativo — Invitaciones a Consultores

## Sistema de Facilitadores FEPADE 2026

Este documento está dirigido al personal autorizado encargado de administrar invitaciones y accesos de consultores al Sistema de Facilitadores FEPADE.

---

## 1. ¿Cuándo debe crearse una invitación?

Debe crearse una invitación cuando:

- el consultor ya posee expediente;
- el consultor está activo;
- todavía no posee usuario;
- se desea permitir que cree personalmente sus credenciales.

La invitación **no debe** utilizarse para crear un nuevo expediente.

---

## 2. Quién puede crear invitaciones

La administración de invitaciones está disponible únicamente para usuarios autorizados.

Principalmente:

```text
Administrador
Gestor
```

según los permisos configurados.

---

## 3. Crear una invitación

1. Ingrese al Sistema de Facilitadores.
2. Diríjase a: `Consultores → Listado de consultores`
3. Localice al consultor correspondiente.
4. Seleccione la opción para crear su invitación.

---

## 4. Configurar duración

La invitación puede configurarse con una duración determinada.

Ejemplo recomendado:

24 horas

También puede configurarse como ilimitada cuando el proceso lo requiera.

---

## 5. Configurar máximo de usos

Para el registro normal se recomienda:

1 uso

Esto garantiza que, una vez creadas las credenciales, el enlace quede consumido.

También pueden configurarse usos ilimitados cuando exista una necesidad específica.

---

## 6. Generación automática

Al crear la invitación, el sistema genera automáticamente:

- Token único
- URL
- Código QR
- Fecha de expiración
- Estado inicial

El estado inicial normalmente será:

Activa

---

## 7. Revisar el detalle

Después de crear la invitación, revise el detalle.

Confirme:

- Consultor correcto
- Estado
- Duración
- Máximo de usos
- Usos actuales
- Fecha de expiración
- URL
- Código QR

---

## 8. Copiar URL

La URL puede copiarse desde el detalle de la invitación.

Debe compartirse únicamente con el consultor correspondiente.

No se recomienda publicar enlaces de invitación en canales abiertos.

---

## 9. Descargar QR

El código QR puede descargarse para facilitar el acceso desde dispositivos móviles.

El QR contiene la misma URL de invitación.

---

## 10. Enviar invitación por correo

Cuando el consultor posee correo electrónico, el sistema permite enviar la invitación directamente.

El correo incluye:

- logo institucional;
- información del acceso;
- botón para abrir la invitación;
- URL alternativa;
- código QR;
- información de vigencia.

Antes de enviar, verifique que el correo corresponda al consultor correcto.

---

## 11. Consultor sin correo

Cuando el expediente no posee correo, el consultor puede proporcionarlo durante la creación de credenciales.

No es necesario crear manualmente el usuario únicamente por la ausencia de correo en el expediente.

---

## Estados de invitación

### 12. Activa

Significa que la invitación todavía puede utilizarse.

Resultado esperado:

estado = Activa
puedeUsarse() = true

### 13. Consumida

Significa que la invitación alcanzó su máximo de usos o que el consultor ya posee usuario.

Para una invitación de un solo uso:

max_usos = 1
usos_actuales = 1

El enlace ya no debe volver a utilizarse.

### 14. Vencida

Significa que terminó el período de vigencia.

El consultor verá una pantalla de:

Invitación no disponible

En caso de ser necesario conceder acceso nuevamente, deberá generarse una nueva invitación.

### 15. Revocada

Significa que un Administrador o Gestor invalidó manualmente la invitación.

El enlace deja de funcionar inmediatamente.

---

## Revocación

### 16. ¿Cuándo revocar una invitación?

La revocación debe utilizarse cuando:

- la invitación fue enviada al destinatario incorrecto;
- existe sospecha de que el enlace fue compartido;
- se generará una nueva invitación;
- el acceso ya no debe concederse;
- la invitación fue creada por error.

### 17. Cómo revocar

1. Ingrese a: `Seguridad → Invitaciones`
2. Abra el detalle de la invitación.
3. Seleccione: `Revocar invitación`
4. Confirme la operación.

Una vez revocada:

estado = Revocada
puedeUsarse() = false

---

## Registro del Consultor

### 18. Acceso a la invitación

El Consultor puede acceder mediante:

- botón recibido por correo;
- URL;
- código QR.

El sistema valida automáticamente si la invitación todavía puede utilizarse.

### 19. Datos mostrados

El formulario presenta información del expediente como:

- Nombres
- Apellidos
- Tipo de documento
- Número de documento
- Correo

El Consultor debe comprobar que la información corresponde a su identidad.

### 20. Creación de credenciales

El Consultor debe:

1. verificar sus datos;
2. ingresar correo si corresponde;
3. crear una contraseña;
4. confirmar la contraseña;
5. leer términos y políticas;
6. aceptar los términos;
7. seleccionar "Crear mis credenciales".

### 21. Después del registro

Cuando el proceso finaliza correctamente:

```text
Se crea el usuario
 ↓
Se vincula con el consultor
 ↓
Se registra la aceptación de términos
 ↓
Se consume la invitación
 ↓
El consultor puede iniciar sesión
```

### 22. Segundo uso

Una invitación configurada con un solo uso no puede volver a utilizarse después del registro.

Si el Consultor abre nuevamente el enlace, deberá visualizar:

Invitación no disponible

---

## Bitácora de aceptación de términos

### 23. Consultar la bitácora

Ingrese a: `Seguridad → Bitácoras → Aceptación de términos`

La pantalla permite consultar el historial de aceptaciones registradas.

### 24. Información disponible

Entre los principales datos se muestran:

- Consultor
- Correo
- Versión
- Fecha
- Hora
- IP
- Invitación
- Hash

### 25. Uso de filtros

La bitácora permite realizar búsquedas por información del Consultor y por versión de términos.

Esto facilita localizar una aceptación específica.

### 26. Importancia de la bitácora

Los registros deben considerarse históricos.

No deben modificarse manualmente para cambiar:

- Fecha
- Usuario
- Consultor
- Versión
- IP
- Invitación
- Hash

La bitácora representa la evidencia de la aceptación realizada en ese momento.

---

## Portal del Consultor

### 27. Inicio de sesión

Después de crear las credenciales, el Consultor puede ingresar mediante el login normal.

### 28. Menú disponible

Un Consultor puro debe visualizar:

**MI ESPACIO**

- Mi perfil
- Mis capacitaciones

No debe visualizar módulos administrativos.

### 29. Mi perfil

Desde Mi perfil el Consultor puede consultar su expediente y actualizar su información.

El proceso de edición está organizado en:

- Perfil
- Contacto
- Experiencia
- Formación
- Especialización
- Idiomas
- Referencias
- Disponibilidad

### 30. Información que el Consultor no administra

El Consultor no puede modificar directamente:

- Estado del perfil
- Vigencia
- Capacitaciones FEPADE
- Evaluaciones institucionales
- Información de otros consultores

---

## Mis Capacitaciones

### 31. Consultar historial

Desde: `Mi espacio → Mis capacitaciones`

el Consultor puede revisar las capacitaciones registradas institucionalmente.

### 32. Información principal

La vista puede mostrar:

- Curso
- Cliente
- Fecha
- Modalidad
- Horas
- Promedio de evaluación

### 33. Ver detalle

Cada capacitación dispone de una acción para consultar su detalle. Puede incluir:

- Código del evento
- Curso
- Cliente
- Tipo de evento
- Modalidad
- Fechas
- Horas
- Estado
- Encuesta
- Promedio
- Fecha de evaluación
- Fuente

### 34. Evaluación pendiente

Si una capacitación todavía no posee resultados, el sistema mostrará un estado similar a:

Evaluación pendiente

Cuando la información sea recibida desde SAF, aparecerá automáticamente después de la sincronización.

### 35. Solo lectura

El Consultor no puede editar ni eliminar capacitaciones institucionales.

Esto evita inconsistencias con la información proveniente de SAF.

---

## Qué hacer ante problemas

### 36. Invitación vencida

Genere una nueva invitación si todavía corresponde otorgar acceso.

### 37. Invitación revocada

Revise primero el motivo de la revocación.

Si corresponde, genere una nueva invitación.

### 38. Invitación consumida

Verifique si el consultor ya posee usuario.

Si ya tiene usuario, no debe crearse otra cuenta. Debe utilizarse:

- Inicio de sesión

o, si no recuerda la contraseña:

- Recuperación de contraseña

### 39. Correo incorrecto

No envíe una invitación hasta verificar el correo correcto del Consultor.

Si una invitación fue enviada a una dirección incorrecta:

```text
Revocar invitación
 ↓
Corregir información
 ↓
Crear nueva invitación
```

### 40. Usuario eliminado mediante Soft Delete

Actualmente existe una consideración importante:

Si un correo pertenece históricamente a un usuario eliminado mediante Soft Delete, **no debe reutilizarse manualmente** para crear otra cuenta.

La reactivación segura de usuarios eliminados queda registrada como mejora para una versión posterior.

Una coincidencia de correo no debe interpretarse automáticamente como que se trata de la misma persona.

---

## Recomendaciones Operativas

### 41. Configuración recomendada

Para el alta normal de Consultores se recomienda:

Duración: 24 horas
Máximo de usos: 1

### 42. Antes de enviar

Verifique siempre:

- identidad del Consultor;
- correo;
- que todavía no posea usuario;
- duración configurada;
- máximo de usos;
- URL correcta.

### 43. Seguridad

No comparta invitaciones públicamente.

Cada enlace debe considerarse individual y asociado al Consultor correspondiente.

### 44. Contraseñas

El personal administrativo nunca debe solicitar o almacenar manualmente la contraseña personal del Consultor.

La contraseña debe ser creada directamente por el usuario.

### 45. Revocación preventiva

Si existe alguna duda sobre la seguridad de una URL, se recomienda revocarla y generar una nueva.

---

## Flujo Operativo Resumido

```text
Consultor registrado
 ↓
Verificar que no posea usuario
 ↓
Crear invitación
 ↓
24 horas / 1 uso recomendado
 ↓
Revisar URL y QR
 ↓
Enviar al consultor
 ↓
Consultor crea credenciales
 ↓
Acepta términos
 ↓
Sistema crea usuario
 ↓
Invitación queda consumida
 ↓
Consultor inicia sesión
 ↓
Mi perfil
 ↓
Mis capacitaciones
```
