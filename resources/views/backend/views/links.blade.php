<link rel="shortcut icon" href="{{ asset('assets/back-end/assets/media/image/favicon.png') }}">

<!-- Plugin styles -->
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/bundle.css') }}" type="text/css">

<!-- Datepicker -->
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/datepicker/daterangepicker.css') }}">

<!-- Slick -->
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/slick/slick.css') }}">
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/slick/slick-theme.css') }}">

<!-- Vmap -->
<link rel="stylesheet" href="{{ asset('assets/back-end/vendors/vmap/jqvmap.min.css') }}">

<!-- App styles -->
<link rel="stylesheet" href="{{ asset('assets/back-end/assets/css/app.css') }}" type="text/css">
<style>
    /* استایل‌های CSS */
    .alert-container {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 500px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        pointer-events: none; /* اجازه کلیک روی المان‌های زیرین */
    }

    .custom-alert {
        width: 100%;
        background: #fff;
        border-right: 4px solid #ef4444; /* قرمز برای خطا */
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        pointer-events: auto; /* فعال کردن کلیک روی خود باکس */
        
        /* انیمیشن ورود */
        animation: slideIn 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
        opacity: 0;
        transform: translateY(-20px);
    }

    .alert-content {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .alert-icon {
        color: #ef4444;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .alert-text {
        color: #1f2937;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .alert-text strong {
        display: block;
        margin-bottom: 2px;
        color: #dc2626;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .alert-close {
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .alert-close:hover {
        color: #ef4444;
        background: #fef2f2;
    }

    /* انیمیشن خروج */
    .custom-alert.closing {
        animation: slideOut 0.3s ease-in forwards;
    }

    @keyframes slideIn {
        0% {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes slideOut {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
    }

    /* ریسپانسیو برای موبایل */
    @media (max-width: 640px) {
        .alert-container {
            top: 10px;
            width: 95%;
        }
        
        .custom-alert {
            padding: 12px;
        }
        
        .alert-text {
            font-size: 0.85rem;
        }
    }
</style>