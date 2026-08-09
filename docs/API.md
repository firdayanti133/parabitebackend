# Parabite API Documentation

## 1. Overview

Parabite is a Laravel 12 JSON API for three account roles:

| Role | API value | Responsibilities |
| --- | --- | --- |
| Administrator | `admin` | Dashboard statistics, user management, and location management |
| Buyer | `user` | Browse menus and merchants, manage a temporary cart, create orders, and view history |
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

### Menu image URLs and CORS

Menu image fields such as `image` and `menu_image` contain an absolute URL when an image exists:

```text
https://your-domain.example/api/v1/assets/menu/550e8400-e29b-41d4-a716-446655440000.jpg
```

The asset endpoint is public, returns the image bytes directly, and includes CORS and long-lived cache headers. Clients must use the returned URL as-is; do not prepend the API base URL. A menu without an image returns `null`, so clients may display their normal placeholder.

Production must set the public HTTPS origin correctly:

```dotenv
APP_URL=https://your-domain.example
CORS_ALLOWED_ORIGINS=*
```

`CORS_ALLOWED_ORIGINS` may instead contain a comma-separated allowlist, for example `https://app.example,http://localhost:53218`. After changing environment values, run `php artisan optimize:clear`.

## 2. Common Response Format

Every API response uses the same top-level envelope. Successful responses use:

```json
{
  "code": 200,
  "message": "Success",
  "data": {},
  "error_code": null,
  "errors": null
}
```

Validation failures use HTTP `422`:

```json
{
  "code": 422,
  "message": "Validation Error",
  "data": null,
  "error_code": "VALIDATION_ERROR",
  "errors": {
    "email": [
      "The email field is required."
    ]
  },
  "request_id": "c9bd62be-7848-43ea-a021-bf6071d2d77d"
}
```

All HTTP `4xx` and `5xx` responses include `error_code` for programmatic frontend handling and `request_id` for log correlation. The same request ID is returned in the `X-Request-ID` response header. Clients may send their own `X-Request-ID`; otherwise the backend generates one. Unexpected exceptions are logged server-side, while stack traces, SQL, and exception messages are never returned to clients.

### Stable error codes

| Error code | Meaning |
| --- | --- |
| `VALIDATION_ERROR` | One or more request fields failed validation; inspect `errors` |
| `AUTH_UNAUTHENTICATED` | Authentication is missing or could not be verified |
| `AUTH_TOKEN_EXPIRED` | Access token expired; call `/refresh` within the refresh window |
| `AUTH_TOKEN_INVALID` | Token is malformed, invalid, or blacklisted |
| `AUTH_INVALID_CREDENTIALS` | Email/password combination is incorrect |
| `AUTH_ACCOUNT_INACTIVE` | Account exists but is inactive |
| `AUTH_ROLE_FORBIDDEN` | Authenticated role cannot use this endpoint |
| `AUTH_FORBIDDEN` | Authenticated client is forbidden for another reason |
| `ROUTE_NOT_FOUND` | API path does not exist |
| `RESOURCE_NOT_FOUND` | Requested entity does not exist |
| `METHOD_NOT_ALLOWED` | Path exists but does not support the HTTP method |
| `RESOURCE_CONFLICT` | Operation conflicts with current resource state |
| `DUPLICATE_RESOURCE` | A database unique constraint rejected a duplicate |
| `DATABASE_CONSTRAINT_VIOLATION` | A relational database constraint rejected the operation |
| `FILE_UPLOAD_FAILED` | Uploaded file could not be stored |
| `ASSET_NOT_FOUND` | Requested menu image does not exist on the public disk |
| `ORDER_CART_EMPTY` | Order creation was attempted with an empty cart |
| `ORDER_CART_MERCHANT_MISMATCH` | Cart items do not all belong to the submitted Merchant |
| `LOCATION_IN_USE` | Location is referenced by historical orders |
| `INTERNAL_SERVER_ERROR` | Unexpected backend failure; report `request_id` |

Authentication and authorization failures:

| HTTP status | Meaning |
| --- | --- |
| `401` | Token is missing, invalid, expired, blacklisted, or outside its refresh window |
| `403` | The account is inactive or does not have the required role |
| `404` | Requested Admin-managed user or location was not found |
| `409` | The requested operation conflicts with account or relational-data rules |
| `422` | Request validation failed |
| `500` | Unexpected server error |

