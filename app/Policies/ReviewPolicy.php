<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->hasRole('Admin');
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->hasRole('Admin');
    }

    public function moderate(User $user, Review $review): bool
    {
        if ($user->hasRole('Admin')) return true;
        return $user->hasRole('Seller') && $review->book && $review->book->seller_id === $user->id;
    }
}
