<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd('index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $type = 'create';
        return view('backend.category.CreateOrUpdateCategory',compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|max:255',
        ]);

        $status = 'active';
        if(!$request->status){
            $status = 'inactive';
        }

        $image = null;
         if ($request->hasFile('image')) {
            // این خط فایل را در پوشه storage/app/public/images ذخیره می‌کند
            // و نام فایل را به صورت تصادفی (مثلا abc123.jpg) تولید می‌کند
            $path = $request->file('image')->store('images/category', 'public');

            // اگر می‌خواهید آدرس عمومی (URL) را داشته باشید:
            // $publicUrl = asset('storage/' . $path);
            $image = $path;
        }
        dd($image);
            
        try{
            $category = Category::create([
                'title' => $request->title,
                'status' => $status,
                'image' => $image,
                'parent_id' => $request->parent_id,
            ]);
            message('success','درج با موفقیت انجام شد.');
            return redirect()->back();
        }catch(\Exception $e){
            message('error','درج با خطا مواجه شد.');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        dd('show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        dd('edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        dd('update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        dd('destroy');
    }
}
