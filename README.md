# Sistema de Gestion Kipucloud

Sistema de gestion interna para el hotel Kipucloud, desarrollado con **CodeIgniter 4** y **Bootstrap 5**.

## Funcionalidades

- **Inicio (Dashboard)**: Bienvenida con accesos rapidos a las secciones y estadisticas
- **Noticias / Ideas / Manual**: Publicaciones con destinatarios (todos, individual, departamento), comentarios y tarjetas en dos columnas con autor, fecha y hora
- **Noticias - Vista de detalle**: Pagina dedicada por noticia con contenido, acciones (recordatorio, marcador, comentarios) y metadatos del autor
- **Tareas**: Gestion por departamentos, prioridades, fechas limite, modalidad de completado, asignacion de usuarios y seguimiento de progreso
- **Pases de turno**: Intercambio de informacion entre turnos con puntos por area, revisiones y comentarios
- **Recordatorios / Marcadores**: Personales por usuario
- **Borradores**: Flujo de trabajo borrador -> publicacion, con fijar, destinatarios y comentarios
- **Calendario**: Eventos y reuniones
- **Reparaciones / Peticiones de huespedes**: Solicitudes internas y de huespedes
- **Colaboradores / Personal**: Gestion de empleados con roles y departamentos
- **Configuracion**: Colores del sistema personalizables y logo de marca
- **Perfil**: Edicion de datos y foto de perfil
- **Soporte**: Centro de ayuda, contacto, reportes, terminos y privacidad
- **Usuarios / Pagos** (solo admin): Control de pagos con comprobantes

## Requisitos

- **XAMPP** con **PHP 8.2** o superior (https://www.apachefriends.org)
- **Git** (https://git-scm.com)
- **Composer** (https://getcomposer.org)
- Cuenta de **GitHub**

## Instalacion (primer uso)

1. **Clonar el repositorio**

   ```bash
   git clone https://github.com/AndresR2003/sistem_kipu.git
   cd sistem_kipu
   ```

2. **Instalar dependencias**

   ```bash
   composer install
   ```

3. **Crear el archivo `.env`**

   El archivo `.env` NO esta en el repositorio por seguridad (contiene credenciales de la base de datos).

   ```bash
   copy env .env
   ```

   Luego editar `.env` con las credenciales de la base de datos. Dos opciones:

   **Opcion A - Base de datos compartida (recomendada):**
   Solicitar al administrador las credenciales del servidor (host, usuario, contrasena, nombre de la BD) y completarlas.

   **Opcion B - Base de datos local:**
   Crear una base de datos nueva en `phpMyAdmin` y configurar:

   ```
   database.default.hostname = localhost
   database.default.database = nombre_de_la_bd
   database.default.username = root
   database.default.password =
   ```

4. **Base de datos**

   Ejecutar los archivos SQL en el siguiente orden (estan en la raiz del proyecto):

   - `database_migration_*.sql` (migraciones, en orden alfabetico/cronologico)
   - `database_seed_*.sql` (datos de ejemplo, opcional)

   > ⚠️ **Importante**: Si se usa la base de datos compartida, las migraciones se ejecutan una sola vez en el servidor. No volver a ejecutarlas.

5. **Levantar el sistema**

   - Copiar la carpeta a `C:\xampp\htdocs\sistem_kipu` (si no se clono ahi directamente)
   - Iniciar **Apache** y **MySQL** en el panel de XAMPP
   - Abrir en el navegador: `http://localhost/sistem_kipu`

6. **Usuarios de acceso**

   - El administrador puede crear usuarios desde **Colaboradores / Personal**
   - Credenciales por defecto (si se ejecuto el seed de empleados): `username` / `123456`

## Flujo de trabajo con Git

### Reglas basicas

1. **Siempre `git pull` antes de empezar a trabajar** para traer los ultimos cambios
2. **Commit frecuente** con mensajes descriptivos
3. **`git pull` antes de `git push`** para evitar conflictos
4. No editar el mismo archivo a la vez que otra persona

### Trabajo diario (cambios pequenos)

```bash
git pull                                  # traer cambios de los demas
# ... hacer modificaciones ...
git add .
git commit -m "Descripcion del cambio"
git push                                  # subir cambios
```

### Trabajo con ramas (cambios grandes)

```bash
git checkout -b nombre-de-la-rama         # crear rama nueva
# ... hacer modificaciones ...
git add .
git commit -m "Descripcion del cambio"
git push -u origin nombre-de-la-rama      # subir la rama
```

Luego en GitHub crear un **Pull Request** para fusionar la rama con `main`.

### Resolucion de conflictos

Si dos personas editan las mismas lineas, Git marca el conflicto en el archivo:

```
<<<<<<< HEAD
tu codigo
=======
codigo del otro
>>>>>>> nombre-de-la-rama
```

Se elige manualmente que version queda, se eliminan los marcadores y se hace commit.

## Estructura del proyecto

```
app/
  Controllers/    # Controladores del sistema
  Models/         # Modelos de base de datos
  Views/          # Vistas (HTML + PHP)
  Config/         # Configuracion (rutas, filtros, etc.)
js/               # Archivos JavaScript por seccion
public/           # Archivos publicos (fotos de perfil, etc.)
uploads/          # Archivos subidos por usuarios (comprobantes, fotos)
database_*.sql    # Migraciones y seeds de base de datos
```

## Seguridad

- El archivo `.env` con las credenciales **nunca** se sube a GitHub (esta en `.gitignore`)
- Los comprobantes de pago subidos (`uploads/`) no se versionan
- Las sesiones y archivos temporales (`writable/`) no se versionan

## Soporte

Para reportar problemas o proponer mejoras, usar los issues del repositorio de GitHub.
