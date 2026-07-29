# Parabite API Documentation

## 1. Overview

Parabite is a Laravel 12 JSON API for three account roles:

| Role | API value | Responsibilities |
| --- | --- | --- |
| Administrator | `admin` | Dashboard statistics, user management, and location management |
| Buyer | `user` | Browse menus and merchants, manage a temporary cart, create orders, view history, and manage favorites |
| Merchant/Seller | `merchant` | Manage menus, process orders and payments, and view income reports |

The API uses JWT bearer-token authentication.

### Base URL

Local development:

```text
http://localhost:8000/api/v1
```

Examples in this document use:

```bash
BASE_URL=http://localhost:8000/api/v1
```

### Required headers

JSON requests:

```http
Accept: application/json
Content-Type: application/json
```

Protected requests:

```http
Authorization: Bearer <JWT_TOKEN>
```

File-upload requests use:

```http
Accept: application/json
Authorization: Bearer <JWT_TOKEN>
Content-Type: multipart/form-data
```

## 2. Common Response Format

Successful responses generally use:

```json
{
  "code": 200,
  "message": "Success",
  "data": {}
}
```

Validation failures use HTTP `422`:

```json
{
  "code": 422,
  "message": "Validation Error",
  "errors": {
    "email": [
      "The email field is required."
    ]
  }
}
```

Authentication and authorization failures:

| HTTP status | Meaning |
| --- | --- |
| `401` | Token is missing, invalid, expired, blacklisted, or outside its refresh window |
| `403` | The account is inactive or does not have the required role |
| `404` | Requested Admin-managed user or location was not found |
| `409` | The requested operation conflicts with account or relational-data rules |
| `422` | Request validation failed |
| `500` | Unexpected server error |

## 3. Shared Values

### Roles

| Value | Meaning |
| --- | --- |
| `admin` | Administrator |
| `user` | Buyer |
| `merchant` | Merchant/Seller |

### Menu types

| Value | Meaning |
| --- | --- |
| `1` | Food |
| `2` | Drink |

### Menu statuses

| Value | Meaning |
| --- | --- |
| `1` | Available |
| `2` | Unavailable |
| `3` | Coming soon |

### Order types

| Value | Meaning |
| --- | --- |
| `1` | Delivery |
| `2` | Takeaway |
| `3` | Dine-in |

### Payment methods

| Value | Meaning |
| --- | --- |
| `1` | Cash |
| `2` | QRIS |

### Order statuses

| Value | Meaning |
| --- | --- |
| `1` | Waiting |
| `2` | Processing |
| `3` | Done |
| `4` | Cancelled |

## 4. Authentication

### 4.1 Register Buyer

```http
POST /user/register
```

Authentication: not required.

#### JSON body

| Field | Type | Required | Validation |
| --- | --- | --- | --- |
| `name` | string | Yes | Maximum 255 characters |
| `email` | string | Yes | Valid email, maximum 255 characters, unique |
| `phone_number` | string | Yes | Maximum 255 characters |
| `password` | string | Yes | Minimum 8 characters |
| `confirmed_password` | string | Yes | Must match `password` |

```json
{
  "name": "Buyer Example",
  "email": "buyer@example.com",
  "phone_number": "081234567890",
  "password": "secure-password",
  "confirmed_password": "secure-password"
}
```

Success: HTTP `201`. Registration does not automatically log the account in.

### 4.2 Register Merchant

```http
POST /merchant/register
```

Authentication: not required.

The body and validation rules are identical to Buyer registration. The created account receives the `merchant` role and `is_merchant = true`.

```json
{
  "name": "Merchant Example",
  "email": "merchant@example.com",
  "phone_number": "081234567890",
  "password": "secure-password",
  "confirmed_password": "secure-password"
}
```

Success: HTTP `201`.

### 4.3 Login

```http
POST /login
```

Authentication: not required.

#### JSON body

| Field | Type | Required |
| --- | --- | --- |
| `email` | string | Yes |
| `password` | string | Yes |

```json
{
  "email": "admin@example.com",
  "password": "secure-password"
}
```

