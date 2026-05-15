@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h4 class="mb-1" style="font-weight:700; color:#1e293b;">
                    <i data-feather="git-branch" style="width:22px;height:22px;vertical-align:middle;margin-left:6px;"></i>
                    فلوچارت سازمانی سود
                </h4>
                <p class="text-muted mb-0" style="font-size:0.85rem;">ساختار سازمانی و درصد سود هر فرد از فروش‌ها را طراحی و مدیریت کنید.</p>
            </div>
            @admincan('accounting.flowchart.manage')
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" onclick="openAddRootModal()" style="border-radius:8px; padding:8px 18px;">
                <i data-feather="plus-circle" style="width:16px;height:16px;"></i>
                افزودن نود ریشه
            </button>
            @endadmincan
        </div>

        {{-- Tree Canvas --}}
        <div class="card" style="border:none; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,0.06); overflow:hidden;">
            <div class="card-body p-0" style="min-height:500px; position:relative;">
                <div id="fc-toolbar" style="padding:12px 20px; background:linear-gradient(135deg,#f8fafc,#eef2ff); border-bottom:1px solid #e2e8f0; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <button class="fc-tool-btn" onclick="zoomIn()" title="بزرگنمایی"><i data-feather="zoom-in" style="width:16px;height:16px;"></i></button>
                    <button class="fc-tool-btn" onclick="zoomOut()" title="کوچکنمایی"><i data-feather="zoom-out" style="width:16px;height:16px;"></i></button>
                    <button class="fc-tool-btn" onclick="resetZoom()" title="بازنشانی"><i data-feather="maximize" style="width:16px;height:16px;"></i></button>
                    <span style="width:1px;height:24px;background:#cbd5e1;"></span>
                    <button class="fc-tool-btn" onclick="collapseAll()" title="بستن همه"><i data-feather="minus-square" style="width:16px;height:16px;"></i></button>
                    <button class="fc-tool-btn" onclick="expandAll()" title="باز کردن همه"><i data-feather="plus-square" style="width:16px;height:16px;"></i></button>
                    <span style="width:1px;height:24px;background:#cbd5e1;"></span>
                    <button class="fc-tool-btn" onclick="refreshTree()" title="بارگذاری مجدد"><i data-feather="refresh-cw" style="width:16px;height:16px;"></i></button>
                </div>

                <div id="fc-canvas-wrapper" style="overflow:auto; padding:40px 20px; min-height:440px; background: radial-gradient(circle at 20px 20px, #f1f5f9 1px, transparent 1px); background-size:40px 40px;">
                    <div id="fc-canvas" style="transform-origin:top center; transition:transform 0.3s ease; direction:ltr;">
                        <div id="fc-tree-root"></div>
                    </div>
                </div>

                <div id="fc-empty-state" style="display:none; text-align:center; padding:80px 20px;">
                    <div style="width:80px;height:80px;margin:0 auto 16px;border-radius:50%;background:linear-gradient(135deg,#e0e7ff,#c7d2fe);display:flex;align-items:center;justify-content:center;">
                        <i data-feather="git-branch" style="width:36px;height:36px;color:#6366f1;"></i>
                    </div>
                    <h5 style="color:#475569;font-weight:600;">هنوز فلوچارتی ساخته نشده</h5>
                    <p style="color:#94a3b8;font-size:0.9rem;">با کلیک روی «افزودن نود ریشه» ساختار سازمانی خود را بسازید.</p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal: Add / Edit Node --}}
