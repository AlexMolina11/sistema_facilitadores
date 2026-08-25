# Fase 7 — Módulo de Invitaciones

## Sistema de Facilitadores FEPADE 2026

Este documento describe el funcionamiento técnico y funcional del módulo de invitaciones implementado durante la **Fase 7 — Invitaciones** del Sistema de Facilitadores FEPADE.

---

## 1. Objetivo

El módulo de invitaciones permite proporcionar acceso al Sistema de Facilitadores a consultores que ya poseen un expediente registrado, pero todavía no cuentan con credenciales de usuario.

El principio fundamental del flujo es:

> El consultor existe primero como expediente profesional y posteriormente puede recibir acceso al sistema mediante una invitación.

Los consultores pueden ingresar al sistema desde SAF sin poseer inicialmente un usuario asociado.

Posteriormente, un Administrador o Gestor autorizado puede generar una invitación para que el propio consultor cree sus credenciales.

---

## 2. Flujo general

```text
SAF
 ↓
Consultor registrado
 ↓
Consultor sin usuario
 ↓
Administrador/Gestor crea invitación
 ↓
Sistema genera token único
 ↓
Sistema genera URL
 ↓
Sistema genera código QR
 ↓
Invitación enviada al consultor
 ↓
Consultor abre enlace
 ↓
Consultor crea credenciales
 ↓
Acepta términos y políticas
 ↓
Sistema crea usuario
 ↓
Usuario queda vinculado al consultor
 ↓
Invitación queda consumida
 ↓
Consultor puede iniciar sesión
```

---

## 3. Relación Consultor ↔ Usuario

La invitación **no crea** un nuevo expediente de consultor.

El expediente debe existir previamente en:

tbl_consultor

Cuando el consultor completa correctamente el registro, el sistema crea el usuario correspondiente en:

seg_usuarios

La relación se establece mediante:

seg_usuarios.id_consultor

Conceptualmente:

```text
tbl_consultor
      │
      │ id_consultor
      ▼
seg_usuarios
```

---

## 4. Roles e `id_consultor`

El rol de un usuario y su relación con un consultor son conceptos independientes.

La arquitectura utiliza la siguiente regla:

- **ROL / PERMISOS** → determina las funciones administrativas disponibles
- **id_consultor** → habilita el espacio personal del consultor

Por esta razón, son válidos escenarios como:

| Rol           | id_consultor |
| ------------- | ------------ |
| Administrador | NULL         |
| Administrador | 5            |
| Gestor        | NULL         |
| Gestor        | 8            |
| Consultor     | 12           |

Un Administrador o Gestor que también posea `id_consultor` conserva sus funciones administrativas y además obtiene acceso a:

- Mi perfil
- Mis capacitaciones

La presencia de `id_consultor` no sustituye ni elimina los permisos asignados por los roles.

---

## 5. Tabla de invitaciones

Las invitaciones se almacenan en:

seg_invitaciones

Entre los principales datos registrados se encuentran:

- `id_invitacion`
- `id_consultor`
- `id_rol`
- `alias`
- `token`
- `url_invitacion`
- `ruta_qr`
- `duracion_horas`
- `max_usos`
- `usos_actuales`
- `fecha_expiracion`
- `activa`
- `revocada`
- `usuario_crea`
- `usuario_mod`
- `usuario_elim`
- `created_at`
- `updated_at`
- `deleted_at`

La estructura fue ajustada durante la Fase 7 mediante la migración:

2026_08_13_094551_update_seg_invitaciones_for_consultor_access_flow

---

## 6. Creación de una invitación

Una invitación puede generarse desde el listado de consultores o desde el módulo de Invitaciones.

Antes de generarla debe verificarse que:

- el consultor exista;
- el consultor esté activo;
- todavía no posea usuario;
- la invitación corresponda al consultor correcto.

El flujo recomendado es:

```text
Listado de consultores
 ↓
Seleccionar consultor
 ↓
Crear invitación
 ↓
Configurar vigencia
 ↓
Configurar máximo de usos
 ↓
Generar
```

---

## 7. Duración de la invitación

Una invitación puede tener duración limitada.

Ejemplo:

24 horas

En ese caso:

duracion_horas = 24

y el sistema calcula:

fecha_expiracion

También puede configurarse sin límite de tiempo:

duracion_horas = NULL
fecha_expiracion = NULL

La invitación no vence automáticamente por tiempo.

---

## 8. Máximo de usos

Una invitación puede configurarse con un máximo de usos.

Para el registro normal de un consultor se recomienda:

1 uso

Ejemplo:

max_usos = 1
usos_actuales = 0

Después de crear correctamente las credenciales:

usos_actuales = 1

y la invitación queda consumida.

También puede configurarse con usos ilimitados:

max_usos = NULL

---

## 9. Token de invitación

Cada invitación genera un token único.

La URL pública utiliza una estructura similar a:

/registro/{token}

Ejemplo conceptual:

https://facilitadores.fepade.org.sv/registro/TOKEN_GENERADO

El token permite identificar la invitación sin exponer directamente su identificador interno.

La URL completa queda almacenada en:

url_invitacion

---

## 10. Código QR

Cada invitación genera automáticamente un código QR asociado a su URL.