#### Success response

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "token": "<JWT_TOKEN>",
    "token_type": "Bearer",
    "expired_at": 1785600000,
    "refreshable_until": 1786550400,
    "account": {
      "id": 1,
      "name": "Admin Example",
      "email": "admin@example.com",
      "phone_number": "081234567890",
      "role_name": "admin",
      "dashboard_path": "/admin/dashboard"
    }
  }
}
```

`expired_at` and `refreshable_until` are Unix timestamps. `dashboard_path` values are:

| Role | Dashboard path |
| --- | --- |
| `admin` | `/admin/dashboard` |
| `merchant` | `/merchant/dashboard` |
| `user` | `/user/dashboard` |

### 4.4 Refresh Token

```http
POST /refresh
Authorization: Bearer <CURRENT_OR_RECENTLY_EXPIRED_TOKEN>
```

No request body is required.

The default access-token lifetime is 4,320 minutes (three days). The default refresh window is 20,160 minutes (fourteen days) from the original login time.

#### Success response

```json
{
  "code": 200,
  "message": "Token refreshed successfully",
  "data": {
    "token": "<NEW_JWT_TOKEN>",
    "token_type": "Bearer",
    "expired_at": 1785600000,
    "refreshable_until": 1786550400,
    "account": {
      "id": 1,
      "role_name": "admin",
      "dashboard_path": "/admin/dashboard"
    }
  }
}
```

The previous token is immediately blacklisted. Replace the stored token before making another request.

### 4.5 Logout

```http
POST /logout
Authorization: Bearer <JWT_TOKEN>
```

No request body is required. The supplied token is blacklisted and cannot be reused.

```json
{
  "code": 200,
  "message": "Logged out successfully",
  "data": null
}
```

### Authentication cURL example

```bash
curl -X POST "$BASE_URL/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "secure-password"
  }'
```

## 5. Admin API

All Admin endpoints require an authenticated `admin` token.

### 5.1 Dashboard

```http
GET /admin/dashboard
```

No query parameters or body.

Returns real database totals and the current Admin:

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "total_users": 10,
    "total_buyers": 6,
    "total_merchants": 3,
    "total_admins": 1,
    "total_locations": 15,
    "admin": {
      "id": 1,
      "name": "Admin Example",
      "email": "admin@example.com",
      "phone_number": "081234567890",
      "role_name": "admin"
    },
    "navigation": {
      "users": "/api/v1/admin/users",
      "locations": "/api/v1/admin/locations"
    }
  }
}
```

### 5.2 List Users

```http
GET /admin/users
```

#### Query parameters

| Parameter | Type | Required | Default | Rules |
| --- | --- | --- | --- | --- |
| `page` | integer | No | `1` | Minimum 1 |
| `limit` | integer | No | `10` | 1–100 |
| `search` | string | No | Empty | Searches name and email |
| `role` | string | No | All roles | `admin`, `user`, or `merchant` |

Example:

```http
GET /admin/users?page=1&limit=10&search=john&role=user
```

Returns pagination metadata and users without passwords or tokens:

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "total_data": 1,
    "page": 1,
    "limit": 10,
    "total_page": 1,
    "data": [
      {
        "id": 12,
        "name": "John Buyer",
        "email": "john@example.com",
        "phone_number": "081234567890",
        "photo": null,
        "role_name": "user",
        "is_merchant": false,
        "is_active": true,
        "created_at": "2026-07-29 10:00:00",
        "updated_at": "2026-07-29 10:00:00"
      }
    ]
  }
}
```

### 5.3 Create User

```http
POST /admin/users
```

#### JSON body

| Field | Type | Required | Validation |
| --- | --- | --- | --- |
| `name` | string | Yes | Maximum 255 characters |
| `email` | string | Yes | Valid, maximum 255 characters, unique |
| `phone_number` | string | Yes | Maximum 255 characters |
| `role_name` | string | Yes | `admin`, `user`, or `merchant` |
| `password` | string | Yes | Minimum 8 characters |
| `confirmed_password` | string | Yes | Must match `password` |

```json
{
  "name": "New Merchant",
  "email": "newmerchant@example.com",
  "phone_number": "081234567890",
  "role_name": "merchant",
  "password": "secure-password",
  "confirmed_password": "secure-password"
}
```

Success: HTTP `201`. `is_merchant` is derived from `role_name` and cannot be assigned independently.

### 5.4 View User

```http
GET /admin/users/{user_id}
```

Path parameter:

| Parameter | Type | Meaning |
| --- | --- | --- |
| `user_id` | integer | Existing user ID |

The response includes non-sensitive account fields and related-record counts:

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "id": 12,
    "name": "John Buyer",
    "email": "john@example.com",
    "phone_number": "081234567890",
    "role_name": "user",
    "is_merchant": false,
    "is_active": true,
    "related_records": {
      "buyer_orders": 3,
      "merchant_orders": 0,
      "menus": 0,
      "temporary_orders": 0,
      "ratings": 2,
      "favorites": 1,
      "wishlists": 0
    }
  }
}
```