### Error response JSON

The examples below are referenced by the endpoint response matrix later in this document.

#### Missing or invalid token — HTTP 401

```json
{
  "code": 401,
  "message": "Unauthorized: Token error",
  "data": null
}
```

An expired token may return:

```json
{
  "code": 401,
  "message": "Unauthorized: Token expired",
  "data": null
}
```

An invalid or blacklisted token may return:

```json
{
  "code": 401,
  "message": "Unauthorized: Invalid token",
  "data": null
}
```

#### Wrong password — HTTP 401

```json
{
  "code": 401,
  "message": "Wrong Email or Password",
  "errors": null
}
```

#### Authenticated with the wrong role — HTTP 403

```json
{
  "code": 403,
  "message": "Forbidden: Insufficient permissions",
  "data": null
}
```

#### Inactive account — HTTP 403

```json
{
  "code": 403,
  "message": "Forbidden: Account is inactive",
  "data": null
}
```

Login uses a slightly different inactive-account message:

```json
{
  "code": 403,
  "message": "Account is inactive",
  "errors": null
}
```

#### Resource not found — HTTP 404

Admin user:

```json
{
  "code": 404,
  "message": "User not found",
  "errors": null
}
```

Admin location:

```json
{
  "code": 404,
  "message": "Location not found",
  "errors": null
}
```

Buyer and Merchant endpoints generally validate missing IDs as HTTP `422`, not `404`.

#### Operation conflict — HTTP 409

Self-deactivation:

```json
{
  "code": 409,
  "message": "You cannot deactivate your own account",
  "errors": null
}
```

Self-demotion or self-deactivation through user update:

```json
{
  "code": 409,
  "message": "You cannot deactivate or remove the Admin role from your own account",
  "errors": null
}
```

Location used by orders:

```json
{
  "code": 409,
  "message": "Location cannot be deleted because it is used by existing orders",
  "errors": {
    "orders": 3
  }
}
```

#### Validation error — HTTP 422

```json
{
  "code": 422,
  "message": "Validation Error",
  "errors": {
    "email": [
      "The email field has already been taken."
    ],
    "role_name": [
      "The selected role name is invalid."
    ]
  }
}
```

Invalid pagination may use a string instead of field-keyed errors in older Buyer/Merchant controllers:

```json
{
  "code": 422,
  "message": "Validation Error",
  "errors": "Page and limit must be greater than 0"
}
```

#### Internal server error — HTTP 500

All endpoints return a sanitized error:

```json
{
  "code": 500,
  "message": "Internal Server Error",
  "data": null,
  "error_code": "INTERNAL_SERVER_ERROR",
  "errors": null,
  "request_id": "c9bd62be-7848-43ea-a021-bf6071d2d77d"
}
```

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
| `3` | Snack |

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

### Queue numbers

`queue_number` is assigned by the backend when an order is confirmed:

- Numbering starts at `1`.
- Each Merchant has an independent queue.
- Each Merchant's queue resets daily.
- The value never changes when order or payment status changes.
- Historical orders created before queue support return `null`.
- Frontends should display `"-"` when `queue_number` is `null`; never generate a replacement number locally.

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
| `phone_number` | string | Yes | 8–15 digits; may start with `+` |
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
| `phone_number` | string | Yes | 8–15 digits; may start with `+` |
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
      "temporary_orders": 0
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
| `phone_number` | string | Yes | 8–15 digits; may start with `+` |
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

This is a safe deactivation, not a hard deletion. Orders, menus, and other historical records remain stored.

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

Each item contains menu ID, merchant name, menu information, price, and status.

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

Returns menu and merchant information, nutrition facts, status, and price.

### 6.4 Menus Under Ten Thousand

```http
GET /user/menu/sepuluh-ribu
```

Returns menus priced at or below `10000` using the standard paginated menu-list response.

Optional query parameters: `page` (default `1`), `limit` (default `10`, maximum `100`), and `search`.

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

Returns the latest order belonging to the authenticated Buyer, including `queue_number`.

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

Success: HTTP `201`. Queue allocation, order creation, order-line creation, and temporary-cart cleanup are committed atomically.

```json
{
  "code": 201,
  "message": "Success",
  "data": {
    "id": 30,
    "queue_number": 12
  }
}
```

