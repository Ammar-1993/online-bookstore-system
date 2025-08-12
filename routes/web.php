<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// الواجهة العامة
use App\Http\Controllers\{
    HomeController,
    BookController,
    CategoryController,
    PublisherController,
    AuthorController
};

// لوحة التحكم (Admin)
use App\Http\Controllers\Admin\{
    DashboardController,
    BookController as AdminBookController,
    CategoryController as AdminCategoryController,
    PublisherController as AdminPublisherController,
    AuthorController as AdminAuthorController,
    UserController as AdminUserController
};

/*
|--------------------------------------------------------------------------
| الواجهة العامة (Front)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/books/{book:slug}', [BookController::class, 'show'])
    ->name('books.show');

Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])
    ->name('categories.show');

Route::get('/publishers/{publisher:slug}', [PublisherController::class, 'show'])
    ->name('publishers.show');

Route::get('/authors/{author:slug}', [AuthorController::class, 'show'])
    ->name('authors.show');


/*
|--------------------------------------------------------------------------
| مسارات التحقق من البريد (Jetstream / Fortify)
|--------------------------------------------------------------------------
| مفعّلة للمستخدم المسجّل فقط.
*/
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn () => view('auth.verify-email'))
        ->name('verification.notice');

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

        // كتب (Seller يرى/يدير كتبه فقط عبر الـ Policy؛ Admin يرى الكل)
        Route::resource('books', AdminBookController::class);

        // موارد خاصة بالمشرف Admin فقط
        Route::middleware('role:Admin')->group(function () {
            Route::resource('categories', AdminCategoryController::class)->except('show');
            Route::resource('publishers', AdminPublisherController::class)->except('show');
            Route::resource('authors',    AdminAuthorController::class)->except('show');

            // إدارة المستخدمين (لا إنشاء من اللوحة حالياً)
            Route::resource('users', AdminUserController::class)
                ->except(['show', 'create', 'store']);
        });
    });


/*
|--------------------------------------------------------------------------
| Dashboard الافتراضية بعد تسجيل الدخول (Jetstream)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/dashboard', fn () => view('dashboard'))
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Fallback (اختياري)
|--------------------------------------------------------------------------
*/
Route::fallback(fn () => abort(404));
