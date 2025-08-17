<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeController;




// الواجهة العامة
use App\Http\Controllers\{
    HomeController,
    BookController,
    CategoryController,
    PublisherController,
    AuthorController,
    OrderController
};

// لوحة التحكم (Admin)
use App\Http\Controllers\Admin\{
    DashboardController,
    BookController as AdminBookController,
    CategoryController as AdminCategoryController,
    PublisherController as AdminPublisherController,
    AuthorController as AdminAuthorController,
    UserController as AdminUserController,
    OrderController as AdminOrderController
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




// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{book:slug}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{book:slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{book:slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');



// Checkout (يتطلب تسجيل الدخول لأن orders.user_id غير فارغ)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/thanks', [CheckoutController::class, 'thankyou'])->name('checkout.thankyou');

        // طلباتي (عميل)
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('/orders/{order}/invoice.pdf', [OrderController::class, 'invoicePdf'])
            ->name('orders.invoice.pdf');



        // تأكيد دفع (نسخة Mock للتطوير)
        Route::get('/payments/mock/{order}/success', [PaymentController::class, 'mockSuccess'])
            ->name('payments.mock.success');

        // إلغاء طلب
        Route::post('/orders/{order}/cancel', [PaymentController::class, 'cancel'])
            ->name('orders.cancel');

    });




/*
|--------------------------------------------------------------------------
| مراجعات الكتب (مستخدم مسجّل + بريد مُفعّل)
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
| مفعّلة للمستخدم المسجّل فقط.
*/
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // يحدّث verified_at
        return redirect()->route('dashboard');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');


});

/*
|--------------------------------------------------------------------------
| لوحة التحكم / الإدارة
|--------------------------------------------------------------------------
| ملاحظة: تأكد من تعريف aliases لوسائط Spatie في bootstrap/app.php:
| 'role', 'permission', 'role_or_permission'
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:Admin|Seller'])
    ->group(function () {
        // الصفحة الرئيسية للوحة التحكم
        Route::get('/', DashboardController::class)->name('dashboard');

        // كتب (Seller يدير كتبه فقط عبر الـ Policy؛ Admin يرى الكل)
        Route::resource('books', AdminBookController::class);

        // إدارة المراجعات: Admin و Seller (Seller يرى مراجعات كتبه فقط - مفلترة في الكنترولر/Policy)
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/reviews/{review}/toggle', [AdminReviewController::class, 'toggle'])->name('reviews.toggle');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

        // موارد خاصة بالمشرف Admin فقط
        Route::middleware('role:Admin')->group(function () {
            Route::resource('categories', AdminCategoryController::class)->except('show');
            Route::resource('publishers', AdminPublisherController::class)->except('show');
            Route::resource('authors', AdminAuthorController::class)->except('show');

            // إدارة المستخدمين (لا إنشاء من اللوحة حاليًا)
            Route::resource('users', AdminUserController::class)->except(['show', 'create', 'store']);

            Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
        });
    });

/*
|--------------------------------------------------------------------------
| Dashboard الافتراضية بعد تسجيل الدخول (Jetstream)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/dashboard', fn() => view('dashboard'))
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Fallback (اختياري)
|--------------------------------------------------------------------------
*/
Route::fallback(fn() => abort(404));




Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/payments/stripe/{order}', [StripeController::class, 'pay'])->name('payments.stripe.pay');
    Route::post('/payments/stripe/{order}/intent', [StripeController::class, 'createIntent'])->name('payments.stripe.intent');
});

Route::post('/payments/stripe/webhook', [StripeController::class, 'webhook'])->name('payments.stripe.webhook');

