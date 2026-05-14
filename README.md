### SISTEMA DE FACILITADORES 2026 - FEPADE

Este es el repositorio base para la etapa de desarrollo del sistema de fácilitadores desarrollado con Laravel 12.

## Tecnologías utilizadas

○ PHP 8.2.12
○ Laravel 12.x
○ Composer 2.8.3
○ Node v24.13.1
○ Npm 11.10.0
○ MySQL/MariaDB 11.10.0
○ Git version 2.45.0.windows.1

## Estado

Estructura base inicial creada.

El sistema permitirá administrar perfiles profesionales de consultores, incluyendo:

- Información personal
- Contacto
- Formación académica
- Experiencia laboral
- Idiomas
- Habilidades
- Referencias
- Disponibilidad
- Tipos de consultoría
- Documentos y evidencias
- Exportación de CV
- Búsqueda avanzada
- Seguridad basada en roles y permisos
- Registro mediante invitaciones (link/QR)

---

## Arquitectura base

El proyecto utiliza una arquitectura modular basada en:

```text
app/Modules/
├── Seg/
└── Fac/
```

Módulos principales
Módulo Descripción
Seg Seguridad, autenticación, usuarios, roles, permisos e invitaciones
Fac Gestión de consultores/facilitadores

#Instalación local

Clonar repositorio:

- git clone https://github.com/AlexMolina11/sistema_facilitadores.git

Instalar dependencias:

- composer install
- npm install

Configurar entorno:

- cp .env.example .env
- php artisan key:generate

Crear base de datos:
CREATE DATABASE fepade_facilitadores
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

Configurar base de datos en .env:
APP_NAME="Facilitadores FEPADE 2026"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fepade_facilitadores
DB_USERNAME=root
DB_PASSWORD=
Si tu MySQL usa otra clave, colócala.

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

Ejecutar migraciones:

- php artisan migrate

Levantar ambiente local:
Terminal 1:

- php artisan serve

Terminal 2:

- npm run dev

#Autor
FEPADE 2026
Sistema de Facilitadores / Consultores
