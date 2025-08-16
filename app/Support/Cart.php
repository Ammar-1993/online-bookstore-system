<?php

namespace App\Support;

use App\Models\Book;
use Illuminate\Support\Facades\Session;

class Cart
{
    public const SESSION_KEY = 'cart.items';

    protected function items(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    protected function put(array $items): void
    {
        Session::put(self::SESSION_KEY, $items);
    }

    /** جميع العناصر */
    public function all(): array
    {
        return $this->items();
    }

    /** إجمالي القطع */
    public function count(): int
    {
        return array_sum(array_column($this->items(), 'qty'));
    }

    /** إضافة عنصر */
    public function add(Book $book, int $qty = 1): void
    {
        if ($book->stock_qty <= 0) {
            throw new \RuntimeException('الكتاب غير متاح في المخزون.');
        }

        $qty = max(1, min($qty, 99));
        $items = $this->items();

        // منع خلط العملات
        if (!empty($items)) {
            $existingCurrency = reset($items)['currency'];
            if ($existingCurrency !== $book->currency) {
                throw new \RuntimeException('لا يمكن خلط عملات مختلفة في نفس السلة.');
            }
        }

        $id = $book->id;
        $currentQty = $items[$id]['qty'] ?? 0;
        $newQty = min($currentQty + $qty, (int) $book->stock_qty);

        if ($newQty <= 0) {
            throw new \RuntimeException('لا تتوفر كمية كافية في المخزون.');
        }

        $items[$id] = [
            'book_id' => $book->id,
            'title'   => $book->title,
            'slug'    => $book->slug,
            'price'   => (float) $book->price,
            'currency'=> $book->currency,
            'qty'     => $newQty,
            'max'     => (int) $book->stock_qty,
            'cover'   => $book->cover_image_path,
        ];

        $this->put($items);
    }

    /** تحديث الكمية (0 يحذف العنصر) */
    public function update(Book $book, int $qty): void
    {
        $qty = max(0, min($qty, 99));
        $items = $this->items();

        if ($qty === 0) {
            unset($items[$book->id]);
        } else {
            $qty = min($qty, (int) $book->stock_qty);
            if (isset($items[$book->id])) {
                $items[$book->id]['qty'] = $qty;
                $items[$book->id]['max'] = (int) $book->stock_qty;
            } else {
                $this->add($book, $qty);
                return;
            }
        }

        $this->put($items);
    }

    /** حذف عنصر */
    public function remove(Book $book): void
    {
        $items = $this->items();
        unset($items[$book->id]);
        $this->put($items);
    }

    /** تفريغ السلة */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /** الإجمالي (بدون شحن/ضريبة) */
    public function subtotal(): float
    {
        return array_reduce($this->items(), fn ($c, $i) => $c + ($i['price'] * $i['qty']), 0.0);
    }

    /** عملة السلة */
    public function currency(): ?string
    {
        $items = $this->items();
        if (empty($items)) return null;
        return (string) (reset($items)['currency'] ?? null);
    }
}