### 5.5 Update User

```http
PUT /admin/users/{user_id}
```

#### JSON body

| Field | Type | Required | Validation |
| --- | --- | --- | --- |
| `name` | string | Yes | Maximum 255 characters |
| `email` | string | Yes | Valid and unique except for this user |
| `phone_number` | string | Yes | Maximum 255 characters |
| `role_name` | string | Yes | `admin`, `user`, or `merchant` |
| `is_active` | boolean | Yes | `true` or `false` |
| `password` | string | No | Minimum 8 characters |
| `confirmed_password` | string | When `password` is supplied | Must match `password` |

Without password change:

```json
{
  "name": "Updated User",
  "email": "updated@example.com",
  "phone_number": "089999999999",
  "role_name": "merchant",
  "is_active": true
}
```

With password change:

```json
{
  "name": "Updated User",
  "email": "updated@example.com",
  "phone_number": "089999999999",
  "role_name": "merchant",
  "is_active": true,
  "password": "new-secure-password",
  "confirmed_password": "new-secure-password"
}
```

Omitting `password` preserves the existing password. An Admin cannot deactivate or remove the Admin role from their own account.

### 5.6 Deactivate User

```http
DELETE /admin/users/{user_id}
```

No body.

This is a safe deactivation, not a hard deletion. Orders, menus, ratings, favorites, wishlists, and other historical records remain stored.

An Admin cannot deactivate their own account. That attempt returns HTTP `409`.

### 5.7 List Locations

```http
GET /admin/locations
```

#### Query parameters

| Parameter | Type | Required | Default | Rules |
| --- | --- | --- | --- | --- |
| `page` | integer | No | `1` | Minimum 1 |
| `limit` | integer | No | `10` | 1–100 |
| `search` | string | No | Empty | Searches location name |

### 5.8 Create Location

```http
POST /admin/locations
```

```json
{
  "name": "New Building"
}
```

`name` is required, trimmed, limited to 255 characters, and checked case-insensitively for duplicates.

Success: HTTP `201`.

### 5.9 View Location

```http
GET /admin/locations/{location_id}
```

No query parameters or body.

### 5.10 Update Location

```http
PUT /admin/locations/{location_id}
```

```json
{
  "name": "Updated Building Name"
}
```

The same trimming and duplicate rules as creation apply.

### 5.11 Delete Location

```http
DELETE /admin/locations/{location_id}
```

No body.

If an existing order references the location, deletion is rejected with HTTP `409`:

```json
{
  "code": 409,
  "message": "Location cannot be deleted because it is used by existing orders",
  "errors": {
    "orders": 3
  }
}
```

## 6. Buyer API

All endpoints in this section require an authenticated `user` token.

### 6.1 List All Menus

```http
GET /user/menu/list
```

#### Query parameters

| Parameter | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `page` | number | No | `1` | Page number, greater than 0 |
| `limit` | number | No | `10` | Items per page, greater than 0 |
| `search` | string | No | Empty | Menu-name search |
| `type` | string | No | All | Menu type |

Example:

```http
GET /user/menu/list?page=1&limit=10&search=rice&type=1
```

Each item contains menu ID, merchant name, menu information, price, status, favorite flag, and average rating.

### 6.2 List Menus From One Merchant

```http
GET /user/menu/list/{merchant_id}
```

#### Query parameters

| Parameter | Type | Required | Default |
| --- | --- | --- | --- |
| `page` | number | No | `1` |
| `limit` | number | No | `10` |
| `search` | string | No | Empty |