Los códigos QR se generan en formato:

SVG

Esto evita depender de la extensión Imagick para la generación de archivos PNG.

La ruta queda almacenada en:

ruta_qr

Ejemplo:

invitaciones/qr/invitacion_15.svg

Desde el detalle de la invitación puede:

- visualizarse;
- descargarse;
- utilizarse en el correo enviado al consultor.

---

## 11. Detalle de invitación

La pantalla de detalle permite consultar información como:

- consultor;
- estado;
- duración;
- máximo de usos;
- usos actuales;
- fecha de creación;
- fecha de expiración;
- URL;
- código QR.

Desde esta pantalla pueden realizarse acciones como:

- Copiar URL
- Descargar QR
- Enviar correo
- Revocar invitación
- Volver al listado

---

## 12. Estados funcionales

El sistema determina dinámicamente el estado de una invitación.

Los principales estados son:

- Activa
- Vencida
- Revocada
- Consumida
- Inactiva

---

## 13. Invitación Activa

Una invitación está activa cuando:

- está habilitada;
- no está revocada;
- no ha vencido;
- no alcanzó el máximo de usos;
- existe el consultor;
- el consultor todavía no posee usuario.

Resultado:

estado() = "Activa"
puedeUsarse() = true

---

## 14. Invitación Vencida

Una invitación está vencida cuando:

fecha_expiracion < fecha actual

Resultado:

estado() = "Vencida"
puedeUsarse() = false

No es necesario modificar manualmente el campo `activa`.

La lógica de negocio determina que la invitación ya no puede utilizarse.

Cuando el consultor intenta abrirla, el sistema presenta una pantalla de:

Invitación no disponible

indicando que la invitación ha vencido.

---

## 15. Invitación Revocada

Una invitación puede ser invalidada manualmente por un usuario autorizado.

Después de revocarla:

revocada = true

Resultado:

estado() = "Revocada"
puedeUsarse() = false

Si el consultor intenta acceder mediante la URL, el sistema muestra un mensaje indicando que la invitación fue revocada y ya no puede utilizarse.

---

## 16. Invitación Consumida

Una invitación queda consumida cuando alcanza el máximo de usos permitido.

Ejemplo:

max_usos = 1
usos_actuales = 1

Resultado:

estado() = "Consumida"
puedeUsarse() = false

También deja de ser utilizable cuando el consultor ya posee un usuario asociado.

---

## 17. Validación central

El modelo de invitación utiliza el método:

puedeUsarse()

para centralizar las condiciones necesarias para permitir el acceso.

La invitación se rechaza cuando:

- está inactiva;
- está revocada;
- está vencida;
- alcanzó el máximo de usos;
- no existe el consultor;
- el consultor ya posee usuario.

Esto evita depender únicamente de los estados visuales de la interfaz.

---

## 18. Envío por correo electrónico

Cuando el consultor posee correo electrónico, la invitación puede enviarse directamente desde el sistema.

El correo utiliza la identidad institucional del Sistema de Facilitadores FEPADE.

Incluye:

- logo FEPADE;
- nombre del consultor;
- explicación del proceso;
- botón de acceso;
- URL alternativa;
- código QR;
- información sobre vigencia.

El envío del correo **no consume** la invitación.

Ejemplo:

Antes de enviar:
usos_actuales = 0

Después de enviar:
usos_actuales = 0

---

## 19. Consultor sin correo

Un consultor proveniente de SAF puede no poseer inicialmente correo electrónico.

Cuando esto ocurre, el registro público permite capturar el correo durante la creación de credenciales.

El correo proporcionado queda asociado al expediente como correo principal cuando corresponde.

---

## 20. Rutas principales

Las principales rutas administrativas del módulo son:

GET seg/invitaciones
POST seg/invitaciones/consultores/{consultor}
GET seg/invitaciones/{invitacion}
GET seg/invitaciones/{invitacion}/qr/descargar
PATCH seg/invitaciones/{invitacion}/revocar

El registro público utiliza:

GET registro/{token}
POST registro/{token}

---

## 21. Seguridad del módulo

La administración de invitaciones está destinada únicamente a usuarios autorizados.

Principalmente:

- Administrador
- Gestor

según los permisos configurados.

El Consultor **no administra** sus propias invitaciones.

La URL pública únicamente permite continuar cuando la invitación cumple todas las condiciones definidas por `puedeUsarse()`.

---

## 22. Validaciones realizadas

Durante el cierre de la Fase 7 se verificaron satisfactoriamente:

- creación de invitación;
- generación de token;
- generación de URL;
- generación de QR SVG;
- duración limitada;
- duración ilimitada;
- máximo de usos;
- usos ilimitados;
- envío por correo;
- revocación;
- vencimiento;
- consumo;
- bloqueo del segundo uso;
- creación de usuario;
- vinculación Usuario ↔ Consultor;
- asignación del rol Consultor.

---

## 23. Resultado

El módulo proporciona un mecanismo controlado y trazable para convertir un expediente de consultor existente en un usuario con acceso al Sistema de Facilitadores FEPADE.

La invitación funciona como el mecanismo de autorización inicial para que el propio consultor cree sus credenciales sin que el personal administrativo conozca o defina su contraseña.
