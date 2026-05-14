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
     * Reverse geocode via ApiEco Cedar (see config/services.php apieco.*).
     */
    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $key = (string) config('services.apieco.key');
        if ($key === '') {
            return response()->json([
                'ok' => false,
                'message' => 'برای آدرس‌یابی سیدارمپ، متغیر APIECO_KEY را در فایل .env تنظیم کنید (بازار ApiEco).',
            ], 422);
        }

        $lat = $request->float('lat');
        $lng = $request->float('lng');
        $template = (string) config('services.apieco.reverse_geocode_url');
        $point = $lat.','.$lng;
        $url = str_replace(['{point}', '{lat}', '{lng}'], [$point, (string) $lat, (string) $lng], $template);

        $mapIrKey = (string) (config('services.apieco.map_ir_key') ?: $key);

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'apieco_key' => $key,
                    'Api-Key' => $mapIrKey,
                    'Accept' => 'application/json',
                ])
                ->post($url);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'اتصال به سرویس آدرس‌یابی برقرار نشد.',
            ], 502);
        }

        if (! $response->successful()) {
            return response()->json([
                'ok' => false,
                'message' => 'سرویس آدرس‌یابی پاسخ نامعتبر داد.',
            ], 502);
        }

        $json = $response->json();
        $address = is_array($json) ? $this->pickAddressFromGeocodeJson($json) : null;

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
    private function pickAddressFromGeocodeJson(array $data): ?string
    {
        foreach (['address', 'formatted_address', 'formattedAddress', 'text', 'title', 'name', 'label'] as $k) {
            if (! empty($data[$k]) && is_string($data[$k])) {
                return trim($data[$k]);
            }
        }

        foreach (['data', 'result', 'results', 'body', 'response'] as $nested) {
            if (isset($data[$nested]) && is_array($data[$nested])) {
                if ($nested === 'results') {
                    $first = reset($data[$nested]);
                    if (is_array($first)) {
                        $picked = $this->pickAddressFromGeocodeJson($first);
                        if ($picked) {
                            return $picked;
                        }
                    }
                } else {
                    $picked = $this->pickAddressFromGeocodeJson($data[$nested]);
                    if ($picked) {
                        return $picked;
                    }
                }
            }
        }

        return null;
    }
}
