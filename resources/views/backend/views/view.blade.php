<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    @include('backend.views.meta')
    <title>پنل مدیریت</title>
    @include('backend.views.links')
    @stack('styles')
</head>
<body>
<!-- begin::preloader-->
@include('backend.views.preloader')
<!-- end::preloader -->

<!-- begin::navigation -->
<div class="navigation">
    <!-- begin::navigation header -->
    @include('backend.views.profile')
    <!-- end::navigation header -->

    <!-- begin::navigation menu -->
    @include('backend.views.sidbar')
    <!-- end::navigation menu -->
</div>
<!-- end::navigation -->

<div id="main">
<!-- begin::header -->
@include('backend.views.header')
<!-- end::header -->

<!-- begin::main -->
@yield('main')
<!-- end::main -->

<!-- begin::footer -->
@include('backend.views.footer')
<!-- end::footer -->
</div>

<!-- Plugin scripts -->
@include('backend.views.scripts')
@stack('scripts')
</body>
</html>
