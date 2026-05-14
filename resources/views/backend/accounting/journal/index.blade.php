@extends('backend.views.view')

@section('main')
<div class="main-content">
    <div class="container-fluid">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h4 class="mb-1">دفتر اسناد حسابداری مجزا</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.accounting') }}">حسابداری</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.accounting.professional.index') }}">برنامهٔ جامع</a></li>
                        <li class="breadcrumb-item active">دفتر مجزا</li>
                    </ol>
                </nav>
            </div>
            @admincan('accounting.journal.create')
                <a href="{{ route('admin.accounting.journal.create') }}" class="btn btn-primary rounded-pill">ثبت سند جدید</a>
            @endadmincan
        </div>

        <div class="alert alert-info border-0 shadow-sm mb-3">
            این بخش کاملاً مستقل از فاکتورهای سایت است؛ برای ثبت هزینه‌های جانبی، درآمدهای خارج از فروش آنلاین، تعدیل‌ها و یادداشت‌های مالی استفاده کنید. اعداد گزارش «جامع» همچنان از فاکتورها محاسبه می‌شوند و این اسناد در آینده می‌توانند به گزارش‌های ترکیبی متصل شوند.
        </div>

        <div class="row mb-3">
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="small text-muted font-weight-bold">جمع درآمد / واریز (ثبت‌شده)</div>
                    <div class="h4 mb-0 text-success">{{ number_format($sumIncome) }} <small class="text-muted">تومان</small></div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="small text-muted font-weight-bold">جمع هزینه / برداشت</div>
                    <div class="h4 mb-0 text-danger">{{ number_format($sumExpense) }} <small class="text-muted">تومان</small></div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card border-0 shadow-sm p-3 h-100">
                    <div class="small text-muted font-weight-bold">جمع تعدیل</div>
                    <div class="h4 mb-0 text-secondary">{{ number_format($sumAdjustment) }} <small class="text-muted">تومان</small></div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>تاریخ سند (شمسی)</th>
                            <th>عنوان</th>
                            <th>نوع</th>
                            <th class="text-left" dir="ltr">مبلغ</th>
                            <th>شماره سند</th>
                            <th>ثبت‌کننده</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $e)
                            <tr>
                                <td>{{ $e->id }}</td>
                                <td class="text-monospace" dir="ltr">{{ \App\Support\JalaliCalendar::formatShamsiDate($e->document_date) }}</td>
                                <td>{{ $e->title }}</td>
                                <td><span class="badge badge-light border">{{ \App\Models\AccountingJournalEntry::kindLabel($e->kind) }}</span></td>
                                <td class="text-left font-weight-bold" dir="ltr">{{ number_format((float) $e->amount) }}</td>
                                <td class="text-monospace small" dir="ltr">{{ $e->document_no ?? '—' }}</td>
                                <td class="small">{{ $e->admin ? trim($e->admin->first_name.' '.$e->admin->last_name) : '—' }}</td>
                                <td class="text-nowrap">
                                    @admincan('accounting.journal.edit')
                                        <a href="{{ route('admin.accounting.journal.edit', $e) }}" class="btn btn-sm btn-outline-primary">ویرایش</a>
                                    @endadmincan
                                    @admincan('accounting.journal.delete')
                                        <form action="{{ route('admin.accounting.journal.destroy', $e) }}" method="post" class="d-inline" onsubmit="return confirm('حذف این سند؟');">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                        </form>
                                    @endadmincan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">هنوز سندی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($entries->hasPages())
                <div class="card-footer bg-white">{{ $entries->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