<div class="modal fade" id="nodeModal" tabindex="-1" dir="rtl">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content" style="border:none; border-radius:16px; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border:none;padding:16px 24px;">
                <h5 class="modal-title" id="nodeModalTitle" style="font-weight:700;font-size:1rem;">
                    <i data-feather="edit-3" style="width:18px;height:18px;vertical-align:middle;margin-left:6px;"></i>
                    <span id="nodeModalTitleText">افزودن نود</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <input type="hidden" id="modal-node-id" value="">
                <input type="hidden" id="modal-parent-id" value="">

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">عنوان / سمت <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="modal-title" placeholder="مثلاً: مدیر عامل" style="border-radius:10px; padding:10px 14px;">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">منبع سود</label>
                        <select class="form-select" id="modal-profit-source" style="border-radius:10px; padding:10px 14px;">
                            <option value="none">بدون سهم سود</option>
                            <option value="all_sales">از کل فروش‌ها</option>
                            <option value="own_sales">فقط از فروش خود</option>
                            <option value="marketing_sales">از فروش بازاریابی</option>
                            <option value="site_sales">از فروش سایت</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold" style="font-size:0.85rem;">درصد سود (%)</label>
                        <input type="number" class="form-control" id="modal-profit-pct" min="0" max="100" step="0.01" value="0" style="border-radius:10px; padding:10px 14px;" placeholder="مثلاً 5">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">اتصال به ادمین (اختیاری)</label>
                    <select class="form-select" id="modal-admin-id" style="border-radius:10px; padding:10px 14px;">
                        <option value="">— بدون اتصال —</option>
                        @foreach ($admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->full_name }} ({{ $admin->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size:0.85rem;">رنگ نود</label>
                    <div class="d-flex gap-2 flex-wrap" id="color-picker">
                        @foreach (['#6366f1','#3b82f6','#06b6d4','#10b981','#f59e0b','#ef4444','#ec4899','#8b5cf6','#64748b'] as $c)
                        <div class="fc-color-opt{{ $loop->first ? ' active' : '' }}" data-color="{{ $c }}" style="width:30px;height:30px;border-radius:50%;background:{{ $c }};cursor:pointer;border:3px solid transparent;transition:all 0.2s;" onclick="pickColor(this,'{{ $c }}')"></div>
                        @endforeach
                    </div>
                    <input type="hidden" id="modal-color" value="#6366f1">
                </div>
            </div>
            <div class="modal-footer" style="border:none;padding:0 24px 20px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px;padding:8px 20px;">انصراف</button>
                <button type="button" class="btn btn-primary" onclick="saveNode()" id="modal-save-btn" style="border-radius:10px;padding:8px 24px;">
                    <i data-feather="save" style="width:15px;height:15px;vertical-align:middle;margin-left:4px;"></i>
                    ذخیره
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Confirm Delete --}}
<div class="modal fade" id="deleteModal" tabindex="-1" dir="rtl">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:none;border-radius:16px;">
            <div class="modal-body text-center" style="padding:32px 24px;">
                <div style="width:56px;height:56px;border-radius:50%;background:#fef2f2;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
                    <i data-feather="trash-2" style="width:24px;height:24px;color:#ef4444;"></i>
                </div>
                <h6 style="font-weight:700;color:#1e293b;margin-bottom:8px;">حذف نود</h6>
                <p style="color:#64748b;font-size:0.85rem;margin-bottom:0;">آیا از حذف <strong id="delete-node-title"></strong> و تمام زیرمجموعه‌هایش مطمئنید؟</p>
            </div>
            <div class="modal-footer justify-content-center" style="border:none;padding:0 24px 24px;gap:8px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:10px;padding:8px 24px;">انصراف</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-btn" style="border-radius:10px;padding:8px 24px;">
                    <i data-feather="trash-2" style="width:14px;height:14px;vertical-align:middle;margin-left:4px;"></i>
                    حذف
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .fc-tool-btn {
        width: 36px; height: 36px; border: 1px solid #e2e8f0; border-radius: 8px;
        background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: #475569; transition: all 0.2s;
    }
    .fc-tool-btn:hover { background: #6366f1; color: #fff; border-color: #6366f1; }

    .fc-color-opt.active { border-color: #1e293b !important; transform: scale(1.15); }

    /* Tree structure */
    .fc-tree { list-style: none; padding: 0; margin: 0; }
    .fc-tree ul { list-style: none; padding-right: 0; margin: 0; position: relative; }

    .fc-tree-wrap { display: flex; flex-direction: column; align-items: center; }
    .fc-tree-children { display: flex; flex-wrap: nowrap; gap: 24px; justify-content: center; position: relative; padding-top: 28px; }
    .fc-tree-children::before {
        content: ''; position: absolute; top: 0; left: 50%; width: 0; height: 28px;
        border-left: 2px dashed #cbd5e1;
    }
    .fc-tree-children > .fc-tree-wrap { position: relative; }
    .fc-tree-children > .fc-tree-wrap::before {
        content: ''; position: absolute; top: -28px; left: 50%; width: 0; height: 28px;
        border-left: 2px dashed #cbd5e1;
    }

    .fc-tree-children.fc-multi > .fc-tree-wrap:first-child::after,
    .fc-tree-children.fc-multi > .fc-tree-wrap:last-child::after {
        content: ''; position: absolute; top: 0;
        height: 0; border-top: 2px dashed #cbd5e1;
    }
    .fc-tree-children.fc-multi::before { display: none; }
    .fc-tree-children.fc-multi { position: relative; }
    .fc-tree-children.fc-multi::after {
        content: ''; position: absolute; top: 0;
        left: 0; right: 0; height: 0;
    }

    .fc-node {
        min-width: 180px; max-width: 240px; border-radius: 14px;
        background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        padding: 14px 16px; text-align: center; position: relative;
        cursor: default; transition: all 0.25s; border: 2px solid transparent;
    }
    .fc-node:hover { box-shadow: 0 6px 24px rgba(99,102,241,0.18); transform: translateY(-2px); }

    .fc-node-color-bar {
        position: absolute; top: 0; left: 50%; transform: translateX(-50%);
        width: 50%; height: 4px; border-radius: 0 0 4px 4px;
    }

    .fc-node-title { font-weight: 700; font-size: 0.9rem; color: #1e293b; margin-bottom: 6px; direction: rtl; }
    .fc-node-meta { font-size: 0.75rem; color: #64748b; line-height: 1.6; direction: rtl; }
    .fc-node-badge {
        display: inline-block; padding: 2px 8px; border-radius: 20px;
        font-size: 0.7rem; font-weight: 600; margin-top: 4px;
    }

    .fc-node-actions {
        position: absolute; top: 8px; left: 8px; display: flex; gap: 3px; opacity: 0; transition: opacity 0.2s;
    }
    .fc-node:hover .fc-node-actions { opacity: 1; }

    .fc-node-act {
        width: 26px; height: 26px; border-radius: 6px; border: none;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; font-size: 12px; transition: all 0.15s;
    }
    .fc-act-add { background: #ecfdf5; color: #10b981; }
    .fc-act-add:hover { background: #10b981; color: #fff; }
    .fc-act-edit { background: #eff6ff; color: #3b82f6; }
    .fc-act-edit:hover { background: #3b82f6; color: #fff; }
    .fc-act-del { background: #fef2f2; color: #ef4444; }
    .fc-act-del:hover { background: #ef4444; color: #fff; }

    .fc-connector { stroke: #cbd5e1; stroke-width: 2; stroke-dasharray: 6 4; fill: none; }

    .fc-tree-vertical-line {
        width: 2px; height: 28px; border-left: 2px dashed #cbd5e1; margin: 0 auto;
    }

    .fc-tree-horizontal-bridge {
        position: absolute; top: 0; border-top: 2px dashed #cbd5e1;
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const CSRF = '{{ csrf_token() }}';
    const URL_TREE  = '{{ route("admin.accounting.flowchart.tree") }}';
    const URL_STORE = '{{ route("admin.accounting.flowchart.store") }}';
    const URL_BASE  = '{{ url("admin/accounting/flowchart") }}';
    const CAN_MANAGE = {{ auth('admin')->user()->hasPermission('accounting.flowchart.manage') || auth('admin')->user()->is_super ? 'true' : 'false' }};

    let treeData = @json($tree);
    let currentZoom = 1;
    let collapsedNodes = new Set();

    window.refreshTree = function() {
        fetch(URL_TREE, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
            .then(r => r.json())
            .then(d => { treeData = d.tree; renderTree(); })
            .catch(() => toastr.error('خطا در بارگذاری فلوچارت'));
    };

    function renderTree() {
        const root = document.getElementById('fc-tree-root');
        const empty = document.getElementById('fc-empty-state');
        if (!treeData || treeData.length === 0) {
            root.innerHTML = '';
            root.style.display = 'none';
            empty.style.display = 'block';
            return;
        }
        empty.style.display = 'none';
        root.style.display = 'block';

        root.innerHTML = '<div class="fc-tree-children' + (treeData.length > 1 ? ' fc-multi' : '') + '">' +
            treeData.map(n => renderNode(n)).join('') + '</div>';

        if (typeof feather !== 'undefined') feather.replace();
    }

    function renderNode(node) {
        const kids = node.children || [];
        const hasKids = kids.length > 0;
        const isCollapsed = collapsedNodes.has(node.id);
        const color = node.color || '#6366f1';

        const profitBadge = node.profit_source !== 'none'
            ? '<div class="fc-node-badge" style="background:' + color + '18;color:' + color + ';">' +
              node.profit_percentage + '% — ' + node.profit_source_label + '</div>'
            : '<div class="fc-node-badge" style="background:#f1f5f9;color:#94a3b8;">بدون سهم سود</div>';

        const adminLine = node.admin_name
            ? '<div style="margin-top:3px;"><i data-feather="user" style="width:11px;height:11px;vertical-align:middle;"></i> ' + escHtml(node.admin_name) + '</div>'
            : '';

        const actionsHtml = CAN_MANAGE ? `
            <div class="fc-node-actions">
                <button class="fc-node-act fc-act-add" onclick="openAddChildModal(${node.id})" title="افزودن زیرمجموعه"><i data-feather="plus" style="width:13px;height:13px;"></i></button>
                <button class="fc-node-act fc-act-edit" onclick="openEditModal(${node.id})" title="ویرایش"><i data-feather="edit-2" style="width:13px;height:13px;"></i></button>
                <button class="fc-node-act fc-act-del" onclick="openDeleteModal(${node.id},'${escAttr(node.title)}')" title="حذف"><i data-feather="x" style="width:13px;height:13px;"></i></button>
            </div>
        ` : '';

        const toggleBtn = hasKids ? `
            <div style="position:absolute;bottom:-14px;left:50%;transform:translateX(-50%);z-index:2;">
                <button onclick="toggleCollapse(${node.id})" style="width:28px;height:28px;border-radius:50%;border:2px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;color:#64748b;transition:all 0.2s;" title="${isCollapsed ? 'باز کردن' : 'بستن'}">
                    <i data-feather="${isCollapsed ? 'chevron-down' : 'chevron-up'}" style="width:14px;height:14px;"></i>
                </button>
            </div>
        ` : '';

        let childHtml = '';
        if (hasKids && !isCollapsed) {
            childHtml = '<div class="fc-tree-vertical-line"></div>' +
                '<div class="fc-tree-children' + (kids.length > 1 ? ' fc-multi' : '') + '">' +
                kids.map(k => renderNode(k)).join('') + '</div>';
        }

        return `<div class="fc-tree-wrap" data-node-id="${node.id}">
            <div class="fc-node" data-id="${node.id}" style="border-color:${color}30;">
                <div class="fc-node-color-bar" style="background:${color};"></div>
                ${actionsHtml}
                <div class="fc-node-title">${escHtml(node.title)}</div>
                <div class="fc-node-meta">
                    ${profitBadge}
                    ${adminLine}
                </div>
                ${toggleBtn}
            </div>
            ${childHtml}
        </div>`;
    }

    function escHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function escAttr(s) { return s.replace(/'/g, "\\'").replace(/"/g, '\\"'); }

    window.toggleCollapse = function(id) {
        if (collapsedNodes.has(id)) collapsedNodes.delete(id);
        else collapsedNodes.add(id);
        renderTree();
    };
    window.collapseAll = function() {
        forEachNode(treeData, n => { if (n.children && n.children.length) collapsedNodes.add(n.id); });
        renderTree();
    };
    window.expandAll = function() { collapsedNodes.clear(); renderTree(); };

    function forEachNode(nodes, fn) {
        (nodes || []).forEach(n => { fn(n); forEachNode(n.children, fn); });
    }
    function findNode(nodes, id) {
        for (const n of (nodes || [])) {
            if (n.id === id) return n;
            const f = findNode(n.children, id);
            if (f) return f;
        }
        return null;
    }

    // Zoom
    window.zoomIn = function() { currentZoom = Math.min(currentZoom + 0.1, 2); applyZoom(); };
    window.zoomOut = function() { currentZoom = Math.max(currentZoom - 0.1, 0.3); applyZoom(); };
    window.resetZoom = function() { currentZoom = 1; applyZoom(); };
    function applyZoom() { document.getElementById('fc-canvas').style.transform = 'scale(' + currentZoom + ')'; }

    // Modal helpers
    window.openAddRootModal = function() {
        document.getElementById('modal-node-id').value = '';
        document.getElementById('modal-parent-id').value = '';
        document.getElementById('modal-title').value = '';
        document.getElementById('modal-profit-source').value = 'none';
        document.getElementById('modal-profit-pct').value = '0';
        document.getElementById('modal-admin-id').value = '';
        document.getElementById('modal-color').value = '#6366f1';
        document.getElementById('nodeModalTitleText').textContent = 'افزودن نود ریشه';
        resetColorPicker('#6366f1');
        new bootstrap.Modal(document.getElementById('nodeModal')).show();
    };

    window.openAddChildModal = function(parentId) {
        document.getElementById('modal-node-id').value = '';
        document.getElementById('modal-parent-id').value = parentId;
        document.getElementById('modal-title').value = '';
        document.getElementById('modal-profit-source').value = 'none';
        document.getElementById('modal-profit-pct').value = '0';
        document.getElementById('modal-admin-id').value = '';
        document.getElementById('modal-color').value = '#3b82f6';
        document.getElementById('nodeModalTitleText').textContent = 'افزودن زیرمجموعه';
        resetColorPicker('#3b82f6');
        new bootstrap.Modal(document.getElementById('nodeModal')).show();
    };

    window.openEditModal = function(nodeId) {
        const node = findNode(treeData, nodeId);
        if (!node) return;
        document.getElementById('modal-node-id').value = node.id;
        document.getElementById('modal-parent-id').value = node.parent_id || '';
        document.getElementById('modal-title').value = node.title;
        document.getElementById('modal-profit-source').value = node.profit_source;
        document.getElementById('modal-profit-pct').value = node.profit_percentage;
        document.getElementById('modal-admin-id').value = node.admin_id || '';
        document.getElementById('modal-color').value = node.color || '#6366f1';
        document.getElementById('nodeModalTitleText').textContent = 'ویرایش: ' + node.title;
        resetColorPicker(node.color || '#6366f1');
        new bootstrap.Modal(document.getElementById('nodeModal')).show();
    };

    let deleteTargetId = null;
    window.openDeleteModal = function(id, title) {
        deleteTargetId = id;
        document.getElementById('delete-node-title').textContent = title;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    };

    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (!deleteTargetId) return;
        fetch(URL_BASE + '/' + deleteTargetId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                treeData = d.tree;
                renderTree();
                toastr.success('نود با موفقیت حذف شد.');
            } else {
                toastr.error(d.message || 'خطا');
            }
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        })
        .catch(() => toastr.error('خطا در حذف'));
    });

    window.saveNode = function() {
        const nodeId = document.getElementById('modal-node-id').value;
        const isEdit = !!nodeId;

        const payload = {
            title: document.getElementById('modal-title').value,
            profit_source: document.getElementById('modal-profit-source').value,
            profit_percentage: parseFloat(document.getElementById('modal-profit-pct').value) || 0,
            admin_id: document.getElementById('modal-admin-id').value || null,
            color: document.getElementById('modal-color').value,
        };
        if (!isEdit) {
            payload.parent_id = document.getElementById('modal-parent-id').value || null;
        }

        const url = isEdit ? (URL_BASE + '/' + nodeId) : URL_STORE;
        const method = isEdit ? 'PUT' : 'POST';

        document.getElementById('modal-save-btn').disabled = true;

        fetch(url, {
            method: method,
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(d => {
            document.getElementById('modal-save-btn').disabled = false;
            if (d.success) {
                treeData = d.tree;
                renderTree();
                toastr.success(isEdit ? 'نود ویرایش شد.' : 'نود افزوده شد.');
                bootstrap.Modal.getInstance(document.getElementById('nodeModal')).hide();
            } else if (d.errors) {
                const firstErr = Object.values(d.errors)[0];
                toastr.error(Array.isArray(firstErr) ? firstErr[0] : firstErr);
            } else {
                toastr.error(d.message || 'خطایی رخ داد.');
            }
        })
        .catch(() => {
            document.getElementById('modal-save-btn').disabled = false;
            toastr.error('خطا در ارتباط با سرور');
        });
    };

    window.pickColor = function(el, color) {
        document.querySelectorAll('.fc-color-opt').forEach(e => e.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('modal-color').value = color;
    };

    function resetColorPicker(color) {
        document.querySelectorAll('.fc-color-opt').forEach(e => {
            e.classList.toggle('active', e.dataset.color === color);
        });
    }

    // initial render
    document.addEventListener('DOMContentLoaded', function() {
        renderTree();
    });
})();
</script>
@endpush
