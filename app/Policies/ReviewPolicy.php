<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    // أي مستخدم مسجّل وموثّق البريد يستطيع إنشاء مراجعة
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    // صاحب المراجعة أو الأدمن فقط يعدّل/يحذف
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->hasRole('Admin');
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->hasRole('Admin');
    }

    // إدارة (قبول/رفض) — أدمن دائمًا، أو البائع مالك الكتاب
    public function moderate(User $user, Review $review): bool
    {
        if ($user->hasRole('Admin')) return true;

        return $user->hasRole('Seller') && $review->book && $review->book->seller_id === $user->id;
    }
}