The queue number is final for the order and must be displayed directly by the frontend.

### 6.13 Purchase History

```http
GET /user/history
```

Returns the Buyer's orders with `queue_number`, total price, creation time, first menu summary, merchant summary, and total menu-row count.

### 6.14 Purchase History Detail

```http
GET /user/history/{order_id}
```

Returns `queue_number`, order, user, location, payment, schedule, and all ordered menu rows.

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

### 6.16 List Delivery Locations

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
| `page` | number | No | Page number; default `1` |
| `limit` | number | No | Items per page; default `10` |
| `type` | string | No | `1`, `2`, or `3` |

Example:

```http
GET /merchant/menu/list?page=1&limit=10&type=1
```

### 7.2 View Merchant Menu Detail

```http
GET /merchant/menu/detail/{menu_id}
```

Returns the menu fields.

### 7.3 Create Menu

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
| `type` | integer | Yes | `1` food, `2` drink, `3` snack |
| `nutrition_facts` | string | Yes | Maximum 255 characters |
| `price` | integer | Yes | Minimum `1` |

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

Success returns HTTP `201` with `code: 201`, `data: null`, `error_code: null`, and `errors: null`.

### 7.4 Update Menu

```http
PUT /merchant/menu/{menu_id}
Content-Type: multipart/form-data
```

All fields except `image` are required. If `image` is omitted, the existing image is retained:

| Field | Type | Allowed values |
| --- | --- | --- |
| `name` | string | Maximum 255 characters |
| `description` | string | Maximum 255 characters |
| `image` | file | Optional; JPEG/PNG, maximum 2 MB |
| `type` | integer | `1`, `2`, `3` |
| `nutrition_facts` | string | Maximum 255 characters |
| `price` | integer | Minimum `1` |
| `status` | string | `1`, `2`, `3` |

### 7.5 Delete Menu

```http
DELETE /merchant/menu/{menu_id}
```

No body.

### 7.6 List Orders

```http
GET /merchant/order/list
```

#### Query parameters

| Parameter | Type | Required | Default |
| --- | --- | --- | --- |
| `page` | number | No | `1` |
| `limit` | number | No | `10` |
| `status` | string | No | All; filter accepts `1`, `2`, or `3` |

Returns pagination metadata. Each order contains `queue_number`, user, location, bill, order type, payment method, payment status, order status, schedule, preorder flag, and order lines.

### 7.7 View Order Detail

```http
GET /merchant/order/detail/{order_id}
```

Returns order fields, `queue_number`, and `user_order_list`.

### 7.8 Update Order Status

```http
PUT /merchant/order/status/{order_id}
```

```json
{
  "status": "2"
}
```

Use the documented order status values `1`–`4`.

### 7.9 Mark Order Paid

```http
PUT /merchant/order/payment/{order_id}
```

No body. Sets `is_paid` to `true`.

### 7.10 Today's Income

```http
GET /merchant/report
```

No query parameters or body. Returns the sum of today's paid order bills for the authenticated Merchant.

### 7.11 Daily Paid/Completed Orders

```http
GET /merchant/report/daily
```

#### Query parameters

| Parameter | Type | Required | Default |
| --- | --- | --- | --- |
| `page` | number | No | `1` |
| `limit` | number | No | `10` |

Returns today's completed and paid orders plus today's total income.

### 7.12 Weekly Report

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

## 8. Endpoint Response Matrix

