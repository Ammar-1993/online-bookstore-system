# Online Bookstore System

A modern, RTL-ready (Arabic) online bookstore built with **Laravel 12**, featuring a product catalog, cart & checkout, **Stripe** payments (test mode), invoice PDFs, email notifications via **Mailpit**, role-based admin panel (Admin/Seller/Customer), and a clean Tailwind CSS UI.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red) ![PHP](https://img.shields.io/badge/PHP-8.2+-purple) ![License](https://img.shields.io/badge/License-MIT-green)

---

## Table of Contents

- [Screenshots](#screenshots)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Quick Start (TL;DR)](#quick-start-tldr)
- [Option A (Recommended): Docker via Laravel Sail](#option-a-recommended-docker-via-laravel-sail)
  - [Step 1 — Clone & configure](#step-1--clone--configure)
  - [Step 2 — Boot services](#step-2--boot-services)
  - [Step 3 — App key, storage, and migrations](#step-3--app-key-storage-and-migrations)
  - [Step 4 — Frontend assets](#step-4--frontend-assets)
  - [Step 5 — Stripe (test mode) & webhook](#step-5--stripe-test-mode--webhook)
  - [Useful Sail commands](#useful-sail-commands)
  - [Windows + WSL2 tips](#windows--wsl2-tips)
- [Option B: Native (No Docker)](#option-b-native-no-docker)
- [Environment Variables](#environment-variables)
- [Mailpit (Local Email)](#mailpit-local-email)
- [Users & Roles](#users--roles)
- [Queues (Optional but Recommended)](#queues-optional-but-recommended)
- [Common Tasks](#common-tasks)
- [Troubleshooting](#troubleshooting)
- [Production Notes](#production-notes)
- [Project Structure](#project-structure)
- [Contributing](#contributing)
- [Security](#security)
- [License](#license)

---

## Screenshots

> *🔐 Home Interface:
The login interface provides users with secure access to the system through a clean and intuitive Arabic user interface. It includes fields for entering an email address and password, a password visibility toggle, and a “Forgot Password?” option for easy recovery. The interface supports multilingual access (Arabic and English) and aligns with modern UI principles to ensure usability across devices.*
> <img width="1920" height="1312" alt="image" src="docs/screenshots/home.png" />


- [Home] (Landing & Featured)
- [Route]: /
- [Access]: Public
- [Purpose]: First contact with the bookstore; highlight featured/new books and entry paths.
- [Capture]: Top navigation, hero (if any), featured books grid, footer.
- [KeyUI]: Search, categories link, book cards (cover, title, price, currency), RTL layout.
- [Link]: 
________________________________________
2) Category Details
Route: /categories/{slug}
Access: Public
Purpose: Browse books by category.
Capture: Category title/description, filter/sort (if present), paginated grid.
Key UI: Book cards, RTL pagination, breadcrumbs back to Home.
________________________________________
3) Publisher Details
Route: /publishers/{slug}
Access: Public
Purpose: Showcase books from a specific publisher.
Capture: Publisher header (name/logo if available), books list, pagination.
Key UI: Book cards with price/currency, consistent RTL spacing.
________________________________________
4) Author Details
Route: /authors/{slug}
Access: Public
Purpose: Show an author profile and books by that author.
Capture: Author name/bio (if present), books grid.
Key UI: Book cards, RTL typography and avatars (if used).
________________________________________
5) Book Details (+ Reviews)
Route: /books/{slug}
Access: Public
Purpose: Product page for a book; add to cart; view/add reviews.
Capture: Cover, title, author/publisher, price/currency, stock, add-to-cart button, reviews block.
Key UI: Star rating display, review list (approved only), “write/update review” (for verified users), related books strip.
________________________________________
6) Cart
Route: /cart
Access: Public
Purpose: Review items, edit quantities, remove/clear, proceed to checkout.
Capture: Table of items (cover, title, unit price, qty, line total), totals box, “Proceed to Checkout”.
Key UI: Quantity update form, delete buttons, empty-state card, RTL table.
________________________________________
7) Checkout
Route: /checkout
Access: Authenticated
Purpose: Capture shipping/billing details and confirm order before payment.
Capture: Shipping form, summary of items, totals, “Pay with Stripe (test)” CTA.
Key UI: Validation hints, RTL forms, loader on submit.
________________________________________
8) Payment (Stripe)
Route: /orders/{order}/pay (or payments/stripe/pay)
Access: Authenticated (authorized for the order)
Purpose: Create a PaymentIntent and confirm payment via Stripe (test cards).
Capture: Stripe card element, “Pay” button, short instructions (test card 4242…), feedback state.
Key UI: Loader during confirmation, graceful error handling, redirect to order after success.
________________________________________
9) Thank You (Order Placed)
Route: /checkout/thank-you (if used)
Access: Authenticated
Purpose: Post-purchase confirmation with next steps.
Capture: Success state, short summary, link to “My Orders”.
Key UI: RTL success banner, order number reference.
________________________________________
Customer Account
10) My Orders (List)
Route: /orders
Access: Authenticated
Purpose: Paginated list of the user’s orders.
Capture: ID/number, created date, payment status pill, order status pill, total, “View” link.
Key UI: RTL table, pagination, empty-state.
________________________________________
11) Order Details (Customer)
Route: /orders/{order}
Access: Authenticated & authorized
Purpose: Full order breakdown and customer actions.
Capture: Header (number, date), payment/status pills, line items with totals, actions row.
Key UI: “Pay with Stripe” (if payable), “Cancel” (if cancelable), “View Invoice”, “Download PDF”.
________________________________________
12) Invoice (HTML)
Route: /orders/{order}/invoice
Access: Authenticated & authorized
Purpose: Printable invoice view (RTL).
Capture: Seller/buyer blocks, items table, totals, currency, order meta.
Key UI: Clean RTL print-ready layout.
________________________________________
13) Invoice PDF (Download)
Route: /orders/{order}/invoice/pdf
Access: Authenticated & authorized
Purpose: PDF version generated with mPDF (RTL fonts, Arabic).
Capture: N/A (download), but you can show a preview screenshot of the PDF opened in a viewer.
Key UI: Proper Arabic glyph shaping, right-to-left page direction.
________________________________________
Admin (Dashboard & Management)
14) Admin Dashboard
Route: /admin
Access: Roles: Admin, Seller
Purpose: Overview & navigation hub for management sections.
Capture: Top bar, quick stats (if present), links to Books, Orders, Reviews, etc.
Key UI: RTL admin header, role-aware nav.
________________________________________
15) Admin Books – Index
Route: /admin/books
Access: Roles: Admin, Seller (policy-filtered)
Purpose: Manage books; search/filter and quick actions.
Capture: List/grid of books, “Create Book” CTA.
Key UI: Status (“draft/published”), stock, price, edit/delete buttons.
________________________________________
16) Admin Books – Create/Edit
Route: /admin/books/create, /admin/books/{book}/edit
Access: Roles: Admin, Seller
Purpose: CRUD form for book metadata and pricing.
Capture: Form sections (title/slug, ISBN, author text, category/publisher, price/currency, stock, cover upload, status).
Key UI: Validation, RTL forms, submit with loader.
________________________________________
17) Admin Categories
Route: /admin/categories (+ create/edit)
Access: Admin
Purpose: Manage categories used for browsing.
Capture: Index table and create/edit form.
Key UI: Name, slug, counts, CRUD actions.
________________________________________
18) Admin Publishers
Route: /admin/publishers (+ create/edit)
Access: Admin
Purpose: Manage publishers and their slugs.
Capture: Index table and create/edit form.
Key UI: Name, slug, CRUD actions.
________________________________________
19) Admin Authors
Route: /admin/authors (+ create/edit)
Access: Admin
Purpose: Manage authors linked to books.
Capture: Index table and create/edit form.
Key UI: Name, slug, CRUD actions.
________________________________________
20) Admin Users
Route: /admin/users (+ edit)
Access: Admin
Purpose: Manage users and roles.
Capture: Users table and role assignment UI.
Key UI: Name, email, role badges (Admin/Seller/Customer), status.
________________________________________
21) Admin Reviews (Moderation)
Route: /admin/reviews
Access: Admin (and Seller sees reviews on own books)
Purpose: Search/filter and approve/deny reviews.
Capture: Filters (approved/pending, query), reviews list with book/user, toggle action.
Key UI: Status toggle, delete, pagination.
________________________________________
22) Admin Orders – Index (With Filters)
Route: /admin/orders
Access: Roles: Admin, Seller (policy-filtered)
Purpose: Search & filter orders at scale.
Capture: Filters row (Status, Payment Status, Date From/To, Email), table of orders, pagination.
Key UI: Payment/status pills, user email, created date, “View” link.
Suggested shot: One image with filters expanded and results visible.
________________________________________
23) Admin Order – Details & Actions
Route: /admin/orders/{order}
Access: Roles: Admin, Seller (authorized)
Purpose: Inspect order and perform actions.
Capture: Header (payment/status, user), Payment Intent/Charge blocks, items & totals, actions section.
Key UI:
•	Update status (pending, processing, shipped, cancelled)
•	Update payment status (unpaid, paid, refunded)
•	Refund button (when paid)
•	Mark Shipped (tracking number, carrier, shipped_at), shows tracking URL
________________________________________
Authentication & Errors
24) Sign In / Register / Email Verification
Routes: /login, /register, /email/verify
Access: Public
Purpose: Account access and verification (Jetstream/Fortify).
Capture: RTL form fields, buttons, validation messages.
Key UI: Clean RTL layout, password rules, verification prompt.
________________________________________
25) 403 – Forbidden
Route: /errors/403 (view)
Access: Public (error state)
Purpose: Friendly error with RTL copy.
Capture: The styled error card.
Key UI: Back/Go Home link.
________________________________________
Transactional Emails (Mailpit Previews)
Use Mailpit to preview; capture one desktop screenshot per template in Arabic RTL.
26) Order Placed (RTL)
Template: resources/views/emails/orders/placed.blade.php
Purpose: Confirmation of order creation before payment.
Capture: Subject line (RTL), order summary, “View Order” button.
27) Order Paid (RTL + Invoice PDF)
Template: resources/views/emails/orders/paid.blade.php
Purpose: Payment confirmation; includes invoice PDF attachment.
Capture: Greeting, order details, “View Order” button; show Mailpit header with subject in RTL.
28) Order Shipped (RTL + Tracking)
Template: resources/views/emails/orders/shipped.blade.php
Purpose: Shipping notice with carrier, tracking number, link.
Capture: Body with tracking info, action button.
29) Order Cancelled
Template: resources/views/emails/orders/cancelled.blade.php
Purpose: Cancellation/refund notice.
Capture: Reason copy (if provided), support link.
30) Order Status Updated
Template: resources/views/emails/orders/status_updated.blade.php
Purpose: Inform user of status transitions (e.g., processing → shipped).
Capture: Old/new statuses, CTA.
________________________________________
Shared UI Components
31) Global Page Loader (Full-screen)
View: resources/views/components/page-loader.blade.php
Purpose: Show a blocking loader on submits/links; adds ripple effect.
Capture: Overlay with spinner and “جارٍ التحميل…” text (RTL).
Key UI: Semi-transparent backdrop, accessible aria tags.
________________________________________
32) Center Loader (Inline/Modal Style)
View: resources/views/components/center-loader.blade.php (optional)
Purpose: Use inside a card or modal (non-blocking).
Capture: Single card with spinner + caption centered.
Key UI: Works with data-loading="center" attributes.
________________________________________
33) RTL Pagination
View: resources/views/vendor/pagination/tailwind-rtl.blade.php
Purpose: Proper right-to-left pagination arrows and alignment.
Capture: Pagination bar under a list/grid in RTL.
Key UI: Active page state, hover states.
________________________________________
34) Flash Messages (Stack)
Component: <x-flash-stack />
Purpose: Consistent success/error banners with auto-dismiss.
Capture: A success toast and a warning toast in RTL.
Key UI: Rounded cards, readable Arabic labels.
________________________________________
Optional / Dev Tools
35) Mailpit Inbox
URL: http://localhost:8025/
Purpose: Local email preview.
Capture: A message list and a selected email in Arabic.
Key UI: Shows subjects are correctly RTL.
________________________________________
36) Stripe Test Dashboard
URL: https://dashboard.stripe.com/test/
Purpose: Verify PaymentIntents/charges in test mode.
Capture: PaymentIntent/Charge record that corresponds to an order.
Key UI: Useful to demonstrate end-to-end payment flow.


