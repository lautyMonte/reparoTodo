## Para que pueda iniciar el programa

```bash
composer install (para que cree la carpeta vendor)
copy .env.example .env
php artisan migrate:fresh (crear las tablas en la base de datos)
php artisan db:seed (para poblarlas con datos)
php artisan serve
```
