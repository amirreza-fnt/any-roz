@php
    $selected = old('permissions', $admin->permissions ?? []);
    if (! is_array($selected)) {
        $selected = [];
    }
@endphp

<div class="permissions-matrix">
    @foreach($groups as $group)
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <strong class="text-dark">{{ $group['label'] }}</strong>

                    <!-- دکمه انتخاب همه -->
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary toggle-all-btn ms-2"
                            data-target="group-items-{{ $group['id'] ?? 'default' }}"
                            data-parent-id="{{ $group['id'] ?? 'default' }}"
                            title="فعال/غیرفعال کردن تمام موارد این بخش">
                        <i data-feather="check-square" class="width-14 height-14 me-1"></i>
                        <span class="d-none d-sm-inline">انتخاب همه</span>
                    </button>
                </div>
                <span class="badge badge-light text-muted">{{ $group['id'] ?? '' }}</span>
            </div>
            <div class="card-body pt-0">
                <div id="group-items-{{ $group['id'] ?? 'default' }}" class="row">
                    @foreach($group['items'] ?? [] as $item)
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                       class="custom-control-input perm-checkbox"
                                       name="permissions[]"
                                       value="{{ $item['key'] }}"
                                       id="perm-{{ \Illuminate\Support\Str::slug($item['key']) }}"
                                       data-parent-id="{{ $group['id'] ?? 'default' }}"
                                    {{ in_array($item['key'], $selected, true) ? 'checked' : '' }}>
                                <label class="custom-control-label small" for="perm-{{ \Illuminate\Support\Str::slug($item['key']) }}">{{ $item['label'] }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    .toggle-all-btn {
        transition: all 0.2s ease-in-out;
        border-width: 1px;
        padding: 0.2rem 0.6rem;
    }

    /* حالت فعال (همه انتخاب شده‌اند) */
    .toggle-all-btn.active-state {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .toggle-all-btn.active-state:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    /* حالت غیرفعال */
    .toggle-all-btn:not(.active-state) {
        background-color: #fff;
        color: #6c757d;
    }

    .toggle-all-btn:not(.active-state):hover {
        background-color: #f8f9fa;
        color: #0d6efd;
        border-color: #0d6efd;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof feather !== 'undefined') feather.replace();

        // 1. منطق دکمه «انتخاب همه»
        document.querySelectorAll('.toggle-all-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const checkboxes = document.querySelectorAll(`#${targetId} .perm-checkbox`);

                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const newState = !allChecked;

                checkboxes.forEach(cb => {
                    cb.checked = newState;
                });

                updateButtonState(this, newState);
            });
        });

        // 2. منطق هماهنگی دکمه با تغییرات دستی چک‌باکس‌ها
        // هر زمان یک چک‌باکس تغییر کرد، بررسی می‌کنیم که آیا همه در گروهش تیک خورده‌اند؟
        document.querySelectorAll('.perm-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const parentId = this.getAttribute('data-parent-id');
                const targetId = `group-items-${parentId}`;
                const checkboxesInGroup = document.querySelectorAll(`#${targetId} .perm-checkbox`);
                const allChecked = Array.from(checkboxesInGroup).every(cb => cb.checked);

                // پیدا کردن دکمه مربوط به این گروه
                const btn = document.querySelector(`.toggle-all-btn[data-parent-id="${parentId}"]`);

                if (btn) {
                    updateButtonState(btn, allChecked);
                }
            });
        });

        // تابع کمکی برای تغییر ظاهر دکمه
        function updateButtonState(btn, isActive) {
            if (isActive) {
                btn.classList.add('active-state');
                btn.classList.remove('btn-outline-secondary');
                btn.title = 'لغو انتخاب همه';
                // تغییر آیکون به مربع تیک‌خورده (اختیاری، چون feather لود شده)
                const icon = btn.querySelector('i');
                if(icon) {
                    icon.setAttribute('data-feather', 'square'); // تغییر به آیکون مربع پر
                    if(typeof feather !== 'undefined') feather.replace();
                }
            } else {
                btn.classList.remove('active-state');
                btn.classList.add('btn-outline-secondary');
                btn.title = 'انتخاب همه';
                const icon = btn.querySelector('i');
                if(icon) {
                    icon.setAttribute('data-feather', 'check-square'); // بازگشت به آیکون مربع توخالی
                    if(typeof feather !== 'undefined') feather.replace();
                }
            }
        }
    });
</script>
