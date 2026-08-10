<script src="{{ asset('assets/back-end/vendors/bundle.js') }}"></script>

<!-- Chartjs -->
<script src="{{ asset('assets/back-end/vendors/charts/chartjs/chart.min.js') }}"></script>

<!-- Apex chart -->
<script src="{{ asset('assets/back-end/vendors/charts/apex/apexcharts.min.js') }}"></script>

<!-- Circle progress -->
<script src="{{ asset('assets/back-end/vendors/circle-progress/circle-progress.min.js') }}"></script>

<!-- Peity -->
<script src="{{ asset('assets/back-end/vendors/charts/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('assets/back-end/assets/js/examples/charts/peity.js') }}"></script>

<!-- Datepicker -->
<script src="{{ asset('assets/back-end/vendors/datepicker/daterangepicker.js') }}"></script>

<!-- Slick -->
<script src="{{  asset('assets/back-end/vendors/slick/slick.min.js') }}"></script>

<!-- Vamp -->
<script src="{{ asset('assets/back-end/vendors/vmap/jquery.vmap.min.js')  }}"></script>
<script src="{{ asset('assets/back-end/vendors/vmap/maps/jquery.vmap.usa.js') }}"></script>
<script src="{{ asset('assets/back-end/assets/js/examples/vmap.js') }}"></script>

<!-- Dashboard scripts -->
<script src="{{ asset('assets/back-end/assets/js/examples/dashboard.js') }}"></script>
<div class="colors"> <!-- To use theme colors with Javascript -->
    <div class="bg-primary"></div>
    <div class="bg-primary-bright"></div>
    <div class="bg-secondary"></div>
    <div class="bg-secondary-bright"></div>
    <div class="bg-info"></div>
    <div class="bg-info-bright"></div>
    <div class="bg-success"></div>
    <div class="bg-success-bright"></div>
    <div class="bg-danger"></div>
    <div class="bg-danger-bright"></div>
    <div class="bg-warning"></div>
    <div class="bg-warning-bright"></div>
</div>

<!-- App scripts -->
<script src="{{ asset('assets/back-end/assets/js/app.js')  }}"></script>

<script>
    @if (session('message'))
        document.addEventListener('DOMContentLoaded', function () {
            @php
                $m = session('message');
                $type = $m['type'] ?? 'info';
                if ($type === 'danger') {
                    $type = 'error';
                }
                if (! in_array($type, ['success', 'info', 'warning', 'error'], true)) {
                    $type = 'info';
                }
            @endphp
            toastr.{{ $type }}(@json($m['message'] ?? ''));
        });
    @endif
</script>

<script>
    // تابع بستن باکس با انیمیشن
    function closeAlert(button) {
        const alertBox = button.closest('.custom-alert');
        alertBox.classList.add('closing');

        // حذف کامل از DOM بعد از پایان انیمیشن
        setTimeout(() => {
            alertBox.remove();

            // اگر باکسی نماند، کانتینر را هم پاک کن (اختیاری)
            if (document.querySelectorAll('.custom-alert').length === 0) {
                document.getElementById('alert-container').innerHTML = '';
            }
        }, 300);
    }

    // بستن خودکار بعد از 5 ثانیه (اختیاری)
    document.addEventListener('DOMContentLoaded', () => {
        const alerts = document.querySelectorAll('.custom-alert');
        alerts.forEach((alert, index) => {
            setTimeout(() => {
                if (!alert.classList.contains('closing')) {
                    closeAlert(alert.querySelector('.alert-close'));
                }
            }, 15000 + (index * 200)); // تاخیر کم برای هر باکس
        });
    });
</script>


<script>
    (function () {
        const PRICE_INPUT_CLASS = 'price-input';

        function formatNumber(input) {
            // ذخیره موقعیت نشانگر
            const cursorPos = input.selectionStart;

            // حذف کاراکترهای غیر عددی
            let value = input.value.replace(/[^\d]/g, '');

            if (value === '') {
                input.value = '';
                return;
            }

            // فرمت کردن
            let number = parseInt(value, 10);
            let formatted = number.toLocaleString('en-US');

            // اعمال مقدار
            input.value = formatted;

            // بازگرداندن نشانگر به جای درست
            // اگر کاربر در انتهای فیلد تایپ می‌کند، نشانگر باید در انتها بماند
            // اگر کاربر در وسط تایپ می‌کند، باید همانجا بماند
            const newLength = formatted.length;
            let newCursorPos = cursorPos;

            // محاسبه تعداد کاماهای اضافه شده
            const originalCommas = (input.value.match(/,/g) || []).length;
            const newCommas = (formatted.match(/,/g) || []).length;
            const diff = newCommas - originalCommas;

            // اگر cursor در انتهای عدد بود (یعنی کاربر داشت اضافه می‌کرد)
            if (cursorPos === input.value.length - originalCommas) {
                newCursorPos = newLength;
            } else {
                newCursorPos = cursorPos + diff;
            }

            // محدود کردن
            newCursorPos = Math.min(newCursorPos, newLength);
            newCursorPos = Math.max(newCursorPos, 0);

            input.setSelectionRange(newCursorPos, newCursorPos);
        }

        function cleanPriceInputs(form) {
            const inputs = form.querySelectorAll('.' + PRICE_INPUT_CLASS);
            inputs.forEach(input => {
                input.value = input.value.replace(/,/g, '');
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.' + PRICE_INPUT_CLASS);

            inputs.forEach(input => {
                // استفاده از keyup به جای input
                input.addEventListener('keyup', function () {
                    formatNumber(this);
                });

                // همچنین رویداد paste (چسباندن) را هم مدیریت کنیم
                input.addEventListener('paste', function (e) {
                    setTimeout(() => {
                        formatNumber(this);
                    }, 10);
                });

                if (input.value) {
                    formatNumber(input);
                }
            });
        });

        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form.querySelector('.' + PRICE_INPUT_CLASS)) {
                cleanPriceInputs(form);
            }
        });
    })();
</script>
