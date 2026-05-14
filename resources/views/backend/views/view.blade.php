<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    @include('backend.views.meta')
    <title>پنل مدیریت</title>
    @include('backend.views.links')
    @stack('styles')
    <style>
        /* چسباندن فوتر به پایین نما حتی وقتی محتوا کوتاه است */
        body.admin-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
            margin: 0;
        }
        body.admin-shell > .navigation {
            flex-shrink: 0;
        }
        body.admin-shell > #main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }
        body.admin-shell > #main > .header {
            flex-shrink: 0;
        }
        body.admin-shell > #main > .main-content {
            flex: 1 0 auto;
        }
        body.admin-shell > #main > footer {
            flex-shrink: 0;
            margin-top: auto;
        }
    </style>
</head>
<body class="admin-shell">
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
