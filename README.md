# ReparoTodo

Aplicación web que conecta **clientes** con **profesionales** de oficios (plomería, electricidad, gas, cerrajería, etc.). El cliente busca un profesional, le envía una solicitud, recibe un presupuesto, conversa por chat y, al terminar el trabajo, puede dejar su reseña. El profesional puede publicar los trabajos que terminó para mostrarlos en su perfil.

Trabajo Final Integrador de **Programación Web Dinámica** (Tecnicatura en Desarrollo Web, Universidad Nacional del Comahue).

**Integrantes:** Luciano Elia, Franco Palma, Matias Jañez y Lautaro Montesino.

## Requisitos

- PHP 8.3 o superior
- Composer
- MySQL (por ejemplo, el que trae XAMPP)

## Cómo iniciar el proyecto

Desde la carpeta del proyecto:

```bash
composer install
copy .env.example .env
php artisan key:generate
```

En macOS o Linux, el segundo comando es `cp .env.example .env`.

Después, **creá una base de datos vacía** llamada `reparotodo` (por ejemplo, desde phpMyAdmin). Si tu MySQL tiene usuario o contraseña distintos a los de siempre (`root` sin contraseña), cambialos en el archivo `.env` (`DB_USERNAME` y `DB_PASSWORD`).

Por último:

```bash
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

- `migrate:fresh --seed` crea todas las tablas y las llena con datos de prueba.
- `storage:link` hace visibles las fotos que se suben (perfiles, portadas y trabajos publicados).
- `serve` inicia la aplicación en http://localhost:8000.

## Usuarios de prueba

Todos tienen la contraseña **`password123`**. Se ingresa con el nombre de usuario.

| Usuario | Tipo | Nombre |
|---|---|---|
| `lautarorodriguez` | Cliente | Lautaro Rodriguez |
| `carlagomez` | Cliente | Carla Gomez |
| `luciaromero` | Cliente | Lucia Romero |
| `manuelferrari` | Cliente | Manuel Ferrari |
| `pablodiaz` | Cliente | Pablo Diaz |
| `estebanquito` | Profesional | Esteban Quito |
| `armandoparedes` | Profesional | Armando Paredes |
| `lujanperez` | Profesional | Lujan Perez |
| `matiasjañez` | Profesional | Matias Jañez |
| `matiassalgado` | Profesional | Matias Salgado |

También se pueden crear cuentas nuevas desde la pantalla de registro.

## Notas

- **Chat:** usa la librería [Chatify](https://github.com/munafio/chatify), que ya está incluida en `composer.json`. No hace falta instalar nada más. Las variables necesarias ya vienen en `.env.example`.
- **Selectores con búsqueda:** los campos de selección múltiple del registro (género, oficios, provincias y ciudades) usan la librería [Tom Select](https://tom-select.js.org/). Se carga por CDN desde el layout, así que no hace falta instalarla.
- **Formulario de contacto:** por defecto no envía correos reales (`MAIL_MAILER=log` en `.env`). Los mensajes quedan registrados en `storage/logs/laravel.log`. Para enviarlos por Gmail, cambiá la sección de mail del `.env` por tus datos (`MAIL_MAILER=smtp`, tu correo y una clave de aplicación).
- **Si se cambia algo de la base de datos:** volvé a correr `php artisan migrate:fresh --seed`. Esto borra y recrea todos los datos.