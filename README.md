# 🌿 Hoja Verde — Sistema de Gestión de Inventario

**Centro Holístico Seidel**

Sistema web para la gestión de inventario de productos naturales y medicamentos. Desarrollado como proyecto final de **Algoritmo y Programación II (ALP-365)** — Instituto Universitario Jesús Obrero (IUJO), Barquisimeto.

---

## 📋 Requisitos

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP)

---

## 🚀 Instalación

### 1. Instalar XAMPP
Descarga e instala XAMPP desde [apachefriends.org](https://www.apachefriends.org/).

### 2. Clonar el repositorio en htdocs
```bash
cd C:\xampp\htdocs
git clone https://github.com/GabrielKent04/Gestion_HojaVerde.git gestion_HojaVerde
```

### 3. Importar la base de datos
1. Inicia **Apache** y **MySQL** desde el panel de control de XAMPP.
2. Abre [phpMyAdmin](http://localhost/phpmyadmin).
3. Crea una nueva base de datos o ve a la pestaña **Importar**.
4. Selecciona el archivo `sql/database.sql` ubicado dentro del proyecto.
5. Haz clic en **Continuar** para ejecutar la importación.

### 4. Acceder al sistema
Abre tu navegador y visita:
```
http://localhost/gestion_HojaVerde/auth/login.php
```

---

## 👥 Roles del Sistema

| Rol | Acceso |
|-----|--------|
| **Gerente** | Inventario, Punto de Venta, Reportes PDF, Usuarios, Tasa BCV |
| **Empleado** | Punto de Venta, Inventario |

---

## 🛠️ Tecnologías

- **Backend:** PHP Puro (PDO)
- **Base de Datos:** MySQL / MariaDB
- **Frontend:** HTML5, Tailwind CSS, Alpine.js
- **Iconos:** Lucide Icons
- **Reportes:** FPDF (exportación a PDF)

---

## 👨‍💻 Equipo de Desarrollo

- **Gabriel Alejandro** — Backend, Seguridad, Reportes
- **Luis Torrealba** — CRUD, Inventario, Integridad Relacional
- **Daime Almao** — Frontend UI/UX, Diseño Responsivo
- **Sthephany Cordero** — Interactividad DOM, Validaciones

---

> Proyecto académico — 3er Semestre de Informática, IUJO Barquisimeto, 2026.
