<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Admin bypass: إن كان المستخدم Admin نرجّع true لكل الأفعال.
     */
    public function before(User $user): ?bool
    {
        return $user->hasRole('Admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        // يسمح لكل من Admin و Seller برؤية القائمة
        return $user->hasAnyRole(['Admin', 'Seller']);
    }

    public function view(User $user, Book $book): bool
    {
        // Admin bypass يغطيها before()، هنا نسمح للبائع فقط لو كان مالك الكتاب
        return $book->seller_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Seller']);
    }

    public function update(User $user, Book $book): bool
    {
        return $book->seller_id === $user->id;
    }

    public function delete(User $user, Book $book): bool
    {
        return $book->seller_id === $user->id;
    }

    // اختياري (في حال أضفت سلة محذوفات soft deletes)
    public function restore(User $user, Book $book): bool
    {
        return $book->seller_id === $user->id;
    }

    public function forceDelete(User $user, Book $book): bool
    {
        return $book->seller_id === $user->id;
    }
}
