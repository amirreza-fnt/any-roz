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
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        $syncData = $this->buildWeightSyncFromRequest($request);
        if ($syncData instanceof \Illuminate\Http\RedirectResponse) {
            return $syncData;
        }

        $sumStock = array_sum(array_column($syncData, 'stock'));

        $product = null;
        try {
            DB::transaction(function () use ($request, $slug, $trackingCode, $syncData, $sumStock, &$product) {
                $product = Product::create([
                    'title' => $request->title,
                    'slug' => $slug,
                    'tracking_code' => $trackingCode,
                    'category_id' => (int) $request->category_id,
                    'price' => (int) $request->price,
                    'price_buy' => (int) $request->price_buy,
                    'price_discounted' => (int) $request->price_discounted,
                    'stock' => $sumStock,
                    'status' => $request->status ? 'active' : 'inactive',
                    'suggested' => $request->suggested ? 'active' : 'inactive',
                    'mini_description' => $request->mini_description,
                    'description' => $request->description,
                ]);

                $product->typeOfWeights()->sync($this->syncPivotFormat($syncData));
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

        $syncData = $this->buildWeightSyncFromRequest($request);
        if ($syncData instanceof \Illuminate\Http\RedirectResponse) {
            return $syncData;
        }

        $sumStock = array_sum(array_column($syncData, 'stock'));

        try {
            DB::transaction(function () use ($request, $product, $slug, $trackingCode, $syncData, $sumStock) {
                $product->update([
                    'title' => $request->title,
                    'slug' => $slug,
                    'tracking_code' => $trackingCode,
                    'category_id' => (int) $request->category_id,
                    'price' => (int) $request->price,
                    'price_buy' => (int) $request->price_buy,
                    'price_discounted' => (int) $request->price_discounted,
                    'stock' => $sumStock,
                    'status' => $request->status ? 'active' : 'inactive',
                    'suggested' => $request->suggested ? 'active' : 'inactive',
                    'mini_description' => $request->mini_description,
                    'description' => $request->description,
                ]);

                $product->typeOfWeights()->sync($this->syncPivotFormat($syncData));
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
            'price' => 'required|integer|min:0',
            'price_buy' => 'required|integer|min:0',
            'price_discounted' => 'required|integer|min:0',
            'mini_description' => 'required|string',
            'description' => 'required|string',
        ]);
    }

    /**
     * @return array<int, array{stock:int}>|\Illuminate\Http\RedirectResponse
     */
    private function buildWeightSyncFromRequest(Request $request)
    {
        $ids = $request->input('weight_type_ids', []);
        if (! is_array($ids) || count($ids) < 1) {
            return redirect()->back()->withErrors(['weight_type_ids' => 'حداقل یک نوع وزن انتخاب کنید.'])->withInput();
        }

        $ids = array_unique(array_map('intval', $ids));
        $validIds = TypeOfWeight::whereIn('id', $ids)->pluck('id')->all();
        if (count($validIds) !== count($ids)) {
            return redirect()->back()->withErrors(['weight_type_ids' => 'نوع وزن انتخاب‌شده معتبر نیست.'])->withInput();
        }

        $weights = $request->input('weights', []);
        $sync = [];
        foreach ($ids as $id) {
            if (! isset($weights[$id]['stock']) && ! isset($weights[(string) $id]['stock'])) {
                return redirect()->back()->withErrors(["weights.$id.stock" => 'موجودی برای همهٔ انواع وزن الزامی است.'])->withInput();
            }
            $stock = (int) ($weights[$id]['stock'] ?? $weights[(string) $id]['stock'] ?? -1);
            if ($stock < 0) {
                return redirect()->back()->withErrors(["weights.$id.stock" => 'موجودی نامعتبر است.'])->withInput();
            }
            $sync[$id] = ['stock' => $stock];
        }

        return $sync;
    }

    /**
     * @param  array<int, array{stock:int}>  $syncData
     */
    private function syncPivotFormat(array $syncData): array
    {
        $out = [];
        foreach ($syncData as $id => $row) {
            $out[$id] = ['stock' => $row['stock']];
        }

        return $out;
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
