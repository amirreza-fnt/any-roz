@extends('backend.views.view')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@include('backend.marketing.buyers._map_script')
@endpush
