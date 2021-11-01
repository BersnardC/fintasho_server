
## Requerimientos del proyecto
 - Apache
 - Mysql
 - PHP 7++
 - Recomendaciones (XAMP o WAMP)
 - Colocar carpeta del proyecto en htdocs (Xampp)

## Configurar DB
Ir al archivo .env  y configurar credenciales de base de datos mysql

## Configurar Servidor de Correo
Ir al archivo .env  y configurar MAIL_USERNAME y MAIL_PASSWORD (Si es correo de google)

## Acerca del correo
Para hacer el envio de correo mediante una cuenta de google es importante permitir el acceso a aplicaciones menos seguras de su cuenta, para hacer configurarlo en: <a href="https://myaccount.google.com/lesssecureapps?pli=1&rapt=AEjHL4MehRvsqLK35u7YnhPZFqRpyEyo-8bvrqE2m9ypXTYz0ufWGDGZg1V3mOpeuwfqQhH-fJHplc3OShzDye1nNfGTK5Ttyw">Mi cuenta google</a>

## Correr migraciones
php artisan migrate

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