The error codes in this table use the complete JSON examples from [Error response JSON](#error-response-json).

### Authentication responses

| Method | Endpoint | Success | Possible errors |
| --- | --- | --- | --- |
| `POST` | `/user/register` | `201`, mutation response with `data: null` | `422`, `500` |
| `POST` | `/merchant/register` | `201`, mutation response with `data: null` | `422`, `500` |
| `POST` | `/login` | `200`, login token response | `401`, `403`, `422`, `500` |
| `POST` | `/refresh` | `200`, refreshed token response | `401`, `403`, `500` |
| `POST` | `/logout` | `200`, mutation response with `data: null` | `401`, `403`, `500` |

### Admin responses

| Method | Endpoint | Success | Possible errors |
| --- | --- | --- | --- |
| `GET` | `/admin/dashboard` | `200`, dashboard response | `401`, `403`, `500` |
| `GET` | `/admin/users` | `200`, paginated Admin user response | `401`, `403`, `422`, `500` |
| `POST` | `/admin/users` | `201`, Admin user object | `401`, `403`, `422`, `500` |
| `GET` | `/admin/users/{user_id}` | `200`, Admin user detail response | `401`, `403`, `404`, `500` |
| `PUT` | `/admin/users/{user_id}` | `200`, Admin user object | `401`, `403`, `404`, `409`, `422`, `500` |
| `DELETE` | `/admin/users/{user_id}` | `200`, deactivation response | `401`, `403`, `404`, `409`, `500` |
| `GET` | `/admin/locations` | `200`, paginated location response | `401`, `403`, `422`, `500` |
| `POST` | `/admin/locations` | `201`, location object | `401`, `403`, `422`, `500` |
| `GET` | `/admin/locations/{location_id}` | `200`, location object | `401`, `403`, `404`, `500` |
| `PUT` | `/admin/locations/{location_id}` | `200`, location object | `401`, `403`, `404`, `422`, `500` |
| `DELETE` | `/admin/locations/{location_id}` | `200`, mutation response with `data: null` | `401`, `403`, `404`, `409`, `500` |

### Buyer responses

| Method | Endpoint | Success | Possible errors |
| --- | --- | --- | --- |
| `GET` | `/user/menu/list` | `200`, paginated Buyer menu response | `401`, `403`, `422`, `500` |
| `GET` | `/user/menu/list/{merchant_id}` | `200`, paginated merchant-menu response | `401`, `403`, `422`, `500` |
| `GET` | `/user/menu/detail/{menu_id}` | `200`, Buyer menu detail response | `401`, `403`, `422`, `500` |
| `GET` | `/user/menu/sepuluh-ribu` | `200`, paginated menus priced at or below `10000` | `401`, `403`, `422`, `500` |
| `GET` | `/user/merchant` | `200`, merchant array | `401`, `403`, `422`, `500` |
| `GET` | `/user/merchant/top` | `200`, recommended menu collection | `401`, `403`, `422`, `500` |
| `GET` | `/user/order/temp` | `200`, temporary-cart array | `401`, `403`, `422`, `500` |
| `POST` | `/user/order/temp` | `200`, mutation response with `data: null` | `401`, `403`, `422`, `500` |
| `PUT` | `/user/order/temp/{temp_order_id}` | `200`, mutation response with `data: null` | `401`, `403`, `422`, `500` |
| `DELETE` | `/user/order/temp/{temp_order_id}` | `200`, mutation response with `data: null` | `401`, `403`, `422`, `500` |
| `GET` | `/user/order/current` | `200`, current order object or `null` | `401`, `403`, `422`, `500` |
| `POST` | `/user/order` | `201`, order ID and assigned queue number | `401`, `403`, `422`, `500` |
| `GET` | `/user/history` | `200`, purchase-history array | `401`, `403`, `422`, `500` |
| `GET` | `/user/history/{order_id}` | `200`, purchase-history detail array | `401`, `403`, `422`, `500` |
| `GET` | `/user/profile/stat` | `200`, user-statistics response | `401`, `403`, `422`, `500` |
| `GET` | `/user/locations` | `200`, location array | `401`, `403`, `422`, `500` |

### Merchant responses

| Method | Endpoint | Success | Possible errors |
| --- | --- | --- | --- |
| `GET` | `/merchant/menu/list` | `200`, paginated Merchant menu response | `401`, `403`, `422`, `500` |
| `GET` | `/merchant/menu/detail/{menu_id}` | `200`, Merchant menu detail response | `401`, `403`, `422`, `500` |
| `POST` | `/merchant/menu` | `201`, mutation response with `data: null` | `401`, `403`, `422`, `500` |
| `PUT` | `/merchant/menu/{menu_id}` | `200`, mutation response with `data: null` | `401`, `403`, `422`, `500` |
| `DELETE` | `/merchant/menu/{menu_id}` | `200`, mutation response with `data: null` | `401`, `403`, `422`, `500` |
| `GET` | `/merchant/order/list` | `200`, paginated Merchant order response | `401`, `403`, `422`, `500` |
| `GET` | `/merchant/order/detail/{order_id}` | `200`, Merchant order detail response | `401`, `403`, `422`, `500` |
| `PUT` | `/merchant/order/status/{order_id}` | `200`, mutation response with `data: null` | `401`, `403`, `422`, `500` |
| `PUT` | `/merchant/order/payment/{order_id}` | `200`, mutation response with `data: null` | `401`, `403`, `422`, `500` |
| `GET` | `/merchant/report` | `200`, integer income response | `401`, `403`, `422`, `500` |
| `GET` | `/merchant/report/daily` | `200`, daily report response | `401`, `403`, `422`, `500` |
| `GET` | `/merchant/report/weekly` | `200`, weekly report response | `401`, `403`, `422`, `500` |

## 9. Detailed Success Response JSON

### 9.1 Empty mutation response

Used by registration, logout, cart mutation, Merchant menu mutation, Merchant order updates, and successful location deletion. The HTTP status may be `200` or `201`.

```json
{
  "code": 200,
  "message": "Success",
  "data": null
}
```

Registration uses:

```json
{
  "code": 201,
  "message": "Success",
  "data": null
}
```

Merchant menu creation returns HTTP `201` with:

```json
{
  "code": 201,
  "message": "Success",
  "data": null
}
```

### 9.2 Admin user list

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "total_data": 2,
    "page": 1,
    "limit": 10,
    "total_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Admin Example",
        "email": "admin@example.com",
        "phone_number": "081234567890",
        "photo": null,
        "role_name": "admin",
        "is_merchant": false,
        "is_active": true,
        "created_at": "2026-07-30 08:00:00",
        "updated_at": "2026-07-30 08:00:00"
      }
    ]
  }
}
```

### 9.3 Admin user create/update

```json
{
  "code": 201,
  "message": "Success",
  "data": {
    "id": 20,
    "name": "New Merchant",
    "email": "newmerchant@example.com",
    "phone_number": "081234567890",
    "photo": null,
    "role_name": "merchant",
    "is_merchant": true,
    "is_active": true,
    "created_at": "2026-07-30 08:00:00",
    "updated_at": "2026-07-30 08:00:00"
  }
}
```

Update has the same shape with HTTP and JSON code `200`.

### 9.4 Admin user deactivation

```json
{
  "code": 200,
  "message": "User account deactivated successfully",
  "data": {
    "id": 20,
    "is_active": false,
    "related_records_preserved": true
  }
}
```

### 9.5 Admin location list

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
        "id": 3,
        "name": "Laboratorium Komputer",
        "created_at": "2026-07-30 08:00:00",
        "updated_at": "2026-07-30 08:00:00"
      }
    ]
  }
}
```