---

## Features

- 📚 **Catalog & search**: Books with categories, publishers, authors, cover images, stock, pricing.
- 🛒 **Cart & checkout** with quantity controls and stock checks.
- 💳 **Stripe Payments (test mode)** with **webhook** handling (`payment_intent.succeeded`, `charge.refunded`), idempotent intent creation, and robust order state transitions.
- 🧾 **Invoice PDF** (mPDF) attached to payment confirmation emails.
- 📬 **Email notifications** (order placed, paid, shipped, cancelled, status updated) via **Mailpit** in local/dev.
- 👥 **RBAC** via Spatie Permission (Admin / Seller / Customer).
- ⭐ **Reviews & ratings** with moderation (Admin/Seller).
- 🌍 **RTL & Arabic**: Layouts are `dir="rtl"`, typography and emails tuned for Arabic.
- ⚡ **UI niceties**: Page loader & button ripple, Tailwind CSS, Vite bundling.
- 🧰 Admin panel: Orders, refunds (Stripe), shipping (tracking number, carrier, shipped state), inventory updates.

---

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Payments**: Stripe (test mode)
- **DB/Cache**: MySQL 8+, Redis
- **Frontend**: Tailwind CSS, Vite, (Alpine/Livewire/Jetstream if included)
- **Email (local)**: Mailpit
- **PDF**: mPDF

