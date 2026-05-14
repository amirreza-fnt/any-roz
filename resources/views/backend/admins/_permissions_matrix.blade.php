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
                <strong class="text-dark">{{ $group['label'] }}</strong>
                <span class="badge badge-light text-muted">{{ $group['id'] ?? '' }}</span>
            </div>
            <div class="card-body pt-0">
                <div class="row">
                    @foreach($group['items'] ?? [] as $item)
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="permissions[]" value="{{ $item['key'] }}" id="perm-{{ \Illuminate\Support\Str::slug($item['key']) }}"
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
