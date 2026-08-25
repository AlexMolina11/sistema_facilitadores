# Fase 7 — Registro de Consultores y Aceptación de Términos

## Sistema de Facilitadores FEPADE 2026

Este documento describe el proceso mediante el cual un consultor utiliza una invitación para crear sus credenciales de acceso y aceptar los términos y políticas de uso del Sistema de Facilitadores FEPADE.

---

## 1. Objetivo

El registro mediante invitación permite que un consultor con expediente existente cree personalmente sus credenciales de acceso.

Durante este proceso también se registra formalmente la aceptación de los términos y políticas vigentes.

El flujo busca garantizar:

- identificación correcta del consultor;
- creación segura de credenciales;
- vinculación Usuario ↔ Consultor;
- aceptación obligatoria de términos;
- trazabilidad del proceso;
- consumo controlado de la invitación.

---

## 2. Acceso mediante invitación

El consultor recibe una URL con la estructura:

```text
/registro/{token}
```

Al acceder, el sistema identifica la invitación mediante el token y verifica si todavía puede utilizarse.

Se validan condiciones como:

- estado activo;
- revocación;
- fecha de expiración;
- máximo de usos;
- existencia del consultor;
- inexistencia previa de usuario para ese consultor.

Si alguna condición no se cumple, no se presenta el formulario de registro.

---

## 3. Invitación no disponible

Cuando una invitación no puede utilizarse, el sistema presenta una pantalla informativa.

Los principales motivos pueden ser:

- Invitación vencida
- Invitación revocada
- Invitación consumida
- Consultor con usuario existente
- Invitación no válida

En estos casos el formulario de creación de credenciales no se presenta.

---

## 4. Datos precargados

Cuando la invitación es válida, el sistema utiliza los datos existentes del expediente.

Entre los principales datos mostrados se encuentran:

- Nombres
- Apellidos
- Tipo de identificación
- Número de identificación
- Correo

El objetivo es evitar que el consultor cree un usuario desligado del expediente al cual fue invitado.

---

## 5. Correo electrónico

Cuando el expediente ya posee un correo principal, este se utiliza durante el proceso de registro.

Cuando el consultor no posee correo, el formulario permite capturarlo.

El correo proporcionado durante el registro queda asociado al expediente como correo principal cuando corresponde.

---

## 6. Creación de contraseña

La contraseña es definida directamente por el consultor.

El personal administrativo no necesita conocerla.

La contraseña debe cumplir las reglas mínimas de seguridad configuradas en el sistema.

Posteriormente, el usuario puede utilizar el mecanismo institucional de:

¿Olvidaste tu contraseña?

para realizar un restablecimiento cuando sea necesario.

---

## 7. Términos y políticas de uso

Antes de crear las credenciales, el consultor debe:

- acceder al contenido de términos y políticas;
- leer la información correspondiente;
- confirmar su aceptación;
- marcar el control de aceptación;
- completar el registro.

Sin aceptación no debe completarse la creación del usuario.

---

## 8. Creación del usuario

Una vez que el formulario ha sido validado y los términos han sido aceptados, el sistema crea el usuario en:

seg_usuarios

El usuario queda vinculado con el expediente mediante:

id_consultor

y recibe el rol correspondiente al flujo de invitación.

En el caso normal del acceso para facilitadores:

Rol = Consultor

---

## 9. Relación Usuario ↔ Consultor

Después del registro:

```text
tbl_consultor
      │
      │ id_consultor
      ▼
seg_usuarios
```

El usuario autenticado puede utilizar esta relación para acceder a su propio expediente.

---

## 10. Consumo de la invitación

La invitación se consume únicamente después de completar correctamente el registro.

Abrir el enlace no incrementa los usos.

Enviar el correo tampoco incrementa los usos.

Ejemplo:

Abrir URL
→ usos_actuales = 0

Enviar correo
→ usos_actuales = 0

Crear credenciales correctamente
→ usos_actuales = 1

Para una invitación configurada con:

max_usos = 1

el resultado será:

usos_actuales = 1
estado = Consumida
puedeUsarse() = false

---

## 11. Segundo uso del enlace

Después de consumir una invitación de un solo uso, la misma URL no permite crear otra cuenta.

El sistema presenta:

Invitación no disponible

Esto evita registros duplicados.

---

## Bitácora de Aceptación de Términos

### 12. Objetivo

La aceptación de términos se almacena en una bitácora específica.

Su objetivo es conservar evidencia histórica del momento en que un usuario confirmó la aceptación de las condiciones de uso del sistema.

La aceptación no se almacena únicamente como un valor booleano.

### 13. Tabla de aceptación

La estructura fue creada mediante la migración:

2026_08_17_134020_create_seg_aceptaciones_terminos_table

La bitácora almacena información relacionada con:

- `id_aceptacion`
- `id_consultor`
- `id_usuario`
- `id_invitacion`
- `version_terminos`
- `fecha_aceptacion`
- `ip`
- `hash_terminos`

### 14. Información registrada

Cada aceptación permite conocer:

- Quién aceptó
- Qué versión aceptó
- Cuándo la aceptó
- Desde qué IP
- Mediante qué invitación
- Qué contenido estaba asociado a la aceptación

### 15. Versión de términos

Cada aceptación registra:

version_terminos

Esto permite identificar qué versión de términos estaba vigente cuando se realizó la aceptación.

Si los términos cambian posteriormente, las aceptaciones históricas mantienen la versión correspondiente.

### 16. Fecha de aceptación

El campo:

fecha_aceptacion

registra el momento en que el consultor confirmó la aceptación.

La interfaz administrativa muestra tanto la fecha como la hora.

### 17. Dirección IP

La bitácora conserva:

ip

como información adicional de trazabilidad del proceso.

### 18. Hash de los términos

El sistema registra:

hash_terminos

Este valor permite conservar una referencia verificable sobre el contenido asociado a la aceptación.

En la interfaz administrativa puede mostrarse de forma abreviada, conservando el valor completo en la base de datos.

### 19. Relación con la invitación

Cada aceptación queda vinculada con:

id_invitacion

Esto permite reconstruir el flujo completo:

```text
Consultor
 ↓
Invitación
 ↓
Aceptación de términos
 ↓
Usuario
```

### 20. Módulo administrativo de bitácora

El personal autorizado puede consultar la bitácora desde:

Seguridad
→ Bitácoras
→ Aceptación de términos

La vista presenta información como:

- Consultor
- Correo
- Versión
- Fecha
- Hora
- IP
- Invitación
- Hash

### 21. Filtros

La bitácora permite realizar búsquedas por información relacionada con el consultor y la versión de términos.

Esto facilita auditorías y consultas posteriores.

---

## Portal del Consultor

### 22. Inicio de sesión

Después de crear las credenciales, el consultor puede iniciar sesión mediante el login normal del sistema.

Un usuario Consultor sin permisos administrativos es redirigido hacia su espacio personal.

### 23. Menú del Consultor

El menú principal del Consultor contiene:

**MI ESPACIO**

- Mi perfil
- Mis capacitaciones

Un Consultor puro no debe visualizar opciones administrativas como:

- Dashboard
- Listado de consultores
- Crear consultor
- Catálogos
- Seguridad
- Invitaciones
- Bitácoras

### 24. Mi Perfil

El Consultor puede:

- consultar su expediente;
- editar su información;
- mantener actualizado su perfil;
- utilizar el wizard de actualización.

El wizard está dividido en:

1. Perfil
2. Contacto
3. Experiencia
4. Formación
5. Especialización
6. Idiomas
7. Referencias
8. Disponibilidad

### 25. UX contextual

Cuando el usuario trabaja sobre su propio expediente, el sistema utiliza lenguaje personal como:

- Mi información personal
- Mi información de contacto
- Mi experiencia laboral
- Mi trayectoria educativa
- Mis áreas de especialización
- Mis idiomas
- Mis referencias
- Mi disponibilidad

Cuando un Administrador o Gestor trabaja sobre otro expediente, se utiliza lenguaje administrativo.

### 26. Estado y vigencia

Los campos administrativos:

- `activo`
- `vigente`

no pueden ser modificados por un Consultor puro.

Únicamente usuarios con permisos administrativos de gestión de consultores pueden modificarlos.

La protección existe tanto en la interfaz como en el backend.

### 27. Seguridad del expediente

Un Consultor únicamente puede consultar y editar su propio expediente.

Ejemplo:

/consultores/{su_id} → permitido
/consultores/{otro_id} → 403

También se bloquea el acceso al listado administrativo:

/consultores → 403

### 28. Seguridad de registros hijos

Los controladores verifican que los registros hijos pertenezcan al consultor correspondiente.

Se validaron operaciones manipuladas manualmente sobre:

- Correo
- Experiencia
- Atestado
- Área de especialización
- Idioma
- Referencia

Los registros pertenecientes a otro consultor son rechazados con respuestas 403 o 404 según el controlador correspondiente.

---

## Mis Capacitaciones

### 29. Objetivo

El módulo **Mis capacitaciones** permite que el Consultor consulte el historial institucional de capacitaciones que ha brindado en FEPADE.

Esta información es de solo lectura.

### 30. Información mostrada

Entre los principales datos de capacitación pueden mostrarse:

- Curso
- Código del evento
- Cliente
- Tipo de evento
- Modalidad
- Fecha de inicio
- Fecha de finalización
- Estado
- Horas reales
- Encuesta
- Promedio de evaluación
- Fecha de evaluación
- Fuente

### 31. Evaluación de capacitaciones

El Consultor puede consultar el promedio de evaluación asociado a una capacitación.

Cuando todavía no existe evaluación, el sistema presenta un estado equivalente a:

Evaluación pendiente

Cuando SAF proporcione posteriormente los resultados, estos aparecerán automáticamente después de la sincronización correspondiente.

### 32. Detalle de capacitación

Cada capacitación posee una vista individual.

La vista permite consultar:

- información general del evento;
- cliente;
- modalidad;
- fechas;
- horas;
- estado;
- resultados de evaluación;
- información institucional de referencia.

### 33. Solo lectura

Las capacitaciones provenientes de SAF no pueden modificarse desde el Portal del Consultor.

Esto evita inconsistencias entre:

```text
SAF
 ↓
Tablas intermedias
 ↓
Sincronización Laravel
 ↓
tbl_consultor_capacitacion_fepade
```

### 34. Aislamiento de capacitaciones

El detalle se consulta desde la relación del Consultor autenticado.

Por lo tanto:

/mis-capacitaciones/{capacitacion_propia} → permitido
/mis-capacitaciones/{capacitacion_ajena} → 404

Esto impide que un Consultor consulte evaluaciones o capacitaciones pertenecientes a otra persona.

---

## 35. Validaciones realizadas

Durante la prueba integral de la Fase 7 se verificó:

| Validación                       | Estado |
| -------------------------------- | ------ |
| Registro de credenciales         | ✓      |
| Usuario creado                   | ✓      |
| Rol Consultor                    | ✓      |
| Invitación consumida             | ✓      |
| Bitácora de términos             | ✓      |
| Segundo uso bloqueado            | ✓      |
| Login                            | ✓      |
| Redirección a Mi perfil          | ✓      |
| Menú del Consultor               | ✓      |
| Otro consultor bloqueado         | ✓      |
| Listado administrativo bloqueado | ✓      |
| Mis capacitaciones               | ✓      |

También se verificaron:

- Invitación revocada → acceso bloqueado
- Invitación vencida → `puedeUsarse() = false` → acceso público bloqueado

---

## 36. Resultado

La Fase 7 proporciona un proceso completo para transformar un expediente de consultor existente en un usuario con acceso seguro al Sistema de Facilitadores FEPADE.

El flujo conserva trazabilidad mediante la invitación y la bitácora de aceptación de términos, al mismo tiempo que protege el acceso al expediente y a la información institucional del consultor.