---

## Requirements

**Common**
- Git
- Node.js **18+** and npm **9+**
- Stripe account (test mode)

**Option A — Docker/Sail (Recommended)**
- Docker Desktop (macOS/Windows) or Docker Engine (Linux)
- (Windows) WSL2 with Ubuntu

**Option B — Native**
- PHP **8.2+** with: `ctype`, `curl`, `dom`, `fileinfo`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `bcmath`, `gd`
- Composer **2.5+**
- MySQL **8+** (or MariaDB 10.6+)
- (Optional) Redis 6+

---

## Quick Start (TL;DR)

```bash
# 1) Clone
git clone https://github.com/<org-or-user>/<repo>.git
cd <repo>

# 2) Copy env
cp .env.example .env

# 3) Install dependencies
composer install
npm install

# 4) Run with Sail (Docker)
./vendor/bin/sail up -d

# 5) App key, storage, migrate
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail artisan migrate

# 6) Build assets (dev)
npm run dev

# 7) Stripe webhook (adjust URL if not Sail)
stripe listen --forward-to http://localhost/payments/stripe/webhook --log-level info
# paste whsec_... into STRIPE_WEBHOOK_SECRET in .env

# App at: http://localhost
# Mailpit UI at: http://localhost:8025  (SMTP: 1025)
```

