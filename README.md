<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>


## UES-FORMS - BACKEND
Pasos para la instalación de UES-Forms
1. Composer install
```bash
composer install
```
2. Copiar el archivo .env.example a .env
```bash
cp .env.example .env
```
3. Crear la base de datos PostgreSQL
4. Generar la clave de la aplicación
```bash
php artisan key:generate
```
5. Migrar la base de datos y sembrar los datos iniciales
```bash
php artisan migrate --seed
```
6. Iniciar el servidor de desarrollo
```bash
php artisan serve
```
