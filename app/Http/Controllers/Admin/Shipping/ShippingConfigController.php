<?php

namespace App\Http\Controllers\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingConfig;
use Illuminate\Http\Request;

class ShippingConfigController extends Controller
{
    public function index()
    {
        $configs = ShippingConfig::query()->orderBy('sort_order')->orderByDesc('id')->get();

        return view('backend.shipping.IndexShippingConfig', compact('configs'));
    }

    public function create()
    {
        $type = 'create';
        $config = null;

        return view('backend.shipping.CreateOrUpdateShippingConfig', compact('type', 'config'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        ShippingConfig::create($data);

        message('success', 'روش ارسال ثبت شد.');

        return redirect()->route('admin.shipping-configs.index');
    }

    public function show(ShippingConfig $shipping_config)
    {
        return redirect()->route('admin.shipping-configs.edit', $shipping_config);
    }

    public function edit(ShippingConfig $shipping_config)
    {
        $type = 'edit';
        $config = $shipping_config;

        return view('backend.shipping.CreateOrUpdateShippingConfig', compact('type', 'config'));
    }

    public function update(Request $request, ShippingConfig $shipping_config)
    {
        $data = $this->validated($request);
        $shipping_config->update($data);

        message('success', 'روش ارسال به‌روزرسانی شد.');

        return redirect()->route('admin.shipping-configs.index');
    }

    public function toggleStatus(ShippingConfig $shipping_config)
    {
        $shipping_config->is_active = ! $shipping_config->is_active;
        $shipping_config->save();
        message('success', 'وضعیت روش ارسال تغییر کرد.');

        return redirect()->back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_shipping_cost' => 'required|integer|min:0',
            'base_insurance_cost' => 'required|integer|min:0',
            'base_packaging_cost' => 'required|integer|min:0',
            'package_weight_limit' => 'required|integer|min:1',
            'extra_weight_cost' => 'required|integer|min:0',
            'sort_order' => 'required|integer|min:0|max:99999',
            'description' => 'nullable|string|max:8000',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