---

## Option A (Recommended): Docker via Laravel Sail

### Step 1 — Clone & configure

```bash
git clone https://github.com/<org-or-user>/<repo>.git
cd <repo>
cp .env.example .env
composer install
npm install
```

In `.env`, use the Sail service hosts:
```dotenv
APP_NAME="Online Bookstore"
APP_ENV=local
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=bookstore
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@example.test"
MAIL_FROM_NAME="${APP_NAME}"

STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx   # fill after stripe listen
```

### Step 2 — Boot services

```bash
./vendor/bin/sail up -d
```

> Default ports  
> - App: **http://localhost**  
> - Mailpit UI: **http://localhost:8025** (SMTP on **1025**)  
> - MySQL: **3306**  
> - Redis: **6379**

If a port is **already in use**, stop the local service or edit `docker-compose.yml` to remap ports, then `sail down && sail up -d`.

### Step 3 — App key, storage, and migrations

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail artisan migrate
# ./vendor/bin/sail artisan db:seed   # if you have seeders
```

### Step 4 — Frontend assets

```bash
npm run dev   # development
# or
npm run build # production
```

### Step 5 — Stripe (test mode) & webhook

Install Stripe CLI and log in:

```bash
stripe login
```

Start a webhook listener that forwards to your app (Sail default URL):

```bash
stripe listen --forward-to http://localhost/payments/stripe/webhook --log-level info
```

Copy the printed `whsec_...` value into `.env` as `STRIPE_WEBHOOK_SECRET`, then try a test payment (e.g., card `4242 4242 4242 4242`, future date, any CVC). Verify:

- Stripe Dashboard (test mode) shows the payment
- `payment_intent.succeeded` → **200** in Stripe CLI logs
- Order status updates to **paid / processing**
- Stock decreases
- A confirmation email (with PDF invoice) appears in **Mailpit** (`http://localhost:8025`)

#### Useful Sail commands

```bash
./vendor/bin/sail artisan optimize:clear
./vendor/bin/sail artisan route:list
./vendor/bin/sail artisan tinker
./vendor/bin/sail artisan queue:work
./vendor/bin/sail npm run dev
./vendor/bin/sail npm run build

./vendor/bin/sail down
./vendor/bin/sail restart
```

Add an **alias** to your shell for convenience:

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