### 9.6 Admin location create/view/update

```json
{
  "code": 201,
  "message": "Success",
  "data": {
    "id": 16,
    "name": "New Building",
    "created_at": "2026-07-30 08:00:00",
    "updated_at": "2026-07-30 08:00:00"
  }
}
```

View and update have the same `data` shape with HTTP and JSON code `200`.

### 9.7 Buyer menu list

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
        "menu_id": 10,
        "merchant_name": "Merchant Example",
        "menu_name": "Fried Rice",
        "menu_description": "House fried rice",
        "menu_image": "https://your-domain.example/api/v1/assets/menu/example.jpg",
        "menu_type": "1",
        "menu_price": 25000,
        "menu_status": "1"
      }
    ]
  }
}
```

### 9.8 Buyer merchant-menu list

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
        "id": 10,
        "name": "Fried Rice",
        "description": "House fried rice",
        "image": "https://your-domain.example/api/v1/assets/menu/example.jpg",
        "type": "1",
        "price": 25000,
        "status": "1"
      }
    ]
  }
}
```

### 9.9 Buyer menu detail

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "menu_id": 10,
    "merchant_id": 5,
    "merchant_name": "Merchant Example",
    "menu_name": "Fried Rice",
    "menu_description": "House fried rice",
    "menu_image": "https://your-domain.example/api/v1/assets/menu/example.jpg",
    "menu_type": "1",
    "menu_price": 25000,
    "menu_status": "1",
    "menu_nutrition_facts": "500 kcal"
  }
}
```

### 9.10 Buyer merchant list

```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "id": 5,
      "name": "Merchant Example",
      "photo": null
    }
  ]
}
```

### 9.11 Buyer recommended menus

The collection is keyed by Merchant ID:

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "5": {
      "merchant_id": 5,
      "menu_id": 10,
      "total_quantity": "25",
      "top_menu": "Fried Rice"
    }
  }
}
```

