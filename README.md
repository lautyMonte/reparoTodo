## Para que pueda iniciar el programa

```bash
composer install (para que cree la carpeta vendor)
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh (crear las tablas en la base de datos)
php artisan db:seed (para poblarlas con datos)
php artisan serve
```
# Para la base de datos (crearlo con este nombre)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reparoTodo
DB_USERNAME=root
DB_PASSWORD=

# Para la libreria chatify (probar antes ejecutar el programa sin realizar los pasos 1 y 2)
1-composer require munafio/chatify (Prepara el entorno de chatify en composer.json y composer.lock)

2-php artisan chatify:install (crea las migraciones,su configuracion, la ruta para comunicarse)

3-php artisan migrate y php artisan storage:link (migrar las tablas, para gestionar conversaciones, participantes, mensajes y configuraciones)
//quizas quitar la opcion de favoritos y bloqueados

5-En modelo user:
use Chatify\Traits\InteractsWithChatify; (para declarar la herramienta que usamos)
use InteractsWithChatify;(va dentro de la clase  y es para que trabaje con los usuarios creados)

4-en .env agregar:
CHATIFY_API_MIDDLEWARE=api,auth:sanctum
CHATIFY_WEB_ENABLED=false
para utilizar las solicitudes de chatify con middleware de API.El "false" es para que podamos crear nuestra propia interfaz de usuario