`merchant_id` must exist in `users`.

### 6.3 View Menu Detail

```http
GET /user/menu/detail/{menu_id}
```

Returns menu and merchant information, nutrition facts, status, favorite flag, price, and average rating.

### 6.4 Menus Under Ten Thousand

```http
GET /user/menu/sepuluh-ribu
```

**Current status: unavailable.** The route is registered, but `User\DashboardController::getSepuluhRibuMenu` is not implemented. Calling it currently produces a server error. Do not integrate this endpoint until the controller method is added.

### 6.5 List Merchants

```http
GET /user/merchant
```

Returns merchant IDs, names, and photos.

### 6.6 Recommended/Top Merchant Menus

```http
GET /user/merchant/top
```

Returns each merchant's highest-ordered menu based on summed order quantities.

### 6.7 View Temporary Cart

```http
GET /user/order/temp
```

Returns the authenticated Buyer's temporary order rows with merchant and menu information.

### 6.8 Add Menu to Temporary Cart

```http
POST /user/order/temp
```

#### JSON body

| Field | Type | Required | Validation |
| --- | --- | --- | --- |
| `menu_id` | number | Yes | Existing menu |
| `quantity` | number | Yes | Numeric |
| `notes` | string | No | Optional notes |

```json
{
  "menu_id": 10,
  "quantity": 2,
  "notes": "No chili"
}
```

The API reads the current menu price and stores `price × quantity` as the temporary row price.

### 6.9 Update Temporary Cart Row

```http
PUT /user/order/temp/{temp_order_id}
```

```json
{
  "quantity": 3,
  "notes": "Extra sauce"
}
```

`quantity` is required and numeric. `notes` is optional. Price is recalculated from the current menu price.

### 6.10 Delete Temporary Cart Row

```http
DELETE /user/order/temp/{temp_order_id}
```

No body.

### 6.11 View Current Order

```http
GET /user/order/current
```

Returns the latest order belonging to the authenticated Buyer.

### 6.12 Create Order

```http
POST /user/order
```

The order lines come from the authenticated Buyer's temporary cart.

#### JSON body

| Field | Type | Required | Validation |
| --- | --- | --- | --- |
| `merchant_id` | number | Yes | Existing user ID |
| `location_id` | number | For delivery (`type = 1`) | Existing location |
| `type` | number | Yes | `1`, `2`, or `3` |
| `payment_method` | number | Yes | `1` or `2` |
| `is_preorder` | boolean | Yes | `true` or `false` |
| `schedule` | date/string | When `is_preorder = true` | Valid date |

Delivery example:

```json
{
  "merchant_id": 5,
  "location_id": 3,
  "type": 1,
  "payment_method": 2,
  "is_preorder": false
}
```

Preorder example:

```json
{
  "merchant_id": 5,
  "type": 2,
  "payment_method": 1,
  "is_preorder": true,
  "schedule": "2026-08-01 12:30:00"
}
```

Success: HTTP `201`. After creation, all temporary cart rows for the Buyer are removed.

### 6.13 Purchase History

```http
GET /user/history
```

Returns the Buyer's orders with total price, creation time, first menu summary, merchant summary, and total menu-row count.

### 6.14 Purchase History Detail

```http
GET /user/history/{order_id}
```

Returns order, user, location, payment, schedule, and all ordered menu rows.

### 6.15 User Order Statistics

```http
GET /user/profile/stat
```