### 9.12 Buyer temporary cart

```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "id": 8,
      "menu_id": 10,
      "merchant_id": 5,
      "merchant_name": "Merchant Example",
      "menu_name": "Fried Rice",
      "menu_image": "https://your-domain.example/api/v1/assets/menu/example.jpg",
      "price": 50000,
      "quantity": 2,
      "notes": "No chili"
    }
  ]
}
```

### 9.13 Buyer current order

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "id": 30,
    "queue_number": 12,
    "user_id": 7,
    "merchant_id": 5,
    "location_id": 3,
    "bill": 50000,
    "type": "1",
    "payment_method": "2",
    "status": "1",
    "schedule": null,
    "is_preorder": 0,
    "created_at": "2026-07-30 08:00:00",
    "updated_at": "2026-07-30 08:00:00",
    "is_paid": 0
  }
}
```

When the Buyer has no order:

```json
{
  "code": 200,
  "message": "Success",
  "data": null
}
```

### 9.14 Buyer purchase history

```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "id": 30,
      "queue_number": 12,
      "total_price": 50000,
      "created_at": "2026-07-30 08:00:00",
      "total_menu": 1,
      "menu_name": "Fried Rice",
      "merchant_name": "Merchant Example",
      "menu_image": "https://your-domain.example/api/v1/assets/menu/example.jpg",
      "menu_type": "1",
      "price": 50000,
      "quantity": 2,
      "notes": "No chili"
    }
  ]
}
```

### 9.15 Buyer purchase history detail

```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "id": 30,
      "queue_number": 12,
      "user_name": "Buyer Example",
      "location_name": "Laboratorium Komputer",
      "total_price": 50000,
      "type": "1",
      "payment_method": "2",
      "status": "1",
      "schedule": null,
      "is_preorder": 0,
      "order_list": [
        {
          "id": 40,
          "order_id": 30,
          "menu_id": 10,
          "menu_name": "Fried Rice",
          "merchant_name": "Merchant Example",
          "menu_image": "https://your-domain.example/api/v1/assets/menu/example.jpg",
          "menu_type": "1",
          "price": 50000,
          "quantity": 2,
          "notes": "No chili"
        }
      ]
    }
  ]
}
```

### 9.16 Buyer locations

```json
{
  "code": 200,
  "message": "Success",
  "data": [
    {
      "id": 3,
      "name": "Laboratorium Komputer"
    }
  ]
}
```

### 9.17 Merchant menu list

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
        "id": 10,
        "name": "Fried Rice",
        "type": "1",
        "description": "House fried rice",
        "image": "https://your-domain.example/api/v1/assets/menu/example.jpg",
        "price": 25000
      }
    ]
  }
}
```

### 9.18 Merchant menu detail

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "id": 10,
    "name": "Fried Rice",
    "description": "House fried rice",
    "image": "https://your-domain.example/api/v1/assets/menu/example.jpg",
    "type": "1",
    "nutrition_facts": "500 kcal",
    "price": 25000,
    "status": "1"
  }
}
```

### 9.19 Merchant order list

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
        "id": 30,
        "queue_number": 12,
        "user_name": "Buyer Example",
        "location_name": "Laboratorium Komputer",
        "bill": 50000,
        "type": "1",
        "payment_method": "2",
        "is_paid": 0,
        "status": "1",
        "schedule": null,
        "is_preorder": 0,
        "user_order_list": [
          {
            "id": 40,
            "order_id": 30,
            "menu_id": 10,
            "menu_name": "Fried Rice",
            "price": 50000,
            "quantity": 2,
            "notes": "No chili"
          }
        ]
      }
    ]
  }
}
```

