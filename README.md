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

## لقطات الشاشة | Screenshots

🏠 الصفحة الرئيسية | Home

صفحة الهبوط تعرض الكتب المميّزة والأقسام وروابط التصفّح والبحث السريع. <img alt="Home" src="docs/screenshots/home.png" width="1200" />

🗂️ صفحة التصنيف | Category

تصفّح كتب تصنيف معيّن مع خيارات الفرز والترقيم. <img alt="Category" src="docs/screenshots/category.png" width="1200" />

🏢 صفحة الناشر | Publisher

عرض بيانات الناشر وكامل كتبه المنشورة في المتجر. <img alt="Publisher" src="docs/screenshots/publisher.png" width="1200" />

✍️ صفحة المؤلف | Author

نبذة المؤلف وقائمة أعماله مع روابط تفصيل الكتب. <img alt="Author" src="docs/screenshots/author.png" width="1200" />

📘 تفاصيل الكتاب + المراجعات | Book Details + Reviews

صفحة المنتج: الصور والوصف والسعر والتوفّر والإضافة للسلة مع استعراض/كتابة المراجعات. <img alt="Book Details" src="docs/screenshots/book-details.png" width="1200" />

🛒 سلة التسوّق | Cart

مراجعة العناصر وتحديث الكميات/الحذف والانتقال لإتمام الطلب. <img alt="Cart" src="docs/screenshots/cart.png" width="1200" />

💳 صفحة إتمام الطلب | Checkout

إدخال عناوين الشحن/الفوترة مع ملخص نهائي قبل الدفع. <img alt="Checkout" src="docs/screenshots/checkout.png" width="1200" />

🧾 الدفع عبر Stripe | Stripe Payment

تأكيد الدفع ببطاقات الاختبار وإنشاء PaymentIntent وتحديث الحالة عبر Webhook. <img alt="Stripe Payment" src="docs/screenshots/payment-stripe.png" width="1200" />

✅ صفحة الشكر | Thank You

تأكيد إنشاء الطلب مع روابط متابعة الطلب والفاتورة. <img alt="Thank You" src="docs/screenshots/thank-you.png" width="1200" />

📦 طلباتي | My Orders

قائمة طلبات العميل مع حالة الدفع والطلب وترقيم الصفحات. <img alt="My Orders" src="docs/screenshots/my-orders.png" width="1200" />

🔍 تفاصيل الطلب (عميل) | Order Details (Customer)

تفاصيل العناصر والمبالغ والإجراءات (دفع/إلغاء/عرض الفاتورة). <img alt="Order Details" src="docs/screenshots/order-details.png" width="1200" />

🧾 فاتورة HTML | Invoice (HTML)

عرض الفاتورة للطباعة مع دعم اتجاه RTL. <img alt="Invoice HTML" src="docs/screenshots/invoice-html.png" width="1200" />

📄 فاتورة PDF | Invoice PDF

تنزيل الفاتورة بصيغة PDF (mPDF) مع دعم العربية. <img alt="Invoice PDF" src="docs/screenshots/invoice-pdf.png" width="1200" />

🧭 لوحة التحكم | Admin Dashboard

نظرة عامة وإحصاءات وروابط سريعة للإدارة. <img alt="Admin Dashboard" src="docs/screenshots/admin-dashboard.png" width="1200" />

📚 إدارة الكتب – القائمة | Admin Books – Index

استعراض الكتب مع البحث والترقيم وإجراءات سريعة. <img alt="Admin Books Index" src="docs/screenshots/admin-books-index.png" width="1200" />

✏️ إدارة الكتب – إنشاء/تعديل | Admin Books – Create/Edit

إنشاء كتاب جديد أو تعديل البيانات والسعر والمخزون والصور. <img alt="Admin Books Edit" src="docs/screenshots/admin-books-edit.png" width="1200" />

🏷️ التصنيفات | Admin Categories

إدارة التصنيفات وربطها بالكتب. <img alt="Admin Categories" src="docs/screenshots/admin-categories.png" width="1200" />

🏢 الناشرون | Admin Publishers

إنشاء/تعديل الناشرين وإدارة كتبهم. <img alt="Admin Publishers" src="docs/screenshots/admin-publishers.png" width="1200" />

