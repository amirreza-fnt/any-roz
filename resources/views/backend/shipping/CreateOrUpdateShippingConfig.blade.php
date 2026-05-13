@extends('backend.views.view')

@php
    $isEdit = ($type ?? '') === 'edit';
    $c = $config;
@endphp

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>{{ $isEdit ? 'ویرایش روش ارسال' : 'افزودن روش ارسال' }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.shipping-configs.index') }}">روش ارسال</a></li>
                    <li class="breadcrumb-item active">{{ $isEdit ? 'ویرایش' : 'افزودن' }}</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ $isEdit ? route('admin.shipping-configs.update', $c) : route('admin.shipping-configs.store') }}" class="card border-0 shadow-sm">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label>عنوان روش <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required maxlength="255" value="{{ old('name', $c?->name) }}" placeholder="مثلاً پست پیشتاز">
                    </div>
                    <div class="form-group col-md-4">
                        <label>ترتیب نمایش</label>
                        <input type="number" name="sort_order" class="form-control" min="0" max="99999" value="{{ old('sort_order', $c?->sort_order ?? 1) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>هزینه پایه ارسال (تومان)</label>
                        <input type="number" name="base_shipping_cost" class="form-control" min="0" required value="{{ old('base_shipping_cost', $c?->base_shipping_cost ?? 35000) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>بیمه پایه (تومان)</label>
                        <input type="number" name="base_insurance_cost" class="form-control" min="0" required value="{{ old('base_insurance_cost', $c?->base_insurance_cost ?? 5000) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>بسته‌بندی پایه (تومان)</label>
                        <input type="number" name="base_packaging_cost" class="form-control" min="0" required value="{{ old('base_packaging_cost', $c?->base_packaging_cost ?? 0) }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>حداکثر وزن بسته (کیلوگرم)</label>
                        <input type="number" name="package_weight_limit" class="form-control" min="1" required value="{{ old('package_weight_limit', $c?->package_weight_limit ?? 12) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>هزینه هر واحد وزن اضافه (تومان)</label>
                        <input type="number" name="extra_weight_cost" class="form-control" min="0" required value="{{ old('extra_weight_cost', $c?->extra_weight_cost ?? 0) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>توضیحات (برای کاربر یا پرسنل)</label>
                    <textarea name="description" class="form-control" rows="3" maxlength="8000">{{ old('description', $c?->description) }}</textarea>
                </div>

                <div class="form-group form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', ($c?->is_active ?? true) ? '1' : '0') === '1')>
                    <label class="form-check-label" for="is_active">روش فعال باشد</label>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('admin.shipping-configs.index') }}" class="btn btn-light">بازگشت</a>
                <button type="submit" class="btn btn-primary px-4">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection
