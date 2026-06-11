# Bitácora de Desarrollo

## Proyecto: Sistema de Facilitadores FEPADE 2026

---

# Fase 1 — Configuración Base del Proyecto

Fecha: 14/05/2026

## Objetivo

Preparar la estructura inicial del sistema en Laravel 12 y dejar lista la base para el desarrollo modular.

## Actividades realizadas

### 1. Creación del proyecto

- Se creó proyecto Laravel 12.
- Se generó `APP_KEY`.
- Se configuró entorno `.env`.

### 2. Configuración local

- Configuración de conexión MySQL.
- Validación de conexión a base de datos.
- Configuración inicial de cache y session.

### 3. Estructura modular

Se creó estructura base:

```text
app/Modules/
├── Seg/
│   ├── Controllers/
│   ├── Models/
│   ├── Requests/
│   ├── Services/
│   ├── Middleware/
│   └── Support/
│
└── Fac/
    ├── Controllers/
    ├── Models/
    ├── Requests/
    ├── Services/
    ├── Policies/
    └── Controllers/Catalogo/

Rutas
Se separaron rutas:

routes/
├── web.php
├── seg.php
└── fac.php

Dashboard base
- Se creó DashboardController.
- Se creó vista dashboard inicial.
- Se configuró layout principal.

Frontend
- Configuración inicial de Vite.
- Validación de assets frontend.

###Resultado esperado

El sistema carga correctamente:
/facilitadores/dashboard

Mostrando dashboard inicial sin errores.
```

# Fase 2 — Base de Datos

## Estado actual

Fecha: 18/05/2026

### Seguridad

- seg_roles
- seg_permisos
- seg_usuarios
- seg_usuario_rol
- seg_rol_permiso
- seg_usuario_permiso
- seg_bitacora_accesos
- seg_invitaciones

### Catálogos

- tbl_idioma
- tbl_idioma_nivel
- tbl_nivel_academico
- tbl_pais
- tbl_departamento
- tbl_municipio_mh
- tbl_municipio
- tbl_tipo_telefono
- tbl_tipo_referencia
- tbl_tipo_formacion
- tbl_tipo_atestado
- tbl_tipo_red_social
- tbl_tipo_habilidad
- tbl_habilidad
- tbl_tipo_disponibilidad
- tbl_tipo_documento
- tbl_tipo_consultoria

### Entidad principal

- tbl_consultor

### Dependientes del consultor

- Correos
- Teléfonos
- Documentos
- Redes sociales
- Contacto de emergencia
- Disponibilidad
- Experiencia laboral
- Habilidades
- Idiomas
- Referencias
- Tipos de consultoría
- Formación académica

### Resultado

Base de datos completa con 38 tablas y relaciones foráneas operativas.

---

# Fase 3 — Base Visual del Sistema + Catálogo Modelo

Fecha: 19/05/2026

## Objetivo

Construir la base visual institucional del sistema de Facilitadores FEPADE 2026 para permitir el trabajo paralelo del equipo de desarrollo, definiendo un layout unificado, componentes reutilizables y un catálogo modelo como estándar de implementación.

## Actividades realizadas

### 1. Definición de línea gráfica institucional

Se reemplazó el estilo visual inicial por una nueva identidad UI basada en la propuesta del sistema de Consultores FEPADE.

Se adoptó:

#### Paleta visual principal

```text
Navy        #0D1B2A
Navy 2      #162032
Accent      #00C896
Accent 2    #0099FF
Surface     #F7F9FC
Surface 2   #EEF2F8
Danger      #E24B4A
Warning     #EF9F27
Purple      #7F77DD
Muted       #6B7A90
```

#### Tipografía

```text
Syne
→ títulos, headings, métricas y elementos visuales

DM Sans
→ formularios, labels, navegación y UI general
```

---

### 2. Construcción del layout principal

Se creó estructura base del sistema para reutilización en todos los módulos.

#### Archivos creados

```text
resources/views/layouts/
├── app.blade.php
└── partials/
    ├── sidebar.blade.php
    ├── topbar.blade.php
    └── alerts.blade.php
```

#### Funcionalidades implementadas

- Sidebar institucional.
- Topbar con información de usuario.
- Sistema de alertas reutilizable.
- Estructura responsive base.
- Integración Bootstrap 5.
- Sistema visual homogéneo para todos los módulos.

---

### 3. Sidebar institucional

Se implementó sidebar principal organizado por módulos.

#### Secciones creadas