✍️ المؤلفون | Admin Authors

إدارة المؤلفين وربطهم بالكتب. <img alt="Admin Authors" src="docs/screenshots/admin-authors.png" width="1200" />

👥 المستخدمون | Admin Users

إدارة المستخدمين والصلاحيات (Spatie Roles/Permissions). <img alt="Admin Users" src="docs/screenshots/admin-users.png" width="1200" />

⭐ المراجعات | Admin Reviews (Moderation)

مراجعة/قبول/رفض التقييمات مع البحث والتصفية. <img alt="Admin Reviews" src="docs/screenshots/admin-reviews.png" width="1200" />

🧾 الطلبات – القائمة (فلاتر) | Admin Orders – Index (Filters)

بحث وترشيح الطلبات حسب الحالة/الدفع/التاريخ/البريد. <img alt="Admin Orders Index" src="docs/screenshots/admin-orders-index-filters.png" width="1200" />

🔎 تفاصيل الطلب والإجراءات | Admin Order – Details & Actions

عرض كامل للطلب (عناصر/مبالغ/معرّفات Stripe) مع إجراءات (استرجاع/تغيير حالة/تتبع شحنة). <img alt="Admin Order Details" src="docs/screenshots/admin-order-details.png" width="1200" />

🔑 تسجيل الدخول | Login

وصول آمن بحقول البريد/كلمة المرور ودعم RTL. <img alt="Login" src="docs/screenshots/auth-login.png" width="1200" />

📝 إنشاء حساب | Register

إنشاء حساب جديد مع التحقق من البيانات. <img alt="Register" src="docs/screenshots/auth-register.png" width="1200" />

✉️ تأكيد البريد | Email Verification

تفعيل البريد الإلكتروني قبل الميزات الحسّاسة. <img alt="Verify Email" src="docs/screenshots/auth-verify.png" width="1200" />

🚫 خطأ 403 | Error 403

شاشة ودّية عند عدم السماح بالوصول. <img alt="403" src="docs/screenshots/error-403.png" width="1200" />

📨 بريد: تأكيد إنشاء الطلب | Email: Order Placed

رسالة إنشاء الطلب مع ملخص مختصر ورابط التتبع. <img alt="Email Placed" src="docs/screenshots/email-placed.png" width="1200" />

💳 بريد: تأكيد الدفع | Email: Order Paid

تأكيد الدفع وإرفاق الفاتورة PDF (RTL). <img alt="Email Paid" src="docs/screenshots/email-paid.png" width="1200" />

📦 بريد: تم الشحن | Email: Order Shipped

إشعار الشحن مع رقم/رابط التتبع. <img alt="Email Shipped" src="docs/screenshots/email-shipped.png" width="1200" />

❌ بريد: تم الإلغاء | Email: Order Cancelled

إشعار إلغاء الطلب واسترجاع المبلغ (إن وُجد). <img alt="Email Cancelled" src="docs/screenshots/email-cancelled.png" width="1200" />

🔄 بريد: تحديث حالة الطلب | Email: Order Status Updated

إشعار تغيّر الحالة (processing/shipped…). <img alt="Email Status Updated" src="docs/screenshots/email-status-updated.png" width="1200" />

⏳ مُحمّل الصفحة العام | Global Page Loader

ستار تحميل يغطي الشاشة مع تأثير Ripple على الأزرار. <img alt="Loader Fullscreen" src="docs/screenshots/loader-fullscreen.png" width="1200" />

⚪ مُحمّل مركزي داخل بطاقة | Centered Loader (Inline)

لودر مركزي أنيق للاستخدام داخل بطاقة/مودال. <img alt="Loader Center" src="docs/screenshots/loader-center.png" width="1200" />

📮 Mailpit | Mailpit Inbox

استعراض رسائل النظام أثناء التطوير. <img alt="Mailpit" src="docs/screenshots/mailpit.png" width="1200" />

💼 Stripe Test Dashboard | Stripe Test Dashboard

متابعة عمليات الدفع التجريبية وأحداث الويب هوك. <img alt="Stripe Dashboard" src="docs/screenshots/stripe-dashboard.png" width="1200" />

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
APP_URL=http://localhost:8088

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
