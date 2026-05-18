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

# Fase 2 — Migraciones de Base de Datos

## Estado actual

Fecha: 14/05/2026

### Seguridad inicial completada

- seg_roles
- seg_permisos
- seg_usuarios

### Catálogos base completados

- tbl_idioma
- tbl_idioma_nivel
- tbl_nivel_academico
- tbl_pais
- tbl_municipio_mh
- tbl_tipo_telefono
- tbl_tipo_referencia
- tbl_tipo_formacion
- tbl_tipo_red_social
- tbl_tipo_habilidad
- tbl_tipo_disponibilidad
- tbl_tipo_documento
- tbl_tipo_consultoria

### Catálogos dependientes completados

- tbl_departamento
- tbl_municipio
- tbl_tipo_atestado
- tbl_habilidad

### Entidad principal completada

- tbl_consultor

### Relación agregada

- FK seg_usuarios.id_consultor → tbl_consultor.id_consultor

### Pendiente siguiente sesión

Bloque de seguridad dependiente:

- seg_usuario_rol
- seg_rol_permiso
- seg_usuario_permiso
- seg_bitacora_accesos
- seg_invitaciones

Posteriormente:

- Tablas dependientes del consultor
