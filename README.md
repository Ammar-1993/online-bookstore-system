# Online Bookstore System

A modern, RTL-friendly bookstore built on **Laravel 12**, **PHP 8.4**, **MySQL**, **TailwindCSS**, **Livewire/Jetstream**, and **Stripe**.
It includes a public catalog with search, product pages, reviews, a session-based cart, a checkout flow, user “My Orders” pages, an **Admin panel** with role-based access, **orders management** (including refunds via Stripe), **PDF invoices**, and **email notifications** (Mailpit in development).

> Default locale is **Arabic (RTL)**. All UI components are styled for RTL.

---

## Table of contents

* [Features](#features)
* [Tech stack](#tech-stack)
* [Project structure](#project-structure)
* [Prerequisites](#prerequisites)
* [Getting started (Sail/Docker)](#getting-started-saildocker)
* [Configuration](#configuration)

  * [.env essentials](#env-essentials)
  * [Stripe (test mode)](#stripe-test-mode)
  * [Mailpit (development mail)](#mailpit-development-mail)
* [Daily development commands](#daily-development-commands)
* [Database](#database)
* [Auth, roles & policies](#auth-roles--policies)
* [Business flows](#business-flows)

  * [Cart & checkout](#cart--checkout)
  * [Order life-cycle](#order-life-cycle)
  * [Invoices (PDF)](#invoices-pdf)
  * [Email notifications](#email-notifications)
* [Routes overview](#routes-overview)
* [Admin panel](#admin-panel)
* [Testing](#testing)
* [Troubleshooting](#troubleshooting)
* [Roadmap / Next steps](#roadmap--next-steps)
* [License](#license)

---

## Features

* **Public storefront**

  * Home catalog with search & pagination
  * Book details with authors/publisher/category chips
  * User reviews (CRUD) with average rating & count (approval supported)
  * SEO-friendly slugs
* **Cart & checkout**

  * Session-based cart (add/update/remove/clear, currency consistency, stock checks)
  * Checkout creates `Order` (pending/unpaid) + items
  * “Mock pay now” for quick local testing
  * Stripe card payments (test mode) with **webhook** confirmation & **refunds** from Admin
* **Orders**

  * “My Orders” list + details + cancel + invoices
  * PDF invoices (Arabic/RTL compatible)
  * Email notifications for key events (development via Mailpit)
* **Admin panel**

  * Manage Books, Categories, Publishers, Authors, Users
  * Reviews moderation
  * Orders management (filters, status/payment updates, refunds)
* **Security & correctness**

  * Policies for Admin/Seller/User separation
  * Stock is **only** deducted on payment success (`markPaid()` with DB locks)
  * Cancellation re-stocks when appropriate
  * Webhook is CSRF-exempt & idempotent

---

## Tech stack

* **Backend:** PHP 8.4, Laravel 12.x, MySQL
* **Auth/UI:** Jetstream, Fortify, Livewire
* **Frontend:** Blade, Vite, TailwindCSS (RTL ready)
* **Permissions:** `spatie/laravel-permission` (middleware aliases: `role`, `permission`, `role_or_permission`)
* **Payments:** Stripe (PaymentIntents, CLI for webhooks)
* **Mail (dev):** Mailpit
* **PDF:** mPDF (UTF-8, Arabic fonts, RTL)
* **Docker:** Laravel Sail

---

## Screenshot

```

---

## Prerequisites

* **Docker** (for Laravel Sail)
* **Node.js** (optional on host; `sail npm` can be used)
* **Stripe CLI** (for local webhooks)

  * Windows: `winget install --id=Stripe.StripeCli -e`
* **WSL (Windows)** supported. Use **PowerShell** for Stripe CLI (host) and run Sail commands inside the project directory (WSL).

---

## Getting started (Sail/Docker)

1. Install dependencies (composer already included in repo)

```bash
# from project root
cp .env.example .env
```

2. Start containers

```bash
./vendor/bin/sail up -d
```

3. App key, migrations, seeders, storage

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
```

4. Frontend

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

5. Open the app: **[http://localhost:8080](http://localhost:8080)**

---

## Configuration

### .env essentials

```env
APP_URL=http://localhost:8080
APP_PORT=8080
APP_TIMEZONE=Asia/Aden

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=online_bookstore_db
DB_USERNAME=sail
DB_PASSWORD=password

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@bookstore.test"
MAIL_FROM_NAME="${APP_NAME}"

VITE_DEV_SERVER_HOST=0.0.0.0
VITE_PORT=5173

STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxx
```

> **Note:** Sail’s Mailpit UI is at **[http://localhost:8025](http://localhost:8025)** by default.

### Stripe (test mode)

1. Install & login (on host/PowerShell):

```powershell
winget install --id=Stripe.StripeCli -e
stripe login
```

2. Start webhook tunnel (use **127.0.0.1** to avoid host/WSL DNS edge cases):

```powershell
stripe listen --forward-to http://127.0.0.1:8080/payments/stripe/webhook --log-level info
```

Copy the printed `whsec_...` into `.env` as `STRIPE_WEBHOOK_SECRET`.

3. Use Stripe test cards (e.g., `4242 4242 4242 4242`, any future date, any CVC).

> **Important:** The webhook route is CSRF-exempt in `bootstrap/app.php`.

### Mailpit (development mail)

* UI: **[http://localhost:8025](http://localhost:8025)**
* All emails (order placed/paid/cancelled/status updates) are delivered here in development.

---

## Daily development commands

```bash
# run app
./vendor/bin/sail up -d

# clear caches (recommended after env/route/view changes)
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan optimize:clear

# frontend dev server
./vendor/bin/sail npm run dev

# run test suite
./vendor/bin/sail artisan test
```

---

## Database

* **Migrations** create the required tables: `books`, `orders`, `order_items`, `reviews`, …
* `stock_qty` is authoritative for availability.
* Orders store currency and computed totals; inventory is updated **only** when the order is marked paid.

---

## Auth, roles & policies

* Roles via **spatie/laravel-permission**:

  * **Admin**: full back-office access
  * **Seller**: manage own books, moderate reviews for own books
  * **User**: shop & manage own orders/reviews
* Middleware aliases setup in `bootstrap/app.php`: `role`, `permission`, `role_or_permission`
* Key policies:

  * **OrderPolicy**: users can view/update **their own** orders; Admin can view/update all
  * **ReviewPolicy**: create (verified users), update/delete (owner/Admin), moderate (Admin or owning Seller)
  * **BookPolicy**: Admin all, Seller limited to own inventory

> Ensure the `User` model has:

```php
public function orders() { return $this->hasMany(\App\Models\Order::class); }
```

---

## Business flows

### Cart & checkout

* **Cart** (`App\Support\Cart`):

  * Session-based, keyed by `cart.items`
  * `add/update/remove/clear/subtotal/currency`
  * Prevents mixing different currencies
  * Respects `stock_qty`
* **Checkout**:

  * `CheckoutController@store` creates **Order** with status `pending`, `payment_status=unpaid`
  * No stock deduction at this point

### Order life-cycle

1. **pending/unpaid** — after checkout
2. **Stripe payment**

   * `StripeController@createIntent` creates a PaymentIntent
   * User confirms payment on `/payments/stripe/{order}`
3. **Webhook**: `payment_intent.succeeded`

   * `Order::markPaid()` (inside a DB transaction + `lockForUpdate`)
   * Deducts stock, sets `payment_status=paid`, `status=processing`, timestamps (`paid_at`)
4. **Admin actions**

   * Change status (`processing → shipped → …`)
   * **Refund** → `charge.refunded` webhook → `cancelAndRestock()` → `payment_status=refunded`, `status=cancelled`

### Invoices (PDF)

* HTML invoice view + downloadable **PDF** (`/orders/{order}/invoice.pdf`)
* Arabic/RTL support via mPDF (`utf-8`, `dejavusans`, `autoScriptToLang`, `autoLangToFont`)

### Email notifications

* Optional notifications (development → Mailpit):

  * Order placed, paid, cancelled, status updated
* You can attach the generated PDF to the “paid” email if desired.

---

## Routes overview

**Public**

* `/` — home (catalog, search, pagination)
* `/books/{book:slug}` — book details
* `/categories/{category:slug}`, `/publishers/{publisher:slug}`, `/authors/{author:slug}`

**Cart**

* `/cart` GET — list
* `/cart/add/{book:slug}` POST — add
* `/cart/{book:slug}` PATCH — update qty
* `/cart/{book:slug}` DELETE — remove
* `/cart` DELETE — clear

**Checkout / Orders (auth + verified)**

* `/checkout` GET — show cart summary
* `/checkout` POST — create order
* `/checkout/thanks` GET
* `/orders` GET — My Orders
* `/orders/{order}` GET — My Order details
* `/orders/{order}/invoice` GET — HTML invoice
* `/orders/{order}/invoice.pdf` GET — PDF invoice
* `/orders/{order}/cancel` POST — cancel (when allowed)

**Payments**

* `/payments/mock/{order}/success` GET — mock markPaid (local only)
* `/payments/stripe/{order}` GET — Stripe payment page
* `/payments/stripe/{order}/intent` POST — create PaymentIntent
* `/payments/stripe/webhook` POST — Stripe webhook (CSRF-exempt)

**Admin (auth + role: Admin|Seller)**

* `/admin` — dashboard
* `/admin/books|categories|publishers|authors|users`
* `/admin/reviews`
* `/admin/orders` — list (with filters), show, update
* `/admin/orders/{order}/refund` POST — refund (Admin only)

---

## Admin panel

* Orders list with filters: **status**, **payment\_status**, **date range**, **email**
* Details page shows:

  * PaymentIntent id, Charge id, `paid_at`
  * Items with qty & totals
  * Update controls for **order status** and **payment status**
  * **Refund** button (Admin) — triggers Stripe refund + inventory restoration

---

## Testing

* Quick run:

```bash
./vendor/bin/sail artisan test
```

* Suggested `./.env.testing`:

```env
APP_ENV=testing
APP_KEY=base64:testtesttesttesttesttesttesttesttesttesttest=
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
STRIPE_KEY=pk_test_dummy
STRIPE_SECRET=sk_test_dummy
STRIPE_WEBHOOK_SECRET=whsec_test_secret
```

* Feature tests include:

  * Stripe webhook flow (paid/refunded) and inventory adjustments
  * Order policy (ownership/Admin access)
  * Admin orders filters
  * “My Orders” page visibility

---

## Troubleshooting

* **Webhook received but order doesn’t change**

  * Ensure Stripe CLI uses **127.0.0.1** (not localhost):

    ```
    stripe listen --forward-to http://127.0.0.1:8080/payments/stripe/webhook
    ```
  * Check `.env` has correct `STRIPE_WEBHOOK_SECRET`
  * Verify the webhook route is **CSRF-exempt** in `bootstrap/app.php`
  * Check app logs: `./vendor/bin/sail artisan tail`

* **419/CSRF on creating intent**

  * The `fetch` request on the Stripe page uses same-origin relative URL and includes `X-CSRF-TOKEN` — ensure you didn’t change the route or domain.

* **Stock didn’t change**

  * Stock is deducted only on `payment_intent.succeeded` → `Order::markPaid()`
  * Make sure you’re not intercepting the webhook with a wrong secret.

* **Mail not received**

  * Open Mailpit: **[http://localhost:8025](http://localhost:8025)**
  * Verify `MAIL_HOST=mailpit`, `MAIL_PORT=1025`, and `MAIL_MAILER=smtp`.

* **Windows/WSL specifics**

  * Run Stripe CLI from **PowerShell** on the host.
  * Run Sail commands inside WSL project folder.
  * Prefer `127.0.0.1` in webhook forwarding.

* **Compile assets**

  * `./vendor/bin/sail npm run dev`
  * If Vite HMR issues: restart Vite and refresh.

---

## Roadmap / Next steps

* Shipping workflow:

  * `tracking_number` on orders
  * Mark as `shipped` with email “Your order has shipped”
* Sequential invoice numbering (`IN-000001…`)
* Coupons/discounts, taxes, shipping methods
* Inventory reservations / cancellation timeouts
* Full i18n (Arabic/English switch)

---

## License

This project is open-sourced under the MIT license.
