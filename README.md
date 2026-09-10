# AccessControl

Sistema web para administrar el ingreso de empleados y visitantes. La
aplicacion permite registrar personas, capturar su fotografia mediante webcam,
consultar sus datos, controlar entradas y salidas, procesar colas y generar
reportes de acceso.

## Arquitectura Docker

El entorno se ejecuta como un ecosistema de contenedores conectado a la red
interna `accesscontrol`. Cada servicio tiene una responsabilidad concreta y se
inicia respetando sus dependencias.

```text
Navegador
	 |
	 v
Nginx :8080
	 |
	 v
PHP-FPM (Laravel)
	 |--------------> MySQL :3306
	 |--------------> Queue worker
	 `--------------> Scheduler

init: migraciones + seeder + storage:link
```

### Servicios

| Servicio | Imagen o construccion | Funcion tecnica | Uso funcional |
| --- | --- | --- | --- |
| `db` | `mysql:8.4` | Ejecuta MySQL, crea la base `accesscontrol` y expone un healthcheck. | Conserva usuarios, empleados, visitantes, accesos, sesiones y datos de la aplicacion. |
| `init` | `accesscontrol_app` | Espera a que `db` este saludable y ejecuta `migrate`, `db:seed --class=AdminSeeder` y `storage:link`. | Prepara la base de datos y el almacenamiento antes de permitir el inicio de los servicios Laravel. Termina con `Exited (0)` cuando trabaja correctamente. |
| `app` | `accesscontrol_app` | Ejecuta PHP-FPM en el puerto interno `9000`. | Atiende la logica Laravel: autenticacion, formularios, registros, consultas y reportes. |
| `queue` | `accesscontrol_app` | Ejecuta `php artisan queue:work` con reintentos y timeout. | Procesa tareas diferidas sin bloquear las solicitudes web. |
| `scheduler` | `accesscontrol_app` | Ejecuta `php artisan schedule:work`. | Ejecuta las tareas programadas definidas por Laravel. |
| `nginx` | `nginx:1.27-alpine` | Sirve archivos publicos y reenvia PHP a `app:9000` mediante FastCGI. | Es la puerta de entrada HTTP para el navegador. |

`app`, `queue` y `scheduler` esperan a que `init` termine correctamente. Nginx
espera a que `app` este iniciado y utiliza el nombre DNS interno `app`, que
Docker Compose resuelve dentro de la red del proyecto.

## Archivos y carpetas Docker

```text
Dockerfile
docker-compose.yml
docker/
├── entrypoint.sh
└── nginx/
	 └── default.conf