Returns:

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "expense": 125000,
    "total_order": 5
  }
}
```

### 6.16 List Favorite Menus

```http
GET /user/favorite
```

Returns favorite menu IDs, names, images, and types.

### 6.17 Toggle Favorite Menu

```http
PUT /user/favorite/{menu_id}
```

No body. If the favorite exists it is removed; otherwise it is created.

### 6.18 List Delivery Locations

```http
GET /user/locations
```

Returns all location IDs and names.

## 7. Merchant API

All endpoints in this section require an authenticated `merchant` token.

### 7.1 List Merchant Menus

```http
GET /merchant/menu/list
```

#### Query parameters

| Parameter | Type | Required | Description |
| --- | --- | --- | --- |
| `page` | number | See note | Page number |
| `limit` | number | See note | Items per page |
| `type` | string | No | `1` or `2` |

Example:

```http
GET /merchant/menu/list?page=1&limit=10&type=1
```

**Current implementation note:** explicitly provide `page` and `limit`. The controller converts missing values to zero before applying defaults, causing a `422` response when they are omitted.

### 7.2 View Merchant Menu Detail

```http
GET /merchant/menu/detail/{menu_id}
```

Returns the menu fields and average rating.

### 7.3 List Merchant Favorite Menus

```http
GET /merchant/menu/favorite
```

Returns menu ID, image, and favorite flag for menus marked as Merchant favorites.

### 7.4 Create Menu

```http
POST /merchant/menu
Content-Type: multipart/form-data
```

#### Form fields

| Field | Type | Required | Validation |
| --- | --- | --- | --- |
| `name` | string | Yes | Maximum 255 characters |
| `description` | string | Yes | Maximum 255 characters |
| `image` | file | Yes | JPEG or PNG, maximum 2 MB |
| `type` | string | Yes | Use `1` or `2` |
| `nutrition_facts` | string | Yes | Maximum 255 characters |
| `price` | string | Yes | Maximum 255 characters |

cURL:

```bash
curl -X POST "$BASE_URL/merchant/menu" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -F "name=Fried Rice" \
  -F "description=House fried rice" \
  -F "image=@C:/images/fried-rice.jpg" \
  -F "type=1" \
  -F "nutrition_facts=500 kcal" \
  -F "price=25000"
```

The current controller returns HTTP `200` with JSON field `"code": 201`.

### 7.5 Update Menu

```http
PUT /merchant/menu/{menu_id}
Content-Type: multipart/form-data
```

All fields are required:

| Field | Type | Allowed values |
| --- | --- | --- |
| `name` | string | Maximum 255 characters |
| `description` | string | Maximum 255 characters |
| `image` | file | JPEG/PNG, maximum 2 MB |
| `type` | string | `1`, `2` |
| `nutrition_facts` | string | Maximum 255 characters |
| `price` | number | Maximum numeric value 255 under the current validator |
| `status` | string | `1`, `2`, `3` |
| `is_favorite` | string | `0`, `1` |

### 7.6 Delete Menu

```http
DELETE /merchant/menu/{menu_id}
```

No body.

### 7.7 List Orders

```http
GET /merchant/order/list
```

#### Query parameters

| Parameter | Type | Required | Default |
| --- | --- | --- | --- |
| `page` | number | No | `1` |
| `limit` | number | No | `10` |
| `status` | string | No | All; filter accepts `1`, `2`, or `3` |

Returns pagination metadata. Each order contains user, location, bill, order type, payment method, payment status, order status, schedule, preorder flag, and order lines.

### 7.8 View Order Detail

```http
GET /merchant/order/detail/{order_id}
```

Returns order fields and `user_order_list`.

### 7.9 Update Order Status

```http
PUT /merchant/order/status/{order_id}
```

```json
{
  "status": "2"
}
```

Use the documented order status values `1`–`4`.

### 7.10 Mark Order Paid

```http
PUT /merchant/order/payment/{order_id}
```

No body. Sets `is_paid` to `true`.

### 7.11 Today's Income

```http
GET /merchant/report
```

No query parameters or body. Returns the sum of today's paid order bills for the authenticated Merchant.

### 7.12 Daily Paid/Completed Orders

```http
GET /merchant/report/daily
```

#### Query parameters

| Parameter | Type | Required | Default |
| --- | --- | --- | --- |
| `page` | number | No | `1` |
| `limit` | number | No | `10` |

Returns today's completed and paid orders plus today's total income.

### 7.13 Weekly Report

```http
GET /merchant/report/weekly
```

#### Query parameters

| Parameter | Type | Required | Example |
| --- | --- | --- | --- |
| `start_date` | date | Yes | `2026-07-20` |
| `end_date` | date | Yes | `2026-07-26` |

Example:

```http
GET /merchant/report/weekly?start_date=2026-07-20&end_date=2026-07-26
```

Returns daily grouped income/order totals and overall totals:

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "data": [
      {
        "date": "2026-07-20",
        "total_income": "150000",
        "total_order": 6
      }
    ],
    "total_income": 150000,
    "total_order": 6
  }
}
```