#### Windows + WSL2 tips
- Keep your project **inside the Linux filesystem** (e.g., `~/code/bookstore`) for performance.
- Don’t run heavy operations from `/mnt/c` or `/mnt/d` paths.
- Use `localhost` from Windows browser to reach Sail services.

---

## Option B: Native (No Docker)

1. Install PHP 8.2+, Composer, Node 18+, and MySQL 8+.
2. Clone & install:
   ```bash
   git clone https://github.com/<org-or-user>/<repo>.git
   cd <repo>
   cp .env.example .env
   composer install
   npm install
   ```
3. In `.env`, set your local DB credentials (e.g., `DB_HOST=127.0.0.1`) and mailer (Mailpit or other SMTP).
4. Generate key, link storage, migrate:
   ```bash
   php artisan key:generate
   php artisan storage:link
   php artisan migrate
   ```
5. Start server & assets:
   ```bash
   php artisan serve
   npm run dev
   ```
6. Stripe webhook (adjust URL to your serve port, e.g., `http://127.0.0.1:8000`):
   ```bash
   stripe listen --forward-to http://127.0.0.1:8000/payments/stripe/webhook --log-level info
   ```
   Paste `whsec_...` into `.env`.

---

## Environment Variables

Key variables used by the app:

```dotenv
APP_NAME="Online Bookstore"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql           # 127.0.0.1 if native
DB_PORT=3306
DB_DATABASE=bookstore
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis        # 127.0.0.1 if native

MAIL_MAILER=smtp
MAIL_HOST=mailpit       # 127.0.0.1 if native
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@example.test"
MAIL_FROM_NAME="${APP_NAME}"

STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
```

---

## Mailpit (Local Email)

- UI: `http://localhost:8025`
- SMTP: `mailpit:1025` (Sail) or `127.0.0.1:1025` (native)
- All outgoing app emails appear here (order placed/paid/shipped/etc.), with Arabic & PDF attachments (when applicable).

---

## Users & Roles

The project uses **Spatie/Permission** with roles: **Admin**, **Seller**, **Customer**.

- Register through the UI, then grant roles via your admin interface (or Tinker):

```php
// Example (Tinker):
$user = \App\Models\User::where('email','you@example.com')->first();
$user->assignRole('Admin'); // or 'Seller'
```

Some admin routes are protected by `role:Admin` or `role:Admin|Seller`.

---

## Queues (Optional but Recommended)

To send emails and heavy tasks asynchronously:

```dotenv
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
# Run worker:
# Sail:
./vendor/bin/sail artisan queue:work
# Native:
php artisan queue:work
```

---

## Common Tasks

```bash
# Clear caches
php artisan optimize:clear

# List routes
php artisan route:list

# Run tests (if provided)
php artisan test
```

With Sail, prefix commands with `./vendor/bin/sail`.

---

## Troubleshooting

- **`vendor/bin/sail: No such file or directory`**  
  Run `composer install` first. Ensure `vendor/` exists.

- **Ports (3306, 1025, 8025) already in use**  
  Stop local MySQL/Mailpit or edit `docker-compose.yml` to remap, then `sail down && sail up -d`.

- **Stripe “Invalid signature” (400)**  
  Make sure `STRIPE_WEBHOOK_SECRET` matches the most recent `stripe listen` session output.

- **Slow responses locally**  
  - Move project **inside** WSL’s Linux filesystem (not `/mnt/c` or `/mnt/d`).
  - Disable Xdebug.
  - Cache config/routes (`php artisan config:cache`, `route:cache`) in non-dev.

- **Emails not showing**  
  Confirm Mailpit is running and `.env` points to it. Check `queue:work` if queued.

---

## Production Notes

- Use a real SMTP provider (Mailgun, SES, etc.) instead of Mailpit.
- Set `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL`.
- Run `php artisan migrate --force`.
- Build assets: `npm run build`.
- Set up queue workers (Supervisor or systemd).
- Configure an HTTPS domain, real Stripe webhook endpoint in the Stripe Dashboard.
- Harden permissions on `storage/` and `bootstrap/cache/`.

---

## Contributing

Contributions are welcome!  
Please open an issue to discuss changes, then submit a PR with clear commits and descriptions.

---

## Security

If you discover a security issue, please **do not** open a public issue.  
Email the maintainer directly and allow reasonable time for a fix.

---

## License

This project is open-sourced software licensed under the **MIT license**.

---

**Enjoy!** Spin it up with **Sail**, test a Stripe payment, and check Mailpit for emails & invoices. The UI is RTL-aware out of the box—happy building!
