<?php

namespace App\Http\Controllers\Admin\Colleagues;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\MarketingSale;
use App\Models\MarketingSaleItem;
use App\Models\Product;
use App\Support\JalaliCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ColleagueSaleController extends Controller
{
    private function colleagueId(): int
    {
        return (int) auth('admin')->id();
    }

    public function index()
    {
        $sales = MarketingSale::query()
            ->where('marketer_id', $this->colleagueId())
            ->where('sale_type', MarketingSale::SALE_TYPE_COLLEAGUE)
            ->with(['items.product', 'order'])
            ->orderByDesc('id')
            ->get();

        return view('backend.colleagues.sales.index', compact('sales'));
    }

    public function create()
    {
        $colleague = Admin::query()->where('id', $this->colleagueId())->where('is_colleague', true)->first();

        return view('backend.colleagues.sales.create', compact('colleague'));
    }

    public function store(Request $request)
    {
        $cid = $this->colleagueId();
        $colleague = Admin::query()->where('id', $cid)->where('is_colleague', true)->first();

        $base = $request->validate([
            'sale_date_shamsi' => ['required', 'string', 'max:32'],
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.unit_price' => ['required', 'integer', 'min:1'],
            'items.*.quantity_text' => ['required', 'string', 'max:500'],
        ]);

        $saleDate = $this->parseSaleDate($base['sale_date_shamsi']);

        DB::transaction(function () use ($base, $colleague, $cid, $saleDate) {
            $sale = MarketingSale::create([
                'marketer_id' => $cid,
                'buyer_id' => null,
                'sale_type' => MarketingSale::SALE_TYPE_COLLEAGUE,
                'status' => MarketingSale::STATUS_PENDING,
                'buyer_first_name' => $colleague?->first_name ?? '',
                'buyer_last_name' => $colleague?->last_name ?? '',
                'buyer_phone' => $colleague?->phone ?? '',
                'buyer_store_name' => $colleague?->store_name,
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

        message('success', 'پیش‌فاکتور خرید کلی ثبت شد و برای بررسی حسابداری ارسال گردید.');

        return redirect()->route('admin.colleague.sales.index');
    }

    public function show(MarketingSale $marketing_sale)
    {
        $this->authorizeSale($marketing_sale);
        $marketing_sale->load(['items.product.images', 'order', 'reviewer']);

        return view('backend.colleagues.sales.show', ['sale' => $marketing_sale]);
    }

    public function destroy(MarketingSale $marketing_sale)
    {
        $this->authorizeSale($marketing_sale);
        if (! $marketing_sale->isPending()) {
            message('warning', 'فقط پیش‌فاکتورهای در انتظار حسابداری قابل حذف هستند.');

            return redirect()->back();
        }
        $marketing_sale->delete();
        message('success', 'پیش‌فاکتور حذف شد.');

        return redirect()->route('admin.colleague.sales.index');
    }

    public function apiProducts(Request $request)
    {
        $q = $request->string('q')->toString();
        $rows = Product::query()
            ->orderBy('title')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('title', 'like', '%'.$q.'%')
                        ->orWhere('tracking_code', 'like', '%'.$q.'%');
                });
            })
            ->limit(40)
            ->get(['id', 'title', 'tracking_code']);

        return response()->json([
            'results' => $rows->map(fn (Product $p) => [
                'id' => $p->id,
                'text' => $p->title.' ('.$p->tracking_code.')',
            ])->values(),
        ]);
    }

    public function apiProductJson(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'text' => $product->title.' ('.$product->tracking_code.')',
        ]);
    }

    private function authorizeSale(MarketingSale $sale): void
    {
        if ((int) $sale->marketer_id !== $this->colleagueId() || ! $sale->isColleagueSale()) {
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