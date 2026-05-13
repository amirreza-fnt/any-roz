'use strict';

(function ($) {
    var $window = $(window);
    var $body = $('body');
    var $navigation = $('.navigation');
    
    // تنظیمات NiceScroll
    var niceScrollOptions = {
        cursorcolor: "#495057",
        cursorwidth: "8px",
        cursorborder: "none",
        grabcursor: true
    };

    // ============================================
    // 1. راه‌اندازی اولیه
    // ============================================
    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // ============================================
    // 2. مدیریت آیکون‌های زیرمنو (برای تمام لایه‌ها)
    // ============================================
    function initSubmenuArrows() {
        // این خط تمام آیتم‌های منو را در تمام عمق‌ها پیدا می‌کند
        // و اگر زیرمنوی مستقیم (ul) داشته باشند، آیکون اضافه می‌کند
        $('.navigation ul li').each(function () {
            var $li = $(this);
            var $subMenu = $li.children('ul'); // فقط زیرمنوی مستقیم

            if ($subMenu.length > 0) {
                var $link = $li.children('a'); // لینک والد
                
                // اگر آیکون فلش قبلاً اضافه نشده
                if ($link.find('.sub-menu-arrow').length === 0) {
                    // اضافه کردن آیکون فلش (بسته)
                    $link.append('<i class="sub-menu-arrow ti-angle-up"></i>');
                }
            }
        });
    }

    // اجرای اولیه
    $(document).ready(function () {
        initSubmenuArrows();
        
        if ($navigation.length) {
            $navigation.niceScroll(niceScrollOptions);
        }
    });

    // ============================================
    // 3. مدیریت کلیک روی منوها (تغییر کلیدی اینجاست)
    // ============================================
    
    // نکته مهم: انتخابگر را از '> ul > li > a' به '.navigation-menu-body li a' تغییر دادیم
    // تا تمام لایه‌های تودرتو را پوشش دهد.
    $(document).on('click', '.navigation-menu-body li > a', function (e) {
        var $this = $(this);
        var $parentLi = $this.parent('li');
        var $subMenu = $this.next('ul'); // زیرمنوی بعدی از همان لینک
        var $arrow = $this.find('.sub-menu-arrow');

        // اگر این آیتم زیرمنو دارد
        if ($subMenu.length) {
            e.preventDefault(); // جلوگیری از رفتن به لینک

            // بستن سایر منوهای باز در همان سطح (اختیاری)
            // اگر می‌خواهید فقط یک منو در هر سطح باز باشد، این خطوط را نگه دارید
            $parentLi.siblings().find('> ul').slideUp(300);
            $parentLi.siblings().find('.sub-menu-arrow').removeClass('ti-minus').addClass('ti-angle-up');

            // باز یا بسته کردن منوی فعلی
            if ($subMenu.is(':visible')) {
                // بستن
                $subMenu.slideUp(300);
                if ($arrow.length) {
                    $arrow.removeClass('ti-minus').addClass('ti-angle-up');
                }
            } else {
                // باز کردن
                $subMenu.slideDown(300);
                if ($arrow.length) {
                    $arrow.removeClass('ti-angle-up').addClass('ti-minus');
                }
            }
        }
    });

    // ============================================
    // 4. دکمه تغییر حالت منو (Toggler)
    // ============================================
    $(document).on('click', '.navigation-toggler > a', function () {
        if ($body.hasClass('small-navigation')) {
            $body.removeClass('small-navigation');
            $navigation.getNiceScroll().remove();
            $navigation.niceScroll(niceScrollOptions);
            $body.find('.sub-menu-arrow').removeClass('ti-minus').addClass('ti-angle-up');
        } else if ($body.hasClass('semi-dark') || $window.width() < 992) {
            createOverlay();
            $body.addClass('no-scroll');
            $navigation.addClass('open').niceScroll(niceScrollOptions);
        } else {
            $body.addClass('small-navigation');
            $navigation.niceScroll(niceScrollOptions);
        }
        return false;
    });

    function createOverlay() {
        if ($('.overlay').length < 1) {
            $('body').append('<div class="overlay"></div>');
        }
    }

    function removeOverlay() {
        $('.overlay').remove();
        $body.removeClass('no-scroll');
        $navigation.removeClass('open');
        $navigation.getNiceScroll().remove();
    }

    $(document).on('click', '.overlay', function () {
        removeOverlay();
    });

    // ============================================
    // 5. تم و تنظیمات
    // ============================================
    $(document).on('change', '.theme-switcher input[type="checkbox"]', function () {
        var checkboxId = $(this).attr('id');
        var isChecked = $(this).prop('checked');

        switch (checkboxId) {
            case 'small-navigation':
                if (isChecked) {
                    $body.addClass('small-navigation');
                    $navigation.niceScroll(niceScrollOptions);
                } else {
                    $body.removeClass('small-navigation');
                    $navigation.getNiceScroll().remove();
                    $navigation.niceScroll(niceScrollOptions);
                }
                break;
            case 'semi-dark':
                if (isChecked) $body.addClass('semi-dark');
                else $body.removeClass('semi-dark');
                break;
            case 'dark':
                if (isChecked) $body.addClass('dark-layout');
                else $body.removeClass('dark-layout');
                break;
            case 'sticky-header':
                if (isChecked) $body.addClass('sticky-navigation');
                else $body.removeClass('sticky-navigation');
                break;
        }
    });

    // ============================================
    // 6. پیش‌بارگذاری
    // ============================================
    $window.on('load', function () {
        $('.preloader').fadeOut(500);
        if (typeof toastr !== 'undefined') {
            toastr.options = { "positionClass": "toast-top-right", "timeOut": "3000" };
            if (!$('body').hasClass('login-page')) {
                // toastr.info("به پنل مدیریت خوش آمدید.");
            }
        }
    });

    // ============================================
    // 7. ریسپانسیو
    // ============================================
    $window.on('resize', function () {
        if ($.fn.niceScroll) {
            $navigation.getNiceScroll().resize();
        }
        if ($window.width() >= 992) {
            removeOverlay();
        }
    });

})(jQuery);