### 9.20 Merchant order detail

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "id": 30,
    "queue_number": 12,
    "user_id": 7,
    "merchant_id": 5,
    "location_id": 3,
    "bill": 50000,
    "type": "1",
    "payment_method": "2",
    "status": "1",
    "schedule": null,
    "is_preorder": 0,
    "user_order_list": [
      {
        "id": 40,
        "order_id": 30,
        "menu_id": 10,
        "price": 50000,
        "quantity": 2,
        "notes": "No chili"
      }
    ]
  }
}
```

### 9.21 Merchant income

```json
{
  "code": 200,
  "message": "Success",
  "data": 150000
}
```

### 9.22 Merchant daily report

```json
{
  "code": 200,
  "message": "Success",
  "data": {
    "report": [
      {
        "id": 30,
        "queue_number": 12,
        "user_id": 7,
        "merchant_id": 5,
        "location_id": 3,
        "order_number": 1,
        "bill": 50000,
        "type": "1",
        "payment_method": "2",
        "status": "3",
        "schedule": null,
        "is_preorder": 0,
        "created_at": "2026-07-30 08:00:00"
      }
    ],
    "income": 50000
  }
}
```

The weekly-report success JSON is shown in [Weekly Report](#713-weekly-report).

## 10. Pagination Format

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

## 11. JavaScript/Axios Usage

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

## 12. Testing Queue Numbers

### 12.1 Run the automated queue tests

The focused feature test covers:

- First order receives queue number `1`.
- Second order for the same Merchant and day receives `2`.
- Queue numbering resets the next day.
- Different Merchants have independent queues.
- Rapid allocations do not duplicate numbers.
- The daily counter has a unique Merchant/date record.
- Order creation and order-query APIs return `queue_number`.
- Queue numbers remain unchanged after status and payment updates.

With PHP 8.2+ and SQLite extensions enabled:

```bash
php artisan test --filter=OrderQueueNumberTest
```

If the active Laragon PHP installation does not have SQLite enabled, run PHPUnit using the installed extension DLLs:

```powershell
C:\laragon\bin\php\php-8.2.27-nts-Win32-vs16-x64\php.exe `
  -d extension=pdo_sqlite `
  -d extension=sqlite3 `
  vendor/bin/phpunit tests/Feature/OrderQueueNumberTest.php
```

### 12.2 Prepare for manual testing

Apply the queue migrations and start Laravel:

```bash
php artisan migrate
php artisan serve
```

Use this base URL:

```text
http://localhost:8000/api/v1
```

The test requires:

1. An active Buyer account.
2. An active Merchant account.
3. A menu belonging to that Merchant.
4. The Buyer and Merchant JWT tokens.

### 12.3 Log in as the Buyer

```http
POST /api/v1/login
Accept: application/json
Content-Type: application/json
```

```json
{
  "email": "buyer@example.com",
  "password": "secure-password"
}
```

Save `data.token` from the response as `BUYER_TOKEN`.

### 12.4 Add a menu to the temporary cart

Replace `10` with a real menu ID:

```http
POST /api/v1/user/order/temp
Accept: application/json
Content-Type: application/json
Authorization: Bearer BUYER_TOKEN
```

```json
{
  "menu_id": 10,
  "quantity": 1,
  "notes": "Queue test"
}
```

### 12.5 Confirm the first order

Replace `5` with the Merchant account ID associated with the selected menu:

```http
POST /api/v1/user/order
Accept: application/json
Content-Type: application/json
Authorization: Bearer BUYER_TOKEN
```

```json
{
  "merchant_id": 5,
  "type": 2,
  "payment_method": 1,
  "is_preorder": false
}
```

Expected response:

```json
{
  "code": 201,
  "message": "Success",
  "data": {
    "id": 30,
    "queue_number": 1
  }
}
```

### 12.6 Confirm the second order

Add another menu to the temporary cart and call `POST /user/order` again using the same Merchant.

Expected queue portion:

```json
{
  "data": {
    "queue_number": 2
  }
}
```

Creating an order for a different Merchant should return queue number `1` for that Merchant.

### 12.7 Verify Buyer order responses

Call:

```http
GET /api/v1/user/order/current
Authorization: Bearer BUYER_TOKEN
```

```http
GET /api/v1/user/history
Authorization: Bearer BUYER_TOKEN
```

```http
GET /api/v1/user/history/{order_id}
Authorization: Bearer BUYER_TOKEN
```

Every returned order should contain:

```json
{
  "queue_number": 2
}
```

Historical orders created before the queue migration return:

```json
{
  "queue_number": null
}
```

The mobile client should render a null queue number as `"-"`.

### 12.8 Verify Merchant order responses

Log in as the Merchant and save `data.token` as `MERCHANT_TOKEN`.

