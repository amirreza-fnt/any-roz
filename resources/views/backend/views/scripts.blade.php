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
    @if(session('message'))
        toastr.{{ session('message')['type'] }}('{{ session('message')['message'] }}');
        @php
            session()->forget('message');
        @endphp
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