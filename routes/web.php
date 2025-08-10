<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\{HomeController,BookController,CategoryController,PublisherController,AuthorController};


/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

// صفحة تفاصيل الكتاب باستخدام الـ slug
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');


/* صفحات الكيان */
Route::get('/books/{book:slug}', [BookController::class, 'show'])->name('books.show');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/publishers/{publisher:slug}', [PublisherController::class, 'show'])->name('publishers.show');
Route::get('/authors/{author:slug}', [AuthorController::class, 'show'])->name('authors.show');

/*
|--------------------------------------------------------------------------
| Email Verification Routes (Jetstream/Fortify)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // صفحة تنبيه التحقق
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // تفعيل البريد عبر الرابط الموقّع
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // يحدّث verified_at للمستخدم
        return redirect()->route('dashboard');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    // إعادة إرسال رابط التحقق
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Dashboard (Authenticated + Verified)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
