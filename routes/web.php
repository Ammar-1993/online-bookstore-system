<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\WishlistController;

// Front Controllers
use App\Http\Controllers\{
    HomeController,
    BookController,
    CategoryController,
    PublisherController,
    AuthorController,
    OrderController
};

// Reviews
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;

// Cart / Checkout / Payments
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeController;

// Admin Controllers
use App\Http\Controllers\Admin\{
    DashboardController,
    BookController as AdminBookController,
    CategoryController as AdminCategoryController,
    PublisherController as AdminPublisherController,
    AuthorController as AdminAuthorController,
    UserController as AdminUserController,
    OrderController as AdminOrderController,
    ProfileController as AdminProfileController
};

/*
|--------------------------------------------------------------------------
| الواجهة العامة (Front)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/** ترتيب مهم: index ثم search ثم show */
Route::get('/books', [BookController::class, 'index'])->name('books.index');          // صفحة البحث/التصفية
Route::get('/books/search', [BookController::class, 'search'])->name('books.search'); // إرجاع جزء النتائج (AJAX)
Route::get('/books/{book:slug}', [BookController::class, 'show'])
    ->where('book', '^(?!search$)[^/]+$') // استثناء كلمة search من الالتقاط
    ->name('books.show');

Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/publishers/{publisher:slug}', [PublisherController::class, 'show'])->name('publishers.show');
Route::get('/authors/{author:slug}', [AuthorController::class, 'show'])->name('authors.show');

/*
|--------------------------------------------------------------------------
| عربة التسوق
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{book:slug}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{book:slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{book:slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

/*
|--------------------------------------------------------------------------
| Checkout + طلبات العميل (يتطلب تسجيل دخول + تحقق البريد)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/thanks', [CheckoutController::class, 'thankyou'])->name('checkout.thankyou');

    // طلباتي
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/status', [OrderController::class, 'status'])->name('orders.status');

    // لوحة حسابي (العميل)
    Route::get('/account', [AccountController::class, 'dashboard'])->name('account.index');

    // (اختياري) إن كانت موجودة عندك بالفعل اتركها، وإلا أضفها:
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders.index');
    Route::get('/account/orders/{order}', [AccountController::class, 'show'])->name('account.orders.show');

    // فواتير
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/invoice.pdf', [OrderController::class, 'invoicePdf'])->name('orders.invoice.pdf');

    // نقاط دفع/إلغاء تجريبية
    Route::get('/payments/mock/{order}/success', [PaymentController::class, 'mockSuccess'])->name('payments.mock.success');
    Route::post('/orders/{order}/cancel', [PaymentController::class, 'cancel'])->name('orders.cancel');

    // Wishlist (المفضّلة)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{book}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{book}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/{book}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

/*
|--------------------------------------------------------------------------
| مراجعات الكتب
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::post('/books/{book:slug}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

/*
|--------------------------------------------------------------------------
| مسارات التحقق من البريد (Jetstream / Fortify)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // صفحة التعليمات لإتمام التحقق
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    // رابط التحقق الموقّع
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        $user = $request->user();
        $candidate = ($user && ($user->hasRole('Admin') || $user->hasRole('Seller')))
            ? 'admin.dashboard'
            : 'account.index'; // ✅ تصحيح الاسم

        $target = collect([$candidate, 'dashboard', 'home'])
            ->first(fn($name) => Route::has($name));

        return redirect()->route($target);
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    // إعادة إرسال رسالة التحقق
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('info', 'تمّ التحقق من بريدك مسبقًا.');
        }

        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| لوحة التحكم / الإدارة
|--------------------------------------------------------------------------
| ملاحظة: تأكد من تعريف وسطاء Spatie كـ alias في bootstrap/app.php:
| 'role', 'permission', 'role_or_permission'
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:Admin|Seller'])
    ->group(function () {
        // Dashboard (Controller invokable)
        Route::get('/', DashboardController::class)->name('dashboard');

        // Admin Profile
        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile');

        // كتب (Seller يدير كتبه عبر Policy، Admin الكل)
        Route::resource('books', AdminBookController::class);

        // مراجعات (للعرض والإدارة)
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/toggle', [AdminReviewController::class, 'toggle'])->name('reviews.toggle');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // الطلبات (إدارة)
        Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
        Route::post('/orders/{order}/refund', [AdminOrderController::class, 'refund'])
            ->middleware('role:Admin')->name('orders.refund');
        Route::post('/orders/{order}/ship', [AdminOrderController::class, 'ship'])->name('orders.ship');

        // موارد المشرف فقط
        Route::middleware('role:Admin')->group(function () {
            Route::resource('categories', AdminCategoryController::class)->except('show');
            Route::resource('publishers', AdminPublisherController::class)->except('show');
            Route::resource('authors', AdminAuthorController::class)->except('show');
            Route::resource('users', AdminUserController::class)->except(['show', 'create', 'store']);
        });
    });

/*
|--------------------------------------------------------------------------
| Stripe (الدفع)
|--------------------------------------------------------------------------
*/
// صفحات الدفع عبر Stripe (للمستخدمين المسجّلين)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/payments/stripe/{order}', [StripeController::class, 'pay'])->name('payments.stripe.pay');
    Route::post('/payments/stripe/{order}/intent', [StripeController::class, 'createIntent'])->name('payments.stripe.intent');
});
// Webhook عام (بدون مصادقة)
Route::post('/payments/stripe/webhook', [StripeController::class, 'webhook'])->name('payments.stripe.webhook');

/*
|--------------------------------------------------------------------------
| Dashboard بعد تسجيل الدخول
|--------------------------------------------------------------------------
| تحويل تلقائي: Admin/Seller => لوحة الإدارة، غير ذلك => dashboard الافتراضي.
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/dashboard', function () {
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['Admin', 'Seller'])) {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Fallback (اجعله أخيرًا دومًا)
|--------------------------------------------------------------------------
*/
Route::fallback(fn() => abort(404));
