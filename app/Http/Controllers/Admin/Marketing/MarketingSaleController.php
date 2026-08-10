<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\MarketingBuyer;
use App\Models\MarketingSale;
use App\Models\MarketingSaleItem;
use App\Support\JalaliCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarketingSaleController extends Controller
{
    private function marketerId(): int
    {
        return (int) config('marketing.default_marketer_id');
    }

    public function index()
    {
        $sales = MarketingSale::query()
            ->where('marketer_id', $this->marketerId())
            ->with(['buyer', 'items'])
            ->orderByDesc('id')
            ->get();

        return view('backend.marketing.sales.index', compact('sales'));
    }

    public function create()
    {
        return view('backend.marketing.sales.create');
    }

    public function store(Request $request)
    {
        $mid = $this->marketerId();

        $base = $request->validate([
            'buyer_id' => ['required', 'integer', 'exists:marketing_buyers,id'],
            'sale_date_shamsi' => ['required', 'string', 'max:32'],
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.unit_price' => ['required', 'integer', 'min:1'],
            'items.*.quantity_text' => ['required', 'string', 'max:500'],
        ]);

        $buyer = MarketingBuyer::query()->findOrFail($base['buyer_id']);
        if ((int) $buyer->marketer_id !== $mid) {
            abort(404);
        }

        $saleDate = $this->parseSaleDate($base['sale_date_shamsi']);

        DB::transaction(function () use ($base, $buyer, $mid, $saleDate) {
            $sale = MarketingSale::create([
                'marketer_id' => $mid,
                'buyer_id' => $buyer->id,
                'sale_type' => MarketingSale::SALE_TYPE_MARKETER,
                'status' => MarketingSale::STATUS_PENDING,
                'buyer_first_name' => $buyer->first_name,
                'buyer_last_name' => $buyer->last_name,
                'buyer_phone' => $buyer->phone,
                'buyer_store_name' => $buyer->store_name,
                'payment_method' => $base['payment_method'] ?? null,
                'notes' => $base['notes'] ?? null,
                'sale_date' => $saleDate,
            ]);

            foreach ($base['items'] as $i => $row) {
                MarketingSaleItem::create([
                    'marketing_sale_id' => $sale->id,
                    'product_id' => (int) $row['product_id'],
                    'unit_price' => (int) $row['unit_price'],
                    'quantity_text' => $row['quantity_text'],
                    'sort_order' => $i,
                ]);
            }
        });

        message('success', 'فروش ثبت شد و برای بررسی حسابداری ارسال گردید.');

        return redirect()->route('admin.marketing.sales.index');
    }

    public function show(MarketingSale $sale)
    {
        $this->authorizeSale($sale);
        $sale->load(['buyer.province', 'buyer.city', 'items.product.images', 'order', 'reviewer']);

        return view('backend.marketing.sales.show', compact('sale'));
    }

    public function destroy(MarketingSale $sale)
    {
        $this->authorizeSale($sale);
        if (! $sale->isPending()) {
            message('warning', 'فقط فروش‌های در انتظار حسابداری قابل حذف هستند.');

            return redirect()->back();
        }
        $sale->delete();
        message('success', 'فروش حذف شد.');

        return redirect()->route('admin.marketing.sales.index');
    }

    private function authorizeSale(MarketingSale $sale): void
    {
        if ((int) $sale->marketer_id !== $this->marketerId()) {
            abort(404);
        }
    }

    private function parseSaleDate(string $raw): \Carbon\CarbonInterface
    {
        $raw = trim($raw);
        if (! preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $raw)) {
            throw ValidationException::withMessages([
                'sale_date_shamsi' => 'فرمت تاریخ فروش نامعتبر است.',
            ]);
        }
        try {
            return JalaliCalendar::parseShamsiDateStartOfDay($raw);
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'sale_date_shamsi' => $e->getMessage(),
            ]);
        }
    }
}
