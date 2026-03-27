# Sistema Tickets API

Backend API desacoplado para venta y gestión de eventos, construido con Laravel 11, MySQL/SQLite, JWT propio, arquitectura `Controllers / Services / Repositories`, `Form Requests` y `API Resources`.

## Módulos implementados

- Auth con registro, login, logout y roles `admin`, `organizer`, `user`
- Eventos con CRUD completo
- Venues, secciones y asientos por venue
- Consulta de asientos por evento
- Reserva temporal de asientos con TTL
- Confirmación de booking
- Pago simulado con `stripe` o `paypal`
- Emisión de tickets con QR y PDF

## Endpoints

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/events`
- `GET /api/events/{event}`
- `POST /api/events`
- `PUT|PATCH /api/events/{event}`
- `DELETE /api/events/{event}`
- `GET /api/events/{event}/seats`
- `POST /api/seats/reserve`
- `POST /api/bookings`
- `POST /api/payments`
- `GET /api/tickets`
- `GET /api/tickets/{ticket}/download`

## Configuración rápida

1. Instala dependencias:

```bash
composer install
```

2. Configura variables de entorno desde `.env.example`.

3. Ejecuta migraciones y seed:

```bash
php artisan migrate --seed
```

4. Levanta la API:

```bash
php artisan serve
```

## Variables recomendadas

- `JWT_SECRET`
- `JWT_TTL=3600`
- `RESERVATION_TTL_MINUTES=10`
- `DB_CONNECTION=mysql`

## Seeder demo

Usuarios creados:

- `admin@tickets.test / Password123!`
- `organizer@tickets.test / Password123!`
- `user@tickets.test / Password123!`

Evento demo:

- `Rock Night 2026`

## Flujo básico

1. Autenticar usuario con `/api/auth/login`
2. Consultar `/api/events/{event}/seats`
3. Reservar asientos con `/api/seats/reserve`
4. Confirmar booking con `/api/bookings`
5. Pagar con `/api/payments`
6. Descargar ticket desde `/api/tickets/{ticket}/download`