## 8. Pagination Format

Paginated list endpoints generally return:

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "total_data": 25,
    "page": 1,
    "limit": 10,
    "total_page": 3,
    "data": []
  }
}
```

The Merchant daily-report endpoint currently returns `report` and `income` without the full pagination metadata.

## 9. JavaScript/Axios Usage

### Create an API client

```js
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api/v1',
    headers: {
        Accept: 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});
```

### Login and store token

```js
const response = await api.post('/login', {
    email: 'admin@example.com',
    password: 'secure-password',
});

localStorage.setItem('token', response.data.data.token);
```

### Refresh and replace token

```js
const response = await api.post('/refresh');
localStorage.setItem('token', response.data.data.token);
```

Do not retry refresh with the old token after a successful response because rotation blacklists it.

### Logout

```js
await api.post('/logout');
localStorage.removeItem('token');
```

## 10. Current Implementation Notes

These notes describe the API exactly as currently implemented:

1. `GET /user/menu/sepuluh-ribu` is registered but has no controller implementation.
2. `GET /merchant/menu/list` should always receive explicit positive `page` and `limit` values.
3. The menu database schema supports types `1` and `2`. Some older request validators also accept `3`; clients should not send `3`.
4. Merchant menu creation returns HTTP `200` even though its JSON `code` is `201`.
5. Merchant menu update currently requires a new image on every update.
6. User deletion through the Admin API means deactivation. The record and its historical relations are retained.
7. JWT logout/refresh requires blacklist support. It is enabled by default and uses the configured Laravel cache store.

## 11. Endpoint Summary

| Method | Endpoint | Role |
| --- | --- | --- |
| `POST` | `/login` | Public |
| `POST` | `/refresh` | Refreshable JWT |
| `POST` | `/logout` | Any authenticated role |
| `POST` | `/user/register` | Public |
| `POST` | `/merchant/register` | Public |
| `GET` | `/admin/dashboard` | Admin |
| `GET`, `POST` | `/admin/users` | Admin |
| `GET`, `PUT`, `DELETE` | `/admin/users/{user_id}` | Admin |
| `GET`, `POST` | `/admin/locations` | Admin |
| `GET`, `PUT`, `DELETE` | `/admin/locations/{location_id}` | Admin |
| `GET` | `/user/menu/list` | Buyer |
| `GET` | `/user/menu/list/{merchant_id}` | Buyer |
| `GET` | `/user/menu/detail/{menu_id}` | Buyer |
| `GET` | `/user/menu/sepuluh-ribu` | Buyer; currently unavailable |
| `GET` | `/user/merchant` | Buyer |
| `GET` | `/user/merchant/top` | Buyer |
| `GET`, `POST` | `/user/order/temp` | Buyer |
| `PUT`, `DELETE` | `/user/order/temp/{temp_order_id}` | Buyer |
| `GET` | `/user/order/current` | Buyer |
| `POST` | `/user/order` | Buyer |
| `GET` | `/user/history` | Buyer |
| `GET` | `/user/history/{order_id}` | Buyer |
| `GET` | `/user/profile/stat` | Buyer |
| `GET` | `/user/favorite` | Buyer |
| `PUT` | `/user/favorite/{menu_id}` | Buyer |
| `GET` | `/user/locations` | Buyer |
| `GET` | `/merchant/menu/list` | Merchant |
| `GET` | `/merchant/menu/detail/{menu_id}` | Merchant |
| `GET` | `/merchant/menu/favorite` | Merchant |
| `POST` | `/merchant/menu` | Merchant |
| `PUT`, `DELETE` | `/merchant/menu/{menu_id}` | Merchant |
| `GET` | `/merchant/order/list` | Merchant |
| `GET` | `/merchant/order/detail/{order_id}` | Merchant |
| `PUT` | `/merchant/order/status/{order_id}` | Merchant |
| `PUT` | `/merchant/order/payment/{order_id}` | Merchant |
| `GET` | `/merchant/report` | Merchant |
| `GET` | `/merchant/report/daily` | Merchant |
| `GET` | `/merchant/report/weekly` | Merchant |
