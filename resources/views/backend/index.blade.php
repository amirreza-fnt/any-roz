@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container">
        <div class="page-header mb-4">
            <h4 class="mb-1">داشبورد مدیریت</h4>
            <p class="text-muted mb-0 small">خلاصهٔ وضعیت فعلی فروشگاه — {{ auth('admin')->user()->full_name }}</p>
        </div>

        <div class="row">
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-1">سفارش‌ها</div>
                                <div class="h3 mb-0 font-weight-bold text-primary">{{ number_format($stats['orders']) }}</div>
                            </div>
                            <span class="avatar avatar-sm bg-primary-bright text-primary rounded-circle"><i data-feather="file-text" class="width-18 height-18"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-1">محصولات</div>
                                <div class="h3 mb-0 font-weight-bold text-success">{{ number_format($stats['products']) }}</div>
                            </div>
                            <span class="avatar avatar-sm bg-success-bright text-success rounded-circle"><i data-feather="package" class="width-18 height-18"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-1">دسته‌ها</div>
                                <div class="h3 mb-0 font-weight-bold text-info">{{ number_format($stats['categories']) }}</div>
                            </div>
                            <span class="avatar avatar-sm bg-info-bright text-info rounded-circle"><i data-feather="layers" class="width-18 height-18"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="text-muted small mb-1">کاربران سایت</div>
                                <div class="h3 mb-0 font-weight-bold text-secondary">{{ number_format($stats['users']) }}</div>
                            </div>
                            <span class="avatar avatar-sm bg-secondary-bright text-secondary rounded-circle"><i data-feather="users" class="width-18 height-18"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">مقالات</div>
                        <div class="h4 mb-0 font-weight-bold">{{ number_format($stats['articles']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">فروش بازاریابی در انتظار حسابداری</div>
                        <div class="h4 mb-0 font-weight-bold text-warning">{{ number_format($stats['marketing_pending']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">کدهای هدیه</div>
                        <div class="h4 mb-0 font-weight-bold">{{ number_format($stats['gift_codes']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">کدهای تخفیف</div>
                        <div class="h4 mb-0 font-weight-bold">{{ number_format($stats['discount_codes']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
