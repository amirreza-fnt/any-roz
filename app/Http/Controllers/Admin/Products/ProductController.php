<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TypeOfWeight;
use App\Support\PublicUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'typeOfWeights', 'images'])->orderByDesc('id')->get();

        return view('backend.product.IndexProduct', compact('products'));
    }

    public function create()
    {
        $type = 'create';
        $product = null;
        $categories = Category::orderBy('title')->get();
        $typeOfWeights = TypeOfWeight::orderBy('title')->get();

        return view('backend.product.CreateOrUpdateProduct', compact('type', 'product', 'categories', 'typeOfWeights'));
    }

    public function store(Request $request)
    {
        $this->validateProduct($request);

        $slug = $this->resolveUniqueSlug($request->input('slug'), $request->input('title'));
        $trackingCode = $this->resolveUniqueTrackingCode($request->input('tracking_code'));

        $variantRows = $this->collectVariantRows($request);

        $product = null;
        try {
            DB::transaction(function () use ($request, $slug, $trackingCode, $variantRows, &$product) {
                $payload = $this->buildProductPriceStockPayload($request, $variantRows);

                $product = Product::create([
                    'title' => $request->title,
                    'slug' => $slug,
                    'tracking_code' => $trackingCode,
                    'category_id' => (int) $request->category_id,
                    'price' => $payload['price'],
                    'price_buy' => $payload['price_buy'],
                    'price_discounted' => $payload['price_discounted'],
                    'stock' => $payload['stock'],
                    'status' => $request->status ? 'active' : 'inactive',
                    'suggested' => $request->suggested ? 'active' : 'inactive',
                    'mini_description' => $request->mini_description,
                    'description' => $request->description,
                ]);

                if ($payload['has_variants']) {
                    $product->typeOfWeights()->sync($variantRows);
                } else {
                    $product->typeOfWeights()->detach();
                }
            });

            message('success', 'محصول با موفقیت ثبت شد.');

            return redirect()->route('admin.products.edit', $product);
        } catch (\Exception $e) {
            message('error', 'ثبت محصول با خطا مواجه شد.');

            return redirect()->back()->withInput();
        }
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    public function edit(Product $product)
    {
        $type = 'edit';
        $product->load(['typeOfWeights', 'images']);
        $categories = Category::orderBy('title')->get();
        $typeOfWeights = TypeOfWeight::orderBy('title')->get();

        return view('backend.product.CreateOrUpdateProduct', compact('type', 'product', 'categories', 'typeOfWeights'));
    }

    public function update(Request $request, Product $product)
    {
        $this->validateProduct($request, $product->id);

        $slug = $this->resolveUniqueSlug($request->input('slug'), $request->input('title'), $product->id);
        $trackingCode = $this->resolveUniqueTrackingCode($request->input('tracking_code'), $product->id);

        $variantRows = $this->collectVariantRows($request);

        try {
            DB::transaction(function () use ($request, $product, $slug, $trackingCode, $variantRows) {
                $payload = $this->buildProductPriceStockPayload($request, $variantRows);

                $product->update([
                    'title' => $request->title,
                    'slug' => $slug,
                    'tracking_code' => $trackingCode,
                    'category_id' => (int) $request->category_id,
                    'price' => $payload['price'],
                    'price_buy' => $payload['price_buy'],
                    'price_discounted' => $payload['price_discounted'],
                    'stock' => $payload['stock'],
                    'status' => $request->status ? 'active' : 'inactive',
                    'suggested' => $request->suggested ? 'active' : 'inactive',
                    'mini_description' => $request->mini_description,
                    'description' => $request->description,
                ]);

                if ($payload['has_variants']) {
                    $product->typeOfWeights()->sync($variantRows);
                } else {
                    $product->typeOfWeights()->detach();
                }
            });

            message('success', 'محصول به‌روزرسانی شد.');

            return redirect()->route('admin.products.edit', $product);
        } catch (\Exception $e) {
            message('error', 'ویرایش محصول با خطا مواجه شد.');

            return redirect()->back()->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            foreach ($product->images as $image) {
                PublicUploads::delete($image->path);
                $image->delete();
            }
            $product->typeOfWeights()->detach();
            $product->delete();
            message('success', 'محصول حذف شد.');
        } catch (\Exception $e) {
            message('error', 'حذف محصول با خطا مواجه شد.');
        }

        return redirect()->route('admin.products.index');
    }

    public function toggleStatus(Product $product)
    {
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();
        message('success', 'وضعیت نمایش محصول تغییر کرد.');

        return redirect()->back();
    }

    public function toggleSuggested(Product $product)
    {
        $product->suggested = $product->suggested === 'active' ? 'inactive' : 'active';
        $product->save();
        message('success', 'وضعیت پیشنهادی محصول تغییر کرد.');

        return redirect()->back();
    }

    public function storeImage(Request $request, Product $product)
    {
        $request->validate([
            'file' => 'required|image|max:8192',
        ]);

        $path = PublicUploads::store($request->file('file'), 'images/product');
        $next = (int) ($product->images()->max('sort_order') ?? 0) + 1;
        $image = $product->images()->create([
            'path' => $path,
            'sort_order' => $next,
        ]);

        return response()->json([
            'success' => true,
            'id' => $image->id,
            'url' => PublicUploads::url($path),
        ]);
    }

    public function destroyImage(Request $request, Product $product, ProductImage $product_image)
    {
        if ((int) $product_image->product_id !== (int) $product->id) {
            abort(404);
        }

        PublicUploads::delete($product_image->path);
        $product_image->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        message('success', 'تصویر حذف شد.');

        return redirect()->back();
    }

    private function validateProduct(Request $request, ?int $productId = null): void
    {
        $slugRule = $productId
            ? Rule::unique('products', 'slug')->ignore($productId)
            : Rule::unique('products', 'slug');

        $trackingRule = $productId
            ? Rule::unique('products', 'tracking_code')->ignore($productId)
            : Rule::unique('products', 'tracking_code');

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', $slugRule],
            'tracking_code' => ['nullable', 'string', 'max:64', $trackingRule],
            'category_id' => 'required|exists:categories,id',
            'mini_description' => 'required|string',
            'description' => 'required|string',
        ]);

        $weightIds = array_values(array_unique(array_filter(array_map('intval', (array) $request->input('weight_type_ids', [])))));

        if (count($weightIds) === 0) {
            Validator::make($request->all(), [
                'price' => 'required|integer|min:0',
                'price_buy' => 'required|integer|min:0',
                'price_discounted' => 'required|integer|min:0',
                'stock' => 'required|integer|min:0',
            ])->validate();

            return;
        }

        $existingIds = TypeOfWeight::whereIn('id', $weightIds)->pluck('id')->all();
        if (count($existingIds) !== count($weightIds)) {
            throw ValidationException::withMessages([
                'weight_type_ids' => 'یکی از انواع وزن انتخاب‌شده معتبر نیست.',
            ]);
        }

        $rules = [];
        foreach ($weightIds as $id) {
            foreach (['stock', 'price', 'price_buy', 'price_discounted'] as $field) {
                $rules['weights.'.$id.'.'.$field] = 'required|integer|min:0';
            }
        }

        Validator::make($request->all(), $rules)->validate();
    }

    /**
     * @return array<int, array<string, int>>
     */
    private function collectVariantRows(Request $request): array
    {
        $weightIds = array_values(array_unique(array_filter(array_map('intval', (array) $request->input('weight_type_ids', [])))));

        if (count($weightIds) === 0) {
            return [];
        }

        $weights = $request->input('weights', []);
        $rows = [];

        foreach ($weightIds as $id) {
            $w = $weights[$id] ?? $weights[(string) $id] ?? [];
            $rows[$id] = [
                'stock' => (int) ($w['stock'] ?? 0),
                'price' => (int) ($w['price'] ?? 0),
                'price_buy' => (int) ($w['price_buy'] ?? 0),
                'price_discounted' => (int) ($w['price_discounted'] ?? 0),
            ];
        }

        return $rows;
    }

    /**
     * @param  array<int, array<string, int>>  $variantRows
     * @return array{price: int, price_buy: int, price_discounted: int, stock: int, has_variants: bool}
     */
    private function buildProductPriceStockPayload(Request $request, array $variantRows): array
    {
        if (count($variantRows) === 0) {
            return [
                'has_variants' => false,
                'price' => (int) $request->price,
                'price_buy' => (int) $request->price_buy,
                'price_discounted' => (int) $request->price_discounted,
                'stock' => (int) $request->stock,
            ];
        }

        $stocks = array_column($variantRows, 'stock');
        $prices = array_column($variantRows, 'price');
        $buys = array_column($variantRows, 'price_buy');
        $discounted = array_column($variantRows, 'price_discounted');

        return [
            'has_variants' => true,
            'price' => (int) min($prices),
            'price_buy' => (int) min($buys),
            'price_discounted' => (int) min($discounted),
            'stock' => (int) array_sum($stocks),
        ];
    }

    private function resolveUniqueSlug(?string $slugInput, string $title, ?int $ignoreId = null): string
    {
        $base = $slugInput ? Str::slug($slugInput) : Str::slug($title);
        if ($base === '') {
            $base = 'product-'.Str::lower(Str::random(8));
        }

        $slug = $base;
        $i = 1;
        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function resolveUniqueTrackingCode(?string $input, ?int $ignoreId = null): string
    {
        $code = $input ? trim($input) : '';
        if ($code === '') {
            do {
                $code = 'TR-'.strtoupper(Str::random(10));
            } while (Product::where('tracking_code', $code)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists());

            return $code;
        }

        return $code;
    }
}
