@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.css">
<style>#buyer-map { min-height: 320px; }</style>
@endpush

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header">
            <h4>افزودن خریدار</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.marketing') }}">بازاریابی</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketing.buyers.index') }}">خریداران</a></li>
                    <li class="breadcrumb-item active">افزودن</li>
                </ol>
            </nav>
        </div>

        <form method="post" action="{{ route('admin.marketing.buyers.store') }}" class="card border-0 shadow-sm">
            @csrf
            <div class="card-body">
                @include('backend.marketing.buyers._form', ['buyer' => null, 'provinces' => $provinces, 'cities' => collect()])
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
                <a href="{{ route('admin.marketing.buyers.index') }}" class="btn btn-light">بازگشت</a>
                <button type="submit" class="btn btn-primary px-4">ذخیره</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.js"></script>
@include('backend.marketing.buyers._map_script')
@endpush
