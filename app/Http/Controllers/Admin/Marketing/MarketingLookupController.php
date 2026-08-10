<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\MarketingBuyer;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MarketingLookupController extends Controller
{
    public function products(Request $request)
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

    public function buyers(Request $request)
    {
        $mid = (int) config('marketing.default_marketer_id');
        $q = $request->string('q')->toString();

        $rows = MarketingBuyer::query()
            ->where('marketer_id', $mid)
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($w) use ($like) {
                    $w->where('first_name', 'like', $like)
                        ->orWhere('last_name', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('store_name', 'like', $like);
                });
            })
            ->orderByDesc('id')
            ->limit(40)
            ->get();

        return response()->json([
            'results' => $rows->map(fn (MarketingBuyer $b) => [
                'id' => $b->id,
                'text' => $b->select2_label,
            ])->values(),
        ]);
    }

    public function buyerJson(MarketingBuyer $buyer)
    {
        if ((int) $buyer->marketer_id !== (int) config('marketing.default_marketer_id')) {
            abort(404);
        }

        return response()->json([
            'id' => $buyer->id,
            'first_name' => $buyer->first_name,
            'last_name' => $buyer->last_name,
            'phone' => $buyer->phone,
            'store_name' => $buyer->store_name,
        ]);
    }

    public function productJson(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'text' => $product->title.' ('.$product->tracking_code.')',
        ]);
    }

    public function provinces()
    {
        $rows = Province::query()->orderBy('name')->get(['id', 'name']);

        return response()->json($rows);
    }

    public function cities(Request $request)
    {
        $request->validate([
            'province_id' => 'required|integer|exists:provinces,id',
        ]);

        $rows = City::query()
            ->where('province_id', $request->integer('province_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($rows);
    }

    /**
     * Reverse geocode via Neshan (https://platform.neshan.org/docs/) — server-side with service API key.
     */
    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $key = (string) config('neshan.service_api_key');
        if ($key === '') {
            return response()->json([
                'ok' => false,
                'message' => 'برای آدرس‌یابی، متغیر NESHAN_SERVICE_API_KEY را در .env قرار دهید (کلید وب‌سرویس با سرویس «تبدیل نقطه به آدرس»).',
            ], 422);
        }

        $lat = $request->float('lat');
        $lng = $request->float('lng');
        $url = 'https://api.neshan.org/v5/reverse?'.http_build_query([
            'lat' => $lat,
            'lng' => $lng,
        ]);

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Api-Key' => $key,
                    'Accept' => 'application/json',
                ])
                ->get($url);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'اتصال به سرویس نشان برقرار نشد.',
            ], 502);
        }

        if (! $response->successful()) {
            return response()->json([
                'ok' => false,
                'message' => 'سرویس نشان پاسخ خطا داد.',
            ], 502);
        }

        $json = $response->json();
        $address = is_array($json) ? $this->pickNeshanAddress($json) : null;

        if ($address === null || $address === '') {
            return response()->json([
                'ok' => false,
                'message' => 'آدرسی برای این مختصات یافت نشد.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'address' => $address,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function pickNeshanAddress(array $data): ?string
    {
        foreach (['formatted_address', 'address', 'formattedAddress', 'neighbourhood', 'route_name', 'place', 'title'] as $k) {
            if (! empty($data[$k]) && is_string($data[$k])) {
                return trim($data[$k]);
            }
        }

        foreach (['location', 'result', 'data'] as $nested) {
            if (isset($data[$nested]) && is_array($data[$nested])) {
                $picked = $this->pickNeshanAddress($data[$nested]);
                if ($picked) {
                    return $picked;
                }
            }
        }

        return null;
    }
}
