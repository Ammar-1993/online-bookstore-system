<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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
    profileController as AdminProfileController
};

/*
|--------------------------------------------------------------------------
| الواجهة العامة (Front)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');
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

    // فواتير
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/invoice.pdf', [OrderController::class, 'invoicePdf'])->name('orders.invoice.pdf');

    // نقاط دفع/إلغاء تجريبية
    Route::get('/payments/mock/{order}/success', [PaymentController::class, 'mockSuccess'])->name('payments.mock.success');
    Route::post('/orders/{order}/cancel', [PaymentController::class, 'cancel'])->name('orders.cancel');
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
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    Route::get(
        '/email/verify/{id}/{hash}',
        function (EmailVerificationRequest $request) {
            $request->fulfill();
            return redirect()->route('dashboard');
        }
    )->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
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

        // NEW: Admin Profile
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
