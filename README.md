# HelpDesk MVC - Sistema de Gestión de Incidencias

Este es un sistema de mesa de ayuda (Help Desk) robusto y moderno, construido con una arquitectura MVC en PHP, diseñado para gestionar reportes técnicos, equipos y personal de manera eficiente.

## Características Principales
- **Arquitectura MVC**: Separación clara de responsabilidades para un código mantenible.
- **Diseño Glassmorphism**: Interfaz premium con efectos de transparencia y desenfoque.
- **Tablas Interactivas**: Uso de DataTables Responsive con exportación a PDF y Excel.
- **Seguridad**: Autenticación basada en roles y cifrado SHA1.
- **[Diagrama de Base de Datos](database_diagram.md)**: Visualización relacional completa del sistema.

---

## Casos de Uso

### Perfil: Administrador
El administrador tiene control total sobre la infraestructura y el soporte.
- **Gestión de Usuarios**: Crear, editar, activar/desactivar y eliminar cuentas de usuario.
- **Gestión de Catálogos**: Control de Departamentos, Personal y Equipos (hardware/software).
- **Asignación de Recursos**: Vincular equipos específicos a miembros del personal.
- **Control de Tickets**: Monitorear todos los tickets del sistema, actualizarlos y cerrarlos.
- **Generación de Reportes**: Exportar el historial global de tickets a formatos PDF o Excel para auditoría.

### Perfil: Cliente
El cliente es el usuario final que requiere soporte.
- **Reportar Incidencia**: Generar nuevos tickets detallando el problema y el equipo afectado.
- **Seguimiento**: Consultar el estado real de sus reportes (Abierto, En Proceso, Cerrado).
- **Historial Personal**: Ver y exportar sus propios reportes de incidencias.

---

## Reglas de Negocio

1. **Acceso al Sistema**: Solo los usuarios con estado `Activo` pueden iniciar sesión.
2. **Jerarquía de Datos**: Los clientes solo tienen acceso a la información que ellos mismos han generado. Los administradores tienen visibilidad total.
3. **Flujo de Tickets**:
   - Todo ticket nuevo inicia en estado **Abierto**.
   - Solo un administrador puede pasar un ticket a **En Proceso** o **Cerrado**.
4. **Seguridad de Credenciales**: Las contraseñas se almacenan mediante el algoritmo SHA1.
5. **Integridad de Usuario**: No se permite la eliminación del usuario `admin` principal para prevenir el bloqueo del sistema.
6. **Validación de Equipos**: Solo se pueden reportar incidencias sobre equipos que existan previamente en el inventario.

---

## Tecnologías Utilizadas
- **Backend**: PHP 7.4+, MySQL (PDO).
- **Frontend**: Bootstrap 4, jQuery.
- **Extensiones**: DataTables (Responsive, Buttons), FontAwesome 5, SweetAlert2.

---

## Instalación
1. Clona el repositorio.
2. Importa el archivo `database.sql` en tu servidor MySQL.
3. Configura las credenciales en `config/Database.php`.
4. ¡Inicia el servidor y comienza a gestionar!

---
**Docente:** Aquino Roldan Segura