```

### `Dockerfile`

Construye la imagen `accesscontrol_app` en tres etapas:

1. `vendor`: instala las dependencias PHP de Composer usando
	`composer.lock`, sin dependencias de desarrollo.
2. `frontend`: instala las dependencias Node con `npm ci` y genera los
	recursos compilados mediante `npm run build`.
3. Imagen final PHP 8.3 FPM sobre Debian Bookworm: instala las extensiones
	PHP requeridas, copia el codigo, descubre los paquetes Laravel y prepara
	permisos de `storage` y `bootstrap/cache`.

La imagen no incluye Nginx. PHP-FPM escucha internamente en `9000` y Nginx se
encarga del trafico HTTP.

### `docker-compose.yml`

Define los servicios, la red, las dependencias, los puertos y los volumenes.
El comando de inicializacion es:

```text
php artisan migrate --force
php artisan db:seed --class=AdminSeeder --force
php artisan storage:link --force
```

El `AdminSeeder` crea o actualiza el administrador inicial de forma idempotente,
por lo que reiniciar Compose no debe generar un usuario duplicado.

### `docker/entrypoint.sh`

Es el punto de entrada de la imagen PHP. Antes de iniciar el proceso recibido:

- crea las carpetas requeridas por Laravel;
- ajusta propietario y permisos para `www-data`;
- ejecuta el comando original de la imagen mediante `exec`.

En `app`, por ejemplo, termina ejecutando `php-fpm`. En `queue` y `scheduler`
prepara el mismo entorno y luego ejecuta el comando indicado por Compose.

### `docker/nginx/default.conf`

Configura Nginx para:

- publicar `/var/www/html/public` como raiz web;
- redirigir las rutas Laravel a `public/index.php`;
- enviar archivos PHP a `app:9000`;
- impedir el acceso directo a archivos ocultos como `.env` y `.git`.

## Volumenes y persistencia

| Volumen | Montaje | Contenido |
| --- | --- | --- |
| `app_code` | `/var/www/html` | Codigo de la aplicacion compartido por `init`, `app`, `queue`, `scheduler` y Nginx. |
| `storage_data` | `/var/www/html/storage` | Fotos, logs, cache, sesiones y archivos generados por Laravel. |
| `mysql_data` | `/var/lib/mysql` | Datos persistentes de MySQL. |

Los volumenes permiten recrear contenedores sin perder datos. `docker compose
down` detiene y elimina contenedores, pero conserva los volumenes. Para borrar
tambien la base de datos y los archivos persistentes se debe usar:

```bash
sudo docker compose down -v
```

## Requisitos

- Docker Engine en ejecucion.
- Docker Compose v2 (`docker compose`).
- Un archivo `.env` en la raiz del proyecto con la configuracion Laravel y la
  conexion a la base de datos.
- Permisos para acceder al daemon Docker. En Linux puede usarse `sudo` o
  agregar el usuario al grupo `docker`.

La aplicacion usa los siguientes valores internos definidos en Compose:

```text
Base de datos: accesscontrol
Usuario:       accesscontrol
Password:      accesscontrol_password
Host interno:  db
Puerto interno: 3306
```

Desde el equipo anfitrion, MySQL se publica en el puerto `3307` y la aplicacion
web en el puerto `8080`.

## Puesta en marcha

Desde la raiz del proyecto:

```bash
sudo docker compose build
sudo docker compose up -d
```

Consultar el estado:

```bash
sudo docker compose ps -a
```

El resultado esperado es:

- `db`: `Healthy`;
- `init`: `Exited (0)`;
- `app`, `queue`, `scheduler` y `nginx`: `Up` o `Started`.

El estado `Exited (0)` de `init` es correcto: es un contenedor de una sola
ejecucion, no un proceso permanente.

La aplicacion queda disponible en:

```text
http://localhost:8080
```

### Usuario inicial

El seeder crea el administrador utilizado para acceder al login:

```text
Usuario:     admin
Contrasena:  adminpassword
```

Estas credenciales son de desarrollo. En un entorno real deben cambiarse y no
deben conservarse como valores por defecto.

## Operacion funcional

1. El administrador inicia sesion desde `/login`.
2. Desde el panel puede registrar empleados y visitantes.
3. Los formularios capturan la fotografia desde la webcam y guardan el archivo
	en el almacenamiento publico de Laravel.
4. El sistema permite consultar y editar los registros, registrar entradas y
	salidas, consultar personas dentro de las instalaciones y generar reportes.
5. La cola y el scheduler permanecen disponibles para trabajos diferidos y
	tareas programadas.

El enrolamiento de huella esta desactivado en los formularios de alta y edicion
de empleados y visitantes. La captura y validacion mediante camara permanece
disponible. El codigo biometrico existente no forma parte del flujo requerido
para registrar estos formularios.

## Logs y mantenimiento

Ver todos los logs:

```bash
sudo docker compose logs -f
```

Ver un servicio concreto:

```bash
sudo docker compose logs -f init
sudo docker compose logs -f app
sudo docker compose logs -f nginx
sudo docker compose logs -f db
```

Detener los servicios sin borrar datos:

```bash
sudo docker compose stop
```

Reiniciar la arquitectura:

```bash
sudo docker compose up -d
```

Entrar al contenedor de Laravel para ejecutar comandos Artisan:

```bash
sudo docker compose exec app php artisan route:list
sudo docker compose exec app php artisan view:cache
```

## Diagnostico rapido

### `permission denied` en `/var/run/docker.sock`

El usuario actual no tiene permisos sobre el daemon Docker. Ejecuta los
comandos con `sudo` o configura el grupo `docker` para el usuario.

### `init` termina con error

Revisar el log del inicializador y la salud de MySQL:

```bash
sudo docker compose logs init
sudo docker compose logs db
sudo docker compose ps -a
```

Las causas habituales son credenciales incorrectas en `.env`, una base de datos
no saludable o una migracion con error.

### La web no responde en el puerto 8080

Comprobar que el puerto no este ocupado y revisar Nginx y PHP-FPM:

```bash
sudo docker compose ps
sudo docker compose logs nginx
sudo docker compose logs app
```

### Faltan fotos o archivos de storage

Verificar el volumen `storage_data` y volver a crear el enlace desde `init`:

```bash
sudo docker compose run --rm init php artisan storage:link --force
```

## Consideraciones de seguridad

- Cambiar las contrasenas de MySQL y del administrador antes de publicar el
  sistema fuera de un entorno local.
- No versionar `.env` ni exponerlo mediante Nginx.
- Publicar solo los puertos necesarios.
- Mantener actualizadas las imagenes base y las dependencias Composer y npm.
- Usar HTTPS y una politica de acceso adecuada en ambientes productivos.
