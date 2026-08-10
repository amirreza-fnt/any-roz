<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\PublicUploads;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('parent')->orderByDesc('id')->get();

        return view('backend.category.IndexCategory', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $type = 'create';
        $category = null;
        $parents = Category::orderBy('title')->get();

        return view('backend.category.CreateOrUpdateCategory', compact('type', 'category', 'parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:4096',
        ]);

        $status = 'active';
        if (! $request->status) {
            $status = 'inactive';
        }

        $image = null;
        if ($request->hasFile('image')) {
            $image = PublicUploads::store($request->file('image'), 'images/category');
        }

        try {
            Category::create([
                'title' => $request->title,
                'status' => $status,
                'image' => $image,
                'parent_id' => $request->filled('parent_id') ? (int) $request->parent_id : null,
            ]);
            message('success', 'درج با موفقیت انجام شد.');

            return redirect()->route('admin.category.index');
        } catch (\Exception $e) {
            message('error', 'درج با خطا مواجه شد.');

            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return redirect()->route('admin.category.edit', $category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $type = 'edit';
        $parents = Category::where('id', '!=', $category->id)->orderBy('title')->get();

        return view('backend.category.CreateOrUpdateCategory', compact('type', 'category', 'parents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|max:4096',
        ]);

        if ($request->filled('parent_id') && (int) $request->parent_id === (int) $category->id) {
            return redirect()->back()->withErrors(['parent_id' => 'دسته نمی‌تواند والد خودش باشد.'])->withInput();
        }

        if ($request->filled('parent_id') && $this->wouldCreateCycle($category, (int) $request->parent_id)) {
            return redirect()->back()->withErrors(['parent_id' => 'انتخاب والد معتبر نیست (زیردستهٔ همین دسته قابل انتخاب نیست).'])->withInput();
        }

        $status = 'active';
        if (! $request->status) {
            $status = 'inactive';
        }

        if ($request->hasFile('image')) {
            $this->deleteCategoryImage($category);
            $category->image = PublicUploads::store($request->file('image'), 'images/category');
        }

        $category->title = $request->title;
        $category->status = $status;
        $category->parent_id = $request->filled('parent_id') ? (int) $request->parent_id : null;

        try {
            $category->save();
            message('success', 'ویرایش با موفقیت انجام شد.');

            return redirect()->route('admin.category.index');
        } catch (\Exception $e) {
            message('error', 'ویرایش با خطا مواجه شد.');

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->children()->exists()) {
                message('error', 'ابتدا زیردسته‌های این دسته را حذف یا منتقل کنید.');

                return redirect()->back();
            }
            $this->deleteCategoryImage($category);
            $category->delete();
            message('success', 'حذف با موفقیت انجام شد.');
        } catch (\Exception $e) {
            message('error', 'حذف با خطا مواجه شد.');
        }

        return redirect()->route('admin.category.index');
    }

    public function toggleStatus(Category $category)
    {
        $category->status = $category->status === 'active' ? 'inactive' : 'active';
        $category->save();
        message('success', 'وضعیت دسته به‌روزرسانی شد.');

        return redirect()->back();
    }

    private function wouldCreateCycle(Category $category, int $newParentId): bool
    {
        $ancestor = Category::find($newParentId);
        while ($ancestor) {
            if ($ancestor->id === $category->id) {
                return true;
            }
            $ancestor = $ancestor->parent_id ? Category::find($ancestor->parent_id) : null;
        }

        return false;
    }

    private function deleteCategoryImage(Category $category): void
    {
        PublicUploads::delete($category->image);
    }
}
