<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\TypeOfWeight;
use App\Support\PublicUploads;
use Illuminate\Http\Request;

class TypeOfWeightController extends Controller
{
    public function index()
    {
        $typeOfWeights = TypeOfWeight::orderByDesc('id')->get();

        return view('backend.type_of_weight.IndexTypeOfWeight', compact('typeOfWeights'));
    }

    public function create()
    {
        $type = 'create';
        $typeOfWeight = null;

        return view('backend.type_of_weight.CreateOrUpdateTypeOfWeight', compact('type', 'typeOfWeight'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'weight' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:4096',
        ]);

        $status = 'active';
        if (! $request->status) {
            $status = 'inactive';
        }

        $image = null;
        if ($request->hasFile('image')) {
            $image = PublicUploads::store($request->file('image'), 'images/type-of-weight');
        }

        try {
            TypeOfWeight::create([
                'title' => $request->title,
                'weight' => $request->filled('weight') ? (int) $request->weight : null,
                'status' => $status,
                'image' => $image,
            ]);
            message('success', 'درج با موفقیت انجام شد.');

            return redirect()->route('admin.type-of-weights.index');
        } catch (\Exception $e) {
            message('error', 'درج با خطا مواجه شد.');

            return redirect()->back()->withInput();
        }
    }

    public function show(TypeOfWeight $type_of_weight)
    {
        return redirect()->route('admin.type-of-weights.edit', $type_of_weight);
    }

    public function edit(TypeOfWeight $type_of_weight)
    {
        $type = 'edit';
        $typeOfWeight = $type_of_weight;

        return view('backend.type_of_weight.CreateOrUpdateTypeOfWeight', compact('type', 'typeOfWeight'));
    }

    public function update(Request $request, TypeOfWeight $type_of_weight)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'weight' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:4096',
        ]);

        $status = 'active';
        if (! $request->status) {
            $status = 'inactive';
        }

        if ($request->hasFile('image')) {
            PublicUploads::delete($type_of_weight->image);
            $type_of_weight->image = PublicUploads::store($request->file('image'), 'images/type-of-weight');
        }

        $type_of_weight->title = $request->title;
        $type_of_weight->weight = $request->filled('weight') ? (int) $request->weight : null;
        $type_of_weight->status = $status;

        try {
            $type_of_weight->save();
            message('success', 'ویرایش با موفقیت انجام شد.');

            return redirect()->route('admin.type-of-weights.index');
        } catch (\Exception $e) {
            message('error', 'ویرایش با خطا مواجه شد.');

            return redirect()->back()->withInput();
        }
    }

    public function destroy(TypeOfWeight $type_of_weight)
    {
        try {
            if ($type_of_weight->products()->exists()) {
                message('error', 'این نوع وزن به محصولی متصل است و قابل حذف نیست.');

                return redirect()->back();
            }
            PublicUploads::delete($type_of_weight->image);
            $type_of_weight->delete();
            message('success', 'حذف با موفقیت انجام شد.');
        } catch (\Exception $e) {
            message('error', 'حذف با خطا مواجه شد.');
        }

        return redirect()->route('admin.type-of-weights.index');
    }

    public function toggleStatus(TypeOfWeight $type_of_weight)
    {
        $type_of_weight->status = $type_of_weight->status === 'active' ? 'inactive' : 'active';
        $type_of_weight->save();
        message('success', 'وضعیت به‌روزرسانی شد.');

        return redirect()->back();
    }
}
