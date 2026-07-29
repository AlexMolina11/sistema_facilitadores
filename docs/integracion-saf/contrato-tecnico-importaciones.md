# Contrato técnico de importaciones SAF

## Sistema de Facilitadores FEPADE 2026

**Versión:** 2.0.0
**Estado:** Vigente  
**Responsables:** Equipo SAF y equipo del Sistema de Facilitadores  
**Motor de base de datos:** MariaDB / MySQL

---

## 1. Objetivo

Este documento define el contrato técnico mediante el cual SAF enviará
información al Sistema de Facilitadores FEPADE.

SAF escribirá exclusivamente en las tablas intermedias destinadas a la
recepción de datos.

Laravel será responsable de validar, procesar y sincronizar esos registros
con las tablas funcionales del Sistema de Facilitadores.

El flujo establecido es:

```text
SAF
 ↓
Tablas intermedias de importación
 ↓
Laravel valida y procesa
 ↓
Servicios de sincronización
 ↓
Tablas funcionales
```
