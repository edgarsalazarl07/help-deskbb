# Diagrama Relacional de la Base de Datos

Este diagrama representa la estructura de la base de datos `helpdesk_mvc_db` utilizada en el sistema HelpDesk MVC.

```mermaid
erDiagram
    USUARIOS {
        int id PK
        varchar apellido_paterno
        varchar apellido_materno
        varchar nombre
        date fecha_nacimiento
        enum sexo
        varchar telefono
        varchar correo
        varchar usuario UK
        varchar password_sha1
        enum rol
        text ubicacion
        tinyint activo
        timestamp fecha_creacion
    }

    DEPARTAMENTOS {
        int id PK
        varchar nombre UK
    }

    PERSONAL {
        int id PK
        varchar nombre
        varchar apellidos
        varchar telefono
        varchar correo
        int usuario_id FK
        int departamento_id FK
    }

    CATEGORIAS_EQUIPO {
        int id PK
        varchar nombre UK
    }

    EQUIPOS {
        int id PK
        varchar marca
        varchar modelo
        varchar numero_serie UK
        int categoria_id FK
        enum estado
    }

    ASIGNACIONES {
        int id PK
        int equipo_id FK
        int personal_id FK
        date fecha_asignacion
        date fecha_devolucion
        enum estado_asignacion
    }

    TICKETS {
        int id PK
        int asignacion_id FK
        varchar titulo
        text descripcion
        enum estado
        int departamento_id FK
        timestamp fecha_creacion
        timestamp fecha_cierre
    }

    TICKET_RESPUESTAS {
        int id PK
        int ticket_id FK
        int usuario_id FK
        text mensaje
        timestamp fecha_registro
    }

    USUARIOS ||--o| PERSONAL : "tiene"
    DEPARTAMENTOS ||--o{ PERSONAL : "agrupa"
    CATEGORIAS_EQUIPO ||--o{ EQUIPOS : "clasifica"
    EQUIPOS ||--o{ ASIGNACIONES : "asignado_en"
    PERSONAL ||--o{ ASIGNACIONES : "recibe"
    ASIGNACIONES ||--o{ TICKETS : "genera"
    DEPARTAMENTOS ||--o{ TICKETS : "atiende"
    TICKETS ||--o{ TICKET_RESPUESTAS : "contiene"
    USUARIOS ||--o{ TICKET_RESPUESTAS : "escribe"
```

## Resumen de Relaciones
- **Personal**: Se vincula a un **Usuario** (para acceso al sistema) y a un **Departamento**.
- **Equipos**: Pertenecen a una **Categoría** y pueden estar en múltiples **Asignaciones** (historial).
- **Asignaciones**: Relacionan un **Equipo** con un miembro del **Personal**.
- **Tickets**: Se crean a partir de una **Asignación** específica y pueden ser escalados a un **Departamento**.
- **Respuestas**: Permiten el hilo de comunicación en un **Ticket** por parte de diferentes **Usuarios**.
