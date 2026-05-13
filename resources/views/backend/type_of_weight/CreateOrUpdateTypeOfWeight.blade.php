@extends('backend.views.view')

@section('main')

@php
    $isEdit = $type === 'edit' && $typeOfWeight;
    $pageTitle = $isEdit ? 'ویرایش نوع وزن' : 'افزودن نوع وزن';
    $formAction = $isEdit ? route('admin.type-of-weights.update', $typeOfWeight) : route('admin.type-of-weights.store');
@endphp

<div class="main-content">
    <div class="container">

        <div class="page-header">
            <h4>{{ $pageTitle }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">خانه</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.type-of-weights.index') }}">انواع وزن</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                        <a href="{{ route('admin.type-of-weights.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i data-feather="arrow-right" class="width-16 height-16"></i>
                            <span class="mr-1">بازگشت به لیست</span>
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-4">{{ $pageTitle }}</h6>

                        <div id="alert-container" class="alert-container"></div>

                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="custom-alert alert-error mb-3" role="alert">
                                    <div class="alert-content">
                                        <div class="alert-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                            </svg>
                                        </div>
                                        <div class="alert-text">
                                            <strong>خطا:</strong> {{ $error }}
                                        </div>
                                    </div>
                                    <button type="button" class="alert-close" onclick="closeAlert(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <form class="row" method="post" action="{{ $formAction }}" id="type-of-weight-form">
                                    @csrf
                                    @if ($isEdit)
                                        @method('PUT')
                                    @endif

                                    <div class="form-group col-lg-6 col-12">
                                        <label for="tow-title">عنوان</label>
                                        <input name="title" type="text" class="form-control text-left" id="tow-title" value="{{ old('title', $typeOfWeight->title ?? '') }}" placeholder="مثلاً کیلوگرم، گرم، بسته" dir="ltr" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-12">
                                        <label for="tow-weight">مقدار وزن (اختیاری)</label>
                                        <input name="weight" type="number" min="0" class="form-control text-left" id="tow-weight" value="{{ old('weight', $typeOfWeight->weight ?? '') }}" placeholder="عدد صحیح — در صورت عدم نیاز خالی بگذارید" dir="ltr">
                                        <small class="form-text text-muted">مطابق فیلد اختیاری وزن در دیتابیس.</small>
                                    </div>

                                    <div class="form-group col-12 mt-2">
                                        <small class="form-text text-muted d-block mb-2">در صورت اطمینان از صحت اطلاعات، ذخیره را بزنید.</small>
                                        <button type="submit" class="btn btn-primary pl-4 pr-4">
                                            {{ $isEdit ? 'ذخیرهٔ تغییرات' : 'ثبت' }}
                                        </button>
                                        <a href="{{ route('admin.type-of-weights.index') }}" class="btn btn-light mr-2">انصراف</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