```text
Principal
- Dashboard

Seguridad
- Usuarios
- Roles
- Permisos
- Invitaciones
- Bitácora de acceso

Consultores
- Consultores
- Búsqueda avanzada
- Revisión de perfiles
- Exportación CV

Catálogos
- Idiomas
- Países
- Departamentos
- Municipios
- Habilidades
- Tipos de consultoría
```

#### Características

- Estado activo de menú.
- Navegación persistente.
- Identidad visual FEPADE.
- Preparado para escalabilidad.

---

### 4. Sistema de componentes reutilizables

Se creó base de componentes Blade reutilizables.

#### Componentes creados

```text
resources/views/components/ui/
├── page-header.blade.php
└── stat-card.blade.php
```

#### Objetivo

Estandarizar:

- Headers de páginas.
- Cards de estadísticas.
- Acciones visuales.
- Estructura de CRUD.

---

### 5. Dashboard institucional

Se reconstruyó dashboard base del sistema.

#### Vista creada

```text
resources/views/fac/dashboard.blade.php
```

#### Funcionalidades

- KPIs visuales.
- Cards de métricas.
- Actividad reciente.
- Accesos rápidos.
- Diseño institucional FEPADE.

---

### 6. Sistema de estilos globales

Se creó archivo CSS institucional.

#### Archivo

```text
public/css/fepade.css
```

#### Contenido

- Variables CSS globales.
- Sistema de colores.
- Sidebar.
- Topbar.
- Cards.
- Formularios.
- Botones.
- Badges.
- Responsive base.

---

### 7. Catálogo modelo — Idiomas

Se implementó primer CRUD funcional como estándar para el equipo.

#### Estructura implementada

```text
Modelo
Request Store
Request Update
Controller
Rutas
Index
Create
Edit
Sidebar
Validaciones
Paginación
Mensajes flash
```

#### Backend creado

```text
app/Modules/Fac/Models/
└── Idioma.php

app/Modules/Fac/Controllers/Catalogo/
└── IdiomaController.php

app/Modules/Fac/Requests/
├── StoreIdiomaRequest.php
└── UpdateIdiomaRequest.php
```

#### Frontend creado

```text
resources/views/fac/catalogos/idiomas/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

#### Funcionalidades implementadas

- Listado de idiomas.
- Búsqueda por nombre.
- Creación.
- Edición.
- Soft delete.
- Estado activo/inactivo.
- Validaciones.
- Paginación Bootstrap 5.
- Integración visual FEPADE.

#### Ajustes realizados

- Corrección de componentes Blade.
- Corrección de namespace del controlador.
- Ajuste de paginación Bootstrap.
- Corrección de contador duplicado.
- Eliminación de campo `codigo` para alineación con tabla real `tbl_idioma`.

---

### 8. Base de trabajo colaborativo

Se dejó arquitectura lista para desarrollo paralelo del equipo.

#### División de trabajo definida

```text
Rama base visual
feature/fase3-layout-base

Colaborador 1
feature/fase3-catalogos-base-1

Colaborador 2
feature/fase3-catalogos-base-2

Colaborador 3
feature/fase3-seguridad-visual

Colaborador 4
feature/fase3-consultores-visual
```

#### Estrategia

Todos los colaboradores desarrollarán utilizando:

```text
Layout base
Sidebar
Topbar
Sistema UI
CSS institucional
Catálogo Idiomas como plantilla
```

---

## Resultado esperado

El sistema ahora cuenta con:

- Base visual institucional FEPADE
- Layout reutilizable
- Sidebar funcional
- Dashboard navegable
- Componentes Blade reutilizables
- Sistema visual unificado
- Catálogo modelo funcional
- Arquitectura lista para trabajo paralelo de 4 desarrolladores

Ruta funcional actual:

```text
/dashboard
/catalogos/idiomas
```

##Seguridad y módulo de usuarios

SEG (Seguridad)
Autenticación

✅ Login

✅ Logout

✅ Contraseñas fuertes

✅ Rate limit

✅ Mensajes en español

Usuarios

✅ CRUD

✅ Protección del último administrador

✅ Asociación con consultores

✅ Permisos directos

Roles

✅ CRUD

✅ Asignación de permisos

Permisos

✅ CRUD

✅ Middleware real

✅ Menú dinámico

Bitácora

✅ Login exitoso

✅ Login fallido

✅ Logout

✅ Usuarios

✅ Roles

✅ Permisos

✅ Filtros

✅ Diseño unificado

UX

✅ Paginación corregida

✅ Alertas unificadas

✅ Fechas en español

✅ Zona horaria El Salvador

✅ Consistencia visual con FAC
