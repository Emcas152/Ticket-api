# Sistema Tickets API (Laravel)

Repositorio con scaffold inicial de una API en Laravel para gestión de entradas.

Contenido principal:
- Migración que importa el esquema SQL proporcionado en `database/schema/`.
- Modelos para las tablas principales.
- Controllers esqueleto para reservas, webhook de pago y generación de tickets.
- Job esqueleto para generar PDF con QR y enviar por correo.

Instrucciones rápidas:

1. Instalar dependencias:

```bash
composer install
```

2. Configurar `.env` (DB, REDIS, MAIL, QUEUE_CONNECTION)

3. Ejecutar migración que importa el esquema SQL:

```bash
php artisan migrate
```

4. Ejecutar worker de queue (si usa jobs):

```bash
php artisan queue:work
```
