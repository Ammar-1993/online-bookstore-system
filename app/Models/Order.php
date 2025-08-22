<?php

namespace App\Models;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    /** حالات الطلب المعتمدة في اللوحة */
    public const STATUSES = ['pending', 'processing', 'shipped', 'cancelled'];

    /** حالات الدفع المعتمدة */
    public const PAYMENT_STATUSES = ['unpaid', 'paid', 'refunded'];

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

        // حقول الشحن
        'tracking_number',
        'shipping_carrier',
        'tracking_url',
        'shipped_at',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'billing_address'  => 'array',
        'placed_at'        => 'datetime',
        'paid_at'          => 'datetime',
        'shipped_at'       => 'datetime',
    ];

    /* ==================== العلاقات ==================== */

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ==================== مساعدات الأعمال ==================== */

    /**
     * خصم المخزون وتحديث حالة الطلب عند تأكيد الدفع.
     * آمن ضد ظروف التنافس باستخدام قفل صفوف الكتب داخل معاملة.
     */
    public function markPaid(): void
    {
        DB::transaction(function () {
            if ($this->payment_status === 'paid') {
                return;
            }

            $this->loadMissing('items.book');

            // اقفل كتب الطلب
            $bookIds = $this->items->pluck('book_id')->unique()->values();
            $books   = Book::whereIn('id', $bookIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // تحقّق من الكميات المتاحة
            foreach ($this->items as $it) {
                $book = $books[$it->book_id] ?? null;
                if (! $book) {
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

            // تحديث حالة الدفع والطلب
            $this->forceFill([
                'payment_status' => 'paid',
                'status'         => 'processing',
                'placed_at'      => $this->placed_at ?? now(),
                'paid_at'        => $this->paid_at ?? now(),
            ])->save();
        });
    }

    /**
     * إلغاء الطلب واسترجاع المخزون إذا كان قد خُصم (في حالة paid).
     */
    public function cancelAndRestock(): void
    {
        DB::transaction(function () {
            $this->loadMissing('items.book');

            if ($this->payment_status === 'paid') {
                $bookIds = $this->items->pluck('book_id')->unique()->values();
                $books   = Book::whereIn('id', $bookIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($this->items as $it) {
                    $books[$it->book_id]->increment('stock_qty', $it->qty);
                }

                $this->payment_status = 'refunded';
            }

            $this->status = 'cancelled';
            $this->save();
        });
    }

    /**
     * تمييز الطلب كشُحن: يضبط بيانات التتبع ويحوّل الحالة إلى shipped.
     */
    public function markShipped(string $trackingNumber, ?string $carrier = null, ?string $trackingUrl = null): void
    {
        $trackingUrl = $trackingUrl ?: $this->buildTrackingUrl($carrier, $trackingNumber);

        $this->forceFill([
            'status'          => 'shipped',
            'tracking_number' => $trackingNumber,
            'shipping_carrier'=> $carrier,
            'tracking_url'    => $trackingUrl,
            'shipped_at'      => now(),
        ])->save();
    }

    /**
     * يبني رابط التتبع من config/shipping.php (إن وُجد)، وإلا يستخدم fallback.
     */
    public function buildTrackingUrl(?string $carrier, string $number): ?string
    {
        $carrier = $carrier ? strtolower($carrier) : null;
        $map     = (array) (config('shipping.carriers') ?? []);
        $tpl     = $carrier && isset($map[$carrier]) ? $map[$carrier] : (config('shipping.fallback') ?? null);

        return $tpl ? str_replace('{number}', urlencode($number), $tpl) : null;
    }

    /* ==================== شروط واختصارات ==================== */

    public function isPayable(): bool
    {
        return ! in_array($this->status, ['cancelled'], true)
            && ! in_array($this->payment_status, ['paid', 'refunded'], true);
    }

    public function isCancelable(): bool
    {
        return $this->status !== 'cancelled';
    }

    public function isShippable(): bool
    {
        return $this->payment_status === 'paid' && $this->status !== 'shipped' && $this->status !== 'cancelled';
    }

    public function wasPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /* ==================== تنسيقات وعرض ==================== */

    public function computedTotal(): float
    {
        if (! is_null($this->total_amount)) {
            return (float) $this->total_amount;
        }

        $this->loadMissing('items');

        return round((float) $this->items->sum('total_price'), 2);
    }

    /** رقم منسّق مثل ORD-000123 */
    public function getNumberAttribute(): string
    {
        return 'ORD-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    /** كود العملة */
    public function currencyCode(): string
    {
        return $this->currency ?: config('app.currency', 'USD');
    }

    /** تنسيق مبالغ بسيط */
    public function money(float|int $amount): string
    {
        return number_format((float) $amount, 2) . ' ' . $this->currencyCode();
    }

    /* ==================== سكوبات مفيدة للفلاتر ==================== */

    public function scopeStatus($q, ?string $status)
    {
        if ($status && in_array($status, self::STATUSES, true)) {
            $q->where('status', $status);
        }
    }

    public function scopePaymentStatus($q, ?string $paymentStatus)
    {
        if ($paymentStatus && in_array($paymentStatus, self::PAYMENT_STATUSES, true)) {
            $q->where('payment_status', $paymentStatus);
        }
    }

    public function scopeDateRange($q, ?string $from, ?string $to)
    {
        if ($from) {
            $q->where('created_at', '>=', $from . ' 00:00:00');
        }
        if ($to) {
            $q->where('created_at', '<=', $to . ' 23:59:59');
        }
    }
}
