# Ferreluz

Aplicación web básica para ferretería hecha con PHP y MySQL. Incluye únicamente:
- Login y cierre de sesión con dos roles: administrador y empleado.
- Módulo Inicio.
- Módulo Inventario: listar, buscar productos y consultar alertas de stock.
- Administración de usuarios (solo administrador).
- Cabecera y pie de página compartidos en las páginas internas.
- Diseño oscuro carbón con gris, blanco cálido y acento ámbar.

## Instalar con XAMPP
1. Extrae `Ferreluz.zip` dentro de `C:\xampp\htdocs\`.
2. Enciende Apache y MySQL.
3. Abre `http://localhost/phpmyadmin/` e importa `database.sql`.
4. Revisa las credenciales MySQL de `config.php`.
5. Visita `http://localhost/Ferreluz/setup.php` y pulsa “Crear usuarios y productos”.
6. Inicia sesión en `http://localhost/Ferreluz/login.php`.

## Accesos de prueba
- Administrador: `admin` / `password`
- Empleado: `empleado` / `empleado123`

El administrador puede crear/editar/eliminar productos y crear usuarios. El empleado puede ver y buscar el inventario, pero no puede editar productos ni gestionar usuarios.

**Seguridad:** elimina `setup.php` después de la configuración y cambia las contraseñas iniciales. Para producción añade protección CSRF, políticas de contraseñas y configuración HTTPS.
