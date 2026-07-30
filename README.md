# Parabite Backend

Parabite Backend is a Laravel 12 JSON API for buyers, merchants, and administrators. Authentication uses JWT bearer tokens, application data is stored in MySQL, and API responses follow the existing `code`, `message`, `data`, and `errors` format.

Complete endpoint, request-body, response, authentication, and usage documentation is available in [`docs/API.md`](docs/API.md).

Order queue numbers are generated transactionally per Merchant per day and returned by order creation and order-query APIs.

## Requirements

- PHP 8.2 or newer
- Composer
- MySQL
- Node.js and npm for the Laravel welcome-page assets

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

The database seeder creates an Admin account, a Merchant account, and the initial location list. Change seeded credentials before using the application outside local development.

## Roles

- `admin`: accesses Admin dashboard statistics and manages users and locations.
- `user`: existing Buyer role and Buyer API flows.
- `merchant`: existing Merchant/Seller role and Merchant API flows.

All protected endpoints require:

```text
Authorization: Bearer <jwt-token>
```

The login response includes `account.dashboard_path`, which allows API clients to route each role to its own dashboard without changing existing authentication behavior.

## Token Lifecycle

Access tokens are valid for three days by default and may be refreshed for fourteen days from their original issue time. These values can be configured with:

```dotenv
JWT_TTL=4320
JWT_REFRESH_TTL=20160
JWT_BLACKLIST_ENABLED=true
```

Refresh uses JWT rotation rather than a second stored refresh-token string:

```http
POST /api/v1/refresh
Authorization: Bearer <current-or-recently-expired-token>
```

The response returns a new bearer token and invalidates the previous token immediately. Replace the locally stored token before making another API request.

Logout invalidates the current token:

```http
POST /api/v1/logout
Authorization: Bearer <current-token>
```

The login and refresh responses include `expired_at` and `refreshable_until` Unix timestamps. Once the refresh window expires, the user must log in again.

## Admin API

All Admin endpoints are under `/api/v1/admin` and require the `admin` role.

| Method | Endpoint | Purpose |
| --- | --- | --- |
| `GET` | `/dashboard` | Dashboard statistics and current Admin summary |
| `GET` | `/users` | Paginated user list with `search` and `role` filters |
| `POST` | `/users` | Create a user |
| `GET` | `/users/{user_id}` | View non-sensitive user details |
| `PUT` | `/users/{user_id}` | Update a user or reset their password |
| `DELETE` | `/users/{user_id}` | Safely deactivate a user |
| `GET` | `/locations` | Paginated location list with `search` |
| `POST` | `/locations` | Create a location |
| `GET` | `/locations/{location_id}` | View a location |
| `PUT` | `/locations/{location_id}` | Rename a location |
| `DELETE` | `/locations/{location_id}` | Delete an unused location |

User deletion is implemented as account deactivation so orders, menus, ratings, favorites, wishlists, and other historical records remain valid. Locations referenced by orders cannot be deleted.

## Validation

```bash
composer test
./vendor/bin/pint --test
npm install
npm run build
```

The repository contains feature tests for Admin authorization, user management, and location management under `tests/Feature/Admin`.

## Frontend Scope

This repository contains only the Laravel API and the default Laravel welcome-page assets. It does not contain an existing Buyer, Merchant, or Admin application layout/component system. Admin UI screens should consume the endpoints above from the corresponding frontend repository rather than introducing a second frontend architecture here.
