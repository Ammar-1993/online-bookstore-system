<?php

namespace App\Support;

use App\Models\Book;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Cart
{
    public const SESSION_KEY = 'cart.items';
    public const MAX_PER_LINE = 99;

    /**
     * احصل على مصفوفة العناصر من الجلسة (مفتاحها book_id).
     *
     * @return array<int, array{
     *   book_id:int,title:string,slug:string,price:float,currency:string,
     *   qty:int,max:int,cover:?string,total:float
     * }>
     */
    protected function items(): array
    {
        $items = Session::get(self::SESSION_KEY, []);

        // تأكد من وجود total محدث وmax مطابق للمخزون دائماً
        foreach ($items as $id => &$row) {
            $row['qty']   = max(0, (int) ($row['qty'] ?? 0));
            $row['price'] = (float) ($row['price'] ?? 0);
            $row['total'] = (float) ($row['price'] * $row['qty']);
        }

        return $items;
    }

    protected function put(array $items): void
    {
        Session::put(self::SESSION_KEY, $items);
    }

    /**
     * إرجاع جميع العناصر كما تُعرض للواجهة.
     * البنية متوافقة مع القالب لديك (title/slug/cover/price/qty/max/currency + total).
     */
    public function all(): array
    {
        return $this->items();
    }

    /** اختصار بديل إن احتجته */
    public function list(): array
    {
        return $this->all();
    }

    /** هل السلة فارغة؟ */
    public function isEmpty(): bool
    {
        return empty($this->items());
    }

    /** إجمالي القطع */
    public function count(): int
    {
        return array_sum(array_column($this->items(), 'qty'));
    }

    /**
     * إضافة عنصر (Book أو id) مع ضبط الحدود والمخزون ومنع خلط العملات.
     */
    public function add(Book|int $book, int $qty = 1): void
    {
        $book = $this->resolveBook($book);

        if (($book->stock_qty ?? 0) <= 0) {
            throw new \RuntimeException('الكتاب غير متاح في المخزون.');
        }

        $qty   = max(1, min($qty, self::MAX_PER_LINE));
        $items = $this->items();

        // منع خلط العملات
        if (!empty($items)) {
            $existingCurrency = reset($items)['currency'] ?? null;
            if ($existingCurrency && $existingCurrency !== $book->currency) {
                throw new \RuntimeException('لا يمكن خلط عملات مختلفة في نفس السلة.');
            }
        }

        $id         = (int) $book->id;
        $currentQty = (int) ($items[$id]['qty'] ?? 0);
        $newQty     = min($currentQty + $qty, (int) $book->stock_qty, self::MAX_PER_LINE);

        if ($newQty <= 0) {
            throw new \RuntimeException('لا تتوفر كمية كافية في المخزون.');
        }

        $items[$id] = [
            'book_id'  => $id,
            'title'    => (string) $book->title,
            'slug'     => (string) $book->slug,
            'price'    => (float)  $book->price,
            'currency' => (string) $book->currency,
            'qty'      => $newQty,
            'max'      => (int)    $book->stock_qty,
            'cover'    => $book->cover_image_path,
            'total'    => (float) ($book->price * $newQty),
        ];

        $this->put($items);
    }

    /**
     * تحديث الكمية (0 يحذف العنصر). يقبل Book أو id.
     */
    public function update(Book|int $book, int $qty): void
    {
        $book = $this->resolveBook($book);

        $qty   = max(0, min($qty, self::MAX_PER_LINE));
        $items = $this->items();
        $id    = (int) $book->id;

        if ($qty === 0) {
            unset($items[$id]);
            $this->put($items);
            return;
        }

        $qty = min($qty, (int) $book->stock_qty);

        if (isset($items[$id])) {
            $items[$id]['qty']   = $qty;
            $items[$id]['max']   = (int) $book->stock_qty;
            $items[$id]['price'] = (float) $book->price; // في حال تغيّر السعر
            $items[$id]['total'] = (float) ($items[$id]['price'] * $qty);
        } else {
            // إن لم يكن موجودًا مسبقًا نضيفه
            $this->add($book, $qty);
            return;
        }

        $this->put($items);
    }

    /**
     * حذف عنصر (Book أو id).
     */
    public function remove(Book|int $book): void
    {
        $id    = $book instanceof Book ? (int) $book->id : (int) $book;
        $items = $this->items();
        unset($items[$id]);
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
        return array_reduce(
            $this->items(),
            fn (float $carry, array $row) => $carry + (float) ($row['price'] * $row['qty']),
            0.0
        );
    }

    /** عملة السلة (أو null إن كانت فارغة) */
    public function currency(): ?string
    {
        $items = $this->items();
        if (empty($items)) {
            return null;
        }
        return (string) (reset($items)['currency'] ?? null);
    }

    /** هل العنصر موجود؟ */
    public function exists(Book|int $book): bool
    {
        $id = $book instanceof Book ? (int) $book->id : (int) $book;
        return array_key_exists($id, $this->items());
    }

    /** الكمية الحالية لعنصر ما (0 إن لم يوجد) */
    public function quantity(Book|int $book): int
    {
        $id = $book instanceof Book ? (int) $book->id : (int) $book;
        $items = $this->items();
        return (int) ($items[$id]['qty'] ?? 0);
    }

    /**
     * تحويل (Book|int) إلى Book مع فشل واضح عند عدم الوجود.
     */
    protected function resolveBook(Book|int $book): Book
    {
        if ($book instanceof Book) {
            return $book;
        }

        $model = Book::query()->find((int) $book);
        if (!$model) {
            throw new ModelNotFoundException('Book not found for cart operation.');
        }

        return $model;
    }
}
