# Gestor de Tareas Laravel

Una aplicación de gestión de tareas desarrollada con **Laravel 12**, **Blade** y **Tailwind CSS**. 
Permite gestionar tareas personales y compartirlas con otros usuarios que estén registrados en la app.

---

## 🔧 Requisitos

- PHP >= 8.1
- Composer
- Node.js & npm
- MySQL / MariaDB

---

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <tu-repo.git> gestor-tareas
   cd gestor-tareas
   ```

2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```

3. **Configurar el entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   - Ajusta en `.env`: `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

4. **Instalar dependencias JS y compilar assets**
   ```bash
   npm install
   npm run dev    # para desarrollo
   npm run build  # para producción
   ```

5. **Migrar y semillar la base de datos**
   ```bash
   php artisan migrate
   # Opcional: php artisan db:seed
   ```

6. **Ejecutar el servidor local**
   ```bash
   php artisan serve
   ```
   Abre en `http://127.0.0.1:8000`

---

## Estructura del proyecto

La aplicación está organizada de forma clara siguiendo las convenciones de Laravel:

- **app/Models/**: contiene el modelo `Task.php`, donde se definen las propiedades y relaciones de la tarea.
- **app/Policies/**: aquí vive `TaskPolicy.php`, encargado de controlar quién puede editar o borrar cada tarea.
- **app/Http/Controllers/**: incluye `TasksController.php`, con toda la lógica para crear, actualizar, eliminar, completar y compartir tareas.
- **app/Providers/**: registro de políticas de autorización en `AuthServiceProvider.php`.
- **resources/views/**: vistas Blade:
  - `layouts/app.blade.php` como plantilla base con Tailwind y Vite.
  - `tasks/index.blade.php` para la pantalla principal de gestión de tareas.
- **routes/web.php**: define las rutas de la aplicación (inicio, dashboard, CRUD y endpoints AJAX).

## Funcionalidades

- **CRUD** de tareas: crear, listar, editar, eliminar.
- **Completar/Reabrir** tareas con AJAX (fetch API).
- **Compartir** tarea duplicándola a otro usuario por email.
- **Filtrar** por estado (`pending`, `completed`).
- **Ordenar** por fecha de vencimiento (ascendente/descendente).
- **Autenticación** con Laravel Breeze (login, registro).
- **Autorización** con políticas: solo el dueño puede modificar o borrar.

---

## Autorización

Definida en `app/Providers/AuthServiceProvider.php`:
```php
protected $policies = [
    \App\Models\Task::class => \App\Policies\TaskPolicy::class,
];
```
Y en `app/Policies/TaskPolicy.php`:
```php
public function update(User $user, Task $task) {
    return $user->id === $task->user_id;
}
public function delete(User $user, Task $task) {
    return $user->id === $task->user_id;
}
```

## Usuarios
Actualmente, la aplicacion esta hecha con dos usuarios de prueba, dichos usuarios son autogenerados por el factory. (Revisar factory para ver password de usuario, los dos usan la misma password)

---

## Rutas principales

```bash
GET      /                       # Redirige a login o dashboard
GET      /dashboard              # Dashboard de tareas
POST     /tasks/{task}/toggle    # Completar/Reabrir tarea
POST     /tasks/{task}/share     # Compartir tarea
RESOURCE /tasks                  # Rutas CRUD (index, store, update, destroy, etc.)
``` 
