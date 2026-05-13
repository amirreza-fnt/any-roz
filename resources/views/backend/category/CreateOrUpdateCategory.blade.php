@extends('backend.views.view')

@section('main')

<!-- begin::main-content -->
<div class="main-content">
    <!-- begin::container -->
    <div class="container">
        <div class="page-header">
            <h4>داشبورد مدیریت آنی رز</h4>
            <small class="">خوش آمدید، <span class="text-primary">جان اسنو</span></small>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                    <h6 class="card-title">نمای کلی</h6>
                    <!-- کانتینر نمایش پیام‌ها -->
                <div id="alert-container" class="alert-container"></div>

                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="custom-alert alert-error mb-3" role="alert">
                            <div class="alert-content">
                                <!-- آیکون خطا -->
                                <div class="alert-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                </div>
                                
                                <!-- متن پیام -->
                                <div class="alert-text">
                                    <strong>خطا:</strong> {{ $error }}
                                </div>
                            </div>
                            
                            <!-- دکمه بستن -->
                            <button class="alert-close" onclick="closeAlert(this)">
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
                            <form class="row" method="post" action="{{ route('admin.category.store') }}" enctype="multipart/form-data">
                                <div class="form-group col-lg-6 col-12">
                                    <label for="exampleInputEmail1">عنوان</label>
                                    <input name="title" type="text" class="form-control text-left" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="عنوان دسته بندی خود را وارد کنید" dir="ltr">
                                </div>
                                <div class="form-group col-lg-6 col-12">
                                    <label for="exampleInputPassword1">تصویر دسته بندی</label>
                                    <div class="custom-file">
                                        <input name="image" type="file" class="custom-file-input" id="customFile">
                                        <label class="custom-file-label" for="customFile">انتخاب تصویر</label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-12">
                                    <label for="parent">دسته والد</label>
                                    <select name="parent_id" class="form-control" id="parent">
                                        <option selected value="">انتخاب  کنید</option>
                                        <option value="1">انتخاب پیش فرض</option>
                                        <option value="2">انتخاب پیش فرض</option>
                                        <option>انتخاب پیش فرض</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 col-12 mt-2">
                                    <small id="emailHelp" class="form-text text-muted">اگر از درست بودن اطلاعات ورودی اطمینان دارید بر روی دکمه زیر کلیک کنید.
                                    </small>
                                    <button type="submit" class="btn btn-primary">ثبت</button>
                                </div>
                                <div class="form-group col-lg-6 col-12 mt-2">
                                    <div class="custom-control custom-switch custom-checkbox-success">
                                        <input name="status" type="checkbox" class="custom-control-input" id="customSwitch3" checked>
                                        <label class="custom-control-label" for="customSwitch3">وضیعت دسته بندی</label>
                                    </div>
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
