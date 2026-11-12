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