Call:

```http
GET /api/v1/merchant/order/list
Authorization: Bearer MERCHANT_TOKEN
```

```http
GET /api/v1/merchant/order/detail/{order_id}
Authorization: Bearer MERCHANT_TOKEN
```

Both responses should contain the same `queue_number` assigned during order confirmation.

Update the order status and payment:

```http
PUT /api/v1/merchant/order/status/{order_id}
Authorization: Bearer MERCHANT_TOKEN
Content-Type: application/json
```

```json
{
  "status": "3"
}
```

```http
PUT /api/v1/merchant/order/payment/{order_id}
Authorization: Bearer MERCHANT_TOKEN
```

Fetch the order again and verify that `queue_number` has not changed.

### 12.9 Verify database state

Inspect stored orders:

```sql
SELECT id, merchant_id, queue_number, status, is_paid, created_at
FROM user_orders
ORDER BY id;
```

Inspect the daily counters:

```sql
SELECT merchant_id, queue_date, last_number
FROM merchant_daily_queue_counters
ORDER BY queue_date, merchant_id;
```

Expected behavior:

- One counter row exists for each Merchant/date pair.
- `last_number` matches the latest number allocated that day.
- Different Merchants have separate counters.
- The automated test uses a simulated clock to verify next-day reset behavior without waiting until the following day.

## 13. Current Implementation Notes

These notes describe the API exactly as currently implemented:

1. Menu type `3` is supported for snacks. Apply all pending migrations before sending snack creation requests.
2. Merchant menu images are stored on the `public` disk under `img/menu` and served through `/api/v1/assets/menu/{filename}`. The API endpoint does not require a public storage symlink, but `storage/app/public` must be readable and writable by the PHP process.
3. Migration `2026_08_03_000001_remove_unused_menu_engagement_features` permanently deletes existing menu ratings, Buyer wishlists, and Buyer favorite-menu records. Back up that data before migration if it may be needed later.
4. Rating, wishlist, and favorite-menu APIs and response fields are no longer supported. Removed URLs return `404` or `405` depending on whether a parameterized route matches the same path.
5. User deletion through the Admin API means deactivation. The record and its historical relations are retained.
6. JWT logout/refresh requires blacklist support. It is enabled by default and uses the configured Laravel cache store.
7. Historical orders are not backfilled with potentially misleading queue values. They return `"queue_number": null`; mobile clients should display `"-"`.
8. This repository does not contain the mobile frontend. The mobile order-confirmation, current-order, history, and Merchant order screens must read the documented `queue_number` field instead of a placeholder.

## 14. Endpoint Summary

| Method | Endpoint | Role |
| --- | --- | --- |
| `POST` | `/login` | Public |
| `POST` | `/refresh` | Refreshable JWT |
| `POST` | `/logout` | Any authenticated role |
| `GET` | `/assets/menu/{filename}` | Public |
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
| `GET` | `/user/menu/sepuluh-ribu` | Buyer |
| `GET` | `/user/merchant` | Buyer |
| `GET` | `/user/merchant/top` | Buyer |
| `GET`, `POST` | `/user/order/temp` | Buyer |
| `PUT`, `DELETE` | `/user/order/temp/{temp_order_id}` | Buyer |
| `GET` | `/user/order/current` | Buyer |
| `POST` | `/user/order` | Buyer |
| `GET` | `/user/history` | Buyer |
| `GET` | `/user/history/{order_id}` | Buyer |
| `GET` | `/user/profile/stat` | Buyer |
| `GET` | `/user/locations` | Buyer |
| `GET` | `/merchant/menu/list` | Merchant |
| `GET` | `/merchant/menu/detail/{menu_id}` | Merchant |
| `POST` | `/merchant/menu` | Merchant |
| `PUT`, `DELETE` | `/merchant/menu/{menu_id}` | Merchant |
| `GET` | `/merchant/order/list` | Merchant |
| `GET` | `/merchant/order/detail/{order_id}` | Merchant |
| `PUT` | `/merchant/order/status/{order_id}` | Merchant |
| `PUT` | `/merchant/order/payment/{order_id}` | Merchant |
| `GET` | `/merchant/report` | Merchant |
| `GET` | `/merchant/report/daily` | Merchant |
| `GET` | `/merchant/report/weekly` | Merchant |
