<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\Book; // ← مهم

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'payment_status',
        'total_amount',
        'currency',
        'shipping_address',
        'billing_address',
        'placed_at',
        'payment_intent_id',
        'charge_id',
        'paid_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * خصم المخزون وتحديث حالة الطلب عند تأكيد الدفع.
     * آمن ضد السباقات باستخدام lockForUpdate داخل معاملة.
     */
    public function markPaid(): void
    {
        DB::transaction(function () {
            if ($this->payment_status === 'paid') {
                return; // تم تنفيذها مسبقاً
            }

            $this->loadMissing('items.book');

            // اقفل سجلات الكتب المراد خصمها
            $bookIds = $this->items->pluck('book_id')->unique()->values();
            $books = Book::whereIn('id', $bookIds)->lockForUpdate()->get()->keyBy('id');

            // تحقّق التوفّر
            foreach ($this->items as $it) {
                $book = $books[$it->book_id] ?? null;
                if (!$book) {
                    throw new \RuntimeException("Book not found: {$it->book_id}");
                }
                if ($book->stock_qty < $it->qty) {
                    throw new \RuntimeException("الكمية غير متاحة لكتاب: {$book->title}");
                }
            }

            // خصم المخزون
            foreach ($this->items as $it) {
                $books[$it->book_id]->decrement('stock_qty', $it->qty);
            }

            // حدّث حالة الدفع/الطلب
            $this->forceFill([
                'payment_status' => 'paid',
                'status' => 'processing',
                'placed_at' => $this->placed_at ?? now(),
            ])->save();
        });
    }

    /**
     * إلغاء الطلب واسترجاع المخزون إذا كان قد خُصم (أي في حالة paid).
     */
    public function cancelAndRestock(): void
    {
        DB::transaction(function () {
            $this->loadMissing('items.book');

            if ($this->payment_status === 'paid') {
                $bookIds = $this->items->pluck('book_id')->unique()->values();
                $books = Book::whereIn('id', $bookIds)->lockForUpdate()->get()->keyBy('id');

                foreach ($this->items as $it) {
                    $books[$it->book_id]->increment('stock_qty', $it->qty);
                }

                $this->payment_status = 'refunded';
            }

            $this->status = 'cancelled';
            $this->save();
        });
    }

    // داخل class Order
    public function isPayable(): bool
    {
        return !in_array($this->status, ['cancelled'], true)
            && !in_array($this->payment_status, ['paid', 'refunded'], true);
    }

    public function isCancelable(): bool
    {
        return $this->status !== 'cancelled';
    }

    public function computedTotal(): float
    {
        if (!is_null($this->total_amount))
            return (float) $this->total_amount;
        $this->loadMissing('items');
        return (float) $this->items->sum('total_price');
    }



    /**
     * إرجاع رقم طلب منسّق مثل ORD-000123
     */
    public function getNumberAttribute(): string
    {
        return 'ORD-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * كود العملة الافتراضي (يقرأ من عمود order أو من config).
     */
    public function currencyCode(): string
    {
        return $this->currency ?: config('app.currency', 'USD');
    }

    /**
     * مُنسِّق بسيط للمبالغ مع العملة (بدون i18n ثقيل).
     */
    public function money(float|int $amount): string
    {
        return number_format((float) $amount, 2) . ' ' . $this->currencyCode();
    }

    /**
     * إجمالي الطلب منسوبًا إلى العناصر إن لم تُخزّن total_amount.
     */

}








