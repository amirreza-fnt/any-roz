@extends('backend.views.view')
@section('main')
<main class="main-content">
        <!-- begin::chat block -->
        <div class="row no-gutters chat-block">
            <!-- begin::chat sidebar -->
            <div class="col-lg-4 chat-sidebar border-right">
                <!-- begin::chat sidebar search -->
                <div class="chat-sidebar-header">
                    <div class="d-flex">
                        <div class="pr-3">
                            <div class="avatar avatar-sm">
                                <img src="{{ asset('assets/back-end/assets/media/svg/mean_at_work.svg') }}" class="rounded-circle" alt="image">
                            </div>
                        </div>
                        <div>
                            <div class="m-0 small text-muted">مدیر</div>
                        </div>
                    </div>
                    <form>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="جستجوی گفتگو" aria-describedby="button-addon1">
                            <div class="input-group-append">
                                <button class="btn btn-outline-light" type="button" id="button-addon1">
                                    <i class="ti-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- begin::chat sidebar search -->
                <!-- end::chat list -->
                <div class="chat-sidebar-content">
                    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">گفتگو ها</a>
                        </li>
                    </ul>
                    <div class="tab-content pt-3" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <p class="small mb-0">گفتگو های اخیر</p>
                            <div class="chat-lists">
                                <div class="list-group list-group-flush">
                                    <a href="#" class="list-group-item d-flex align-items-center link-1 pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-danger">
                                                <span class="avatar-title bg-success rounded-circle">م</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">محمد رضایی</h6>
                                            <span class="small text-muted">سلام چطوری؟</span>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <span class="badge badge-primary badge-pill ml-auto">1</span>
                                            <span class="small text-muted">2:32 ب.ظ</span>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item d-flex align-items-center link-1 pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-success">
                                                <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar1.jpg') }}" class="rounded-circle" alt="image">
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">سارا احمدی</h6>
                                            <span class="small text-muted">سلام!</span>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <span class="badge badge-primary badge-pill ml-auto">3</span>
                                            <span class="small text-muted">08:27 ب.ظ</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item d-flex pl-0 pr-0 pb-3 pt-3">
                                    <div class="pr-3">
                                        <div class="avatar avatar-sm avatar-state-warning">
                                            <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle" alt="image">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">مریم کاظمی</h6>
                                        <div class="small text-muted d-flex align-items-center">
                                            <i class="width-15 height-15 mr-1 text-danger" data-feather="arrow-down-left"></i>
                                            امروز، 03:11 ق.ظ
                                        </div>
                                    </div>
                                    <div class="text-right ml-auto">
                                        <i class="text-danger width-18 height-18" data-feather="video"></i>
                                    </div>
                                </a>
                                <a href="#" class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                    <div class="pr-3">
                                        <div class="avatar avatar-sm avatar-state-success">
                                            <img src="{{ asset('assets/back-end/assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle" alt="image">
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">علی جعفری</h6>
                                        <div class="small text-muted d-flex align-items-center">
                                            <i class="width-15 height-15 mr-1 text-success" data-feather="arrow-up-right"></i>
                                            امروز، 03:11 ق.ظ
                                        </div>
                                    </div>
                                    <div class="text-right ml-auto">
                                        <i class="width-18 height-18 text-success" data-feather="phone-call"></i>
                                    </div>
                                </a>
                                <a href="#" class="list-group-item d-flex pl-0 pr-0 pb-3 pt-3">
                                    <div class="pr-3">
                                        <div class="avatar avatar-sm avatar-state-warning">
                                            <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar1.jpg') }}" class="rounded-circle" alt="image">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">زهرا محمدی</h6>
                                        <div class="small text-muted d-flex align-items-center">
                                            <i class="width-15 height-15 mr-1 text-success" data-feather="arrow-up-right"></i>
                                            امروز، 03:11 ق.ظ
                                        </div>
                                    </div>
                                    <div class="text-right ml-auto">
                                        <i class="width-18 height-18 text-success" data-feather="video"></i>
                                    </div>
                                </a>
                                <a href="#" class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                    <div class="pr-3">
                                        <div class="avatar avatar-sm avatar-state-secondary">
                                            <span class="avatar-title bg-info rounded-circle">ح</span>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">حسین نوری</h6>
                                        <div class="small text-muted d-flex align-items-center">
                                            <i class="width-15 height-15 mr-1 text-danger" data-feather="arrow-down-left"></i>
                                            امروز، 03:11 ق.ظ
                                        </div>
                                    </div>
                                    <div class="text-right ml-auto">
                                        <i class="width-18 height-18 text-danger" data-feather="video"></i>
                                    </div>
                                </a>
                                <a href="#" class="list-group-item d-flex pl-0 pr-0 pb-3 pt-3">
                                    <div class="pr-3">
                                        <div class="avatar avatar-sm avatar-state-info">
                                            <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle" alt="image">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">نرگس شریفی</h6>
                                        <div class="small text-muted d-flex align-items-center">
                                            <i class="width-15 height-15 mr-1 text-success" data-feather="arrow-up-right"></i>
                                            امروز، 03:11 ق.ظ
                                        </div>
                                    </div>
                                    <div class="text-right ml-auto">
                                        <i class="width-18 height-18 text-success" data-feather="phone-call"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <p class="small mb-0">142 مخاطب</p>
                            <div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-danger">
                                                <span class="avatar-title bg-success rounded-circle">آ</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">آرش کریمی</h6>
                                            <div class="small text-muted mt-1 line-height-20">تهران</div>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <a href="#" class="p-1">
                                                <i data-feather="phone-call" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="message-circle" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="video" class="width-18 height-18"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-success">
                                                <img src="{{ asset('assets/back-end/assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle" alt="image">
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">امید صادقی</h6>
                                            <div class="small text-muted mt-1 line-height-20">اصفهان</div>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <a href="#" class="p-1">
                                                <i data-feather="phone-call" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="message-circle" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="video" class="width-18 height-18"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-warning">
                                                <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar1.jpg') }}" class="rounded-circle" alt="image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">الهام رضوی</h6>
                                            <div class="small text-muted mt-1 line-height-20">شیراز</div>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <a href="#" class="p-1">
                                                <i data-feather="phone-call" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="message-circle" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="video" class="width-18 height-18"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-info">
                                                <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle" alt="image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">رضا کاظمی</h6>
                                            <div class="small text-muted mt-1 line-height-20">مشهد</div>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <a href="#" class="p-1">
                                                <i data-feather="phone-call" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="message-circle" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="video" class="width-18 height-18"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-secondary">
                                                <span class="avatar-title bg-success rounded-circle">ن</span>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">نیلوفر حسینی</h6>
                                            <div class="small text-muted mt-1 line-height-20">تبریز</div>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <a href="#" class="p-1">
                                                <i data-feather="phone-call" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="message-circle" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="video" class="width-18 height-18"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-success">
                                                <img src="{{ asset('assets/back-end/assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle" alt="image">
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">پیمان عباسی</h6>
                                            <div class="small text-muted mt-1 line-height-20">رشت</div>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <a href="#" class="p-1">
                                                <i data-feather="phone-call" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="message-circle" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="video" class="width-18 height-18"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-danger">
                                                <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar1.jpg') }}" class="rounded-circle" alt="image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">شیما نوری</h6>
                                            <div class="small text-muted mt-1 line-height-20">کرج</div>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <a href="#" class="p-1">
                                                <i data-feather="phone-call" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="message-circle" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="video" class="width-18 height-18"></i>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center pl-0 pr-0 pb-3 pt-3">
                                        <div class="pr-3">
                                            <div class="avatar avatar-sm avatar-state-warning">
                                                <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle" alt="image">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">کیان امینی</h6>
                                            <div class="small text-muted mt-1 line-height-20">اهواز</div>
                                        </div>
                                        <div class="text-right ml-auto">
                                            <a href="#" class="p-1">
                                                <i data-feather="phone-call" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="message-circle" class="width-18 height-18"></i>
                                            </a>
                                            <a href="#" class="p-1">
                                                <i data-feather="video" class="width-18 height-18"></i>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-center mt-3">
                                <button class="btn btn-outline-light btn-sm">مخاطب های بیشتر</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end::chat list -->
            </div>
            <!-- end::chat sidebar -->
            <!-- begin::chat content -->
            <div class="col-lg-8 chat-content">
                <!-- begin::chat header -->
                <div class="chat-header border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="pr-3">
                            <div class="avatar avatar-sm avatar-state-warning">
                                <img src="{{ asset('assets/back-end/assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle" alt="image">
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1">مریم کاظمی</h6>
                        </div>
                    </div>
                </div>
                <!-- end::chat header -->
                <!-- begin::messages -->
                <div class="messages">
                    <div class="message-item">
                        <div class="message-item-content">سلام!</div>
                        <span class="time small text-muted font-italic">02:30 ب.ظ</span>
                    </div>
                    <div class="message-item me">
                        <div class="message-item-content">لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است و برای شرایط فعلی
                        </div>
                        <span class="time small text-muted font-italic">دیروز</span>
                    </div>
                    <div class="message-item">
                        <div class="message-item-content">سلام! امروز حالت چطوره؟</div>
                        <span class="time small text-muted font-italic">02:30 ب.ظ</span>
                    </div>
                    <div class="message-item me">
                        <div class="message-item-content">لورم ایپسوم متن ساختگی</div>
                        <span class="time small text-muted font-italic">02:30 ب.ظ</span>
                    </div>
                    <div class="message-item">
                        <div class="message-item-content d-flex">
                            <i class="ti-file mr-2 font-size-20 mt-2"></i>
                            <div>
                                <div>important_documents.pdf <i class="text-muted small">(50KB)</i></div>
                                <ul class="list-inline small">
                                    <li class="list-inline-item"><a href="#">دریافت</a></li>
                                    <li class="list-inline-item"><a href="#">مشاهده</a></li>
                                </ul>
                            </div>
                        </div>
                        <span class="time small text-muted font-italic">02:30 ب.ظ</span>
                    </div>
                    <div class="message-item me">
                        <div class="message-item-content">لورم ایپسوم متن ساختگی</div>
                        <span class="time small text-muted font-italic">02:30 ب.ظ</span>
                    </div>
                    <div class="message-item">
                        <div class="message-item-content">لورم ایپسوم متن ساختگی</div>
                        <span class="time small text-muted font-italic">02:30 ب.ظ</span>
                    </div>
                    <div class="message-item message-item-divider">
                        <span>امروز</span>
                    </div>
                    <div class="message-item">
                        <div class="message-item-content">لورم ایپسوم متن ساختگی</div>
                        <span class="time small text-muted font-italic">02:30 ب.ظ</span>
                    </div>
                </div>
                <!-- end::messages -->
                <!-- begin::chat footer -->
                <div class="chat-footer border-top">
                    <form class="d-flex">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control" placeholder="پیام خود را بنویسید" id="messageInput">
                        </div>
                        <div class="chat-footer-buttons d-flex">
                            <button class="btn btn-primary" type="submit">
                                <i data-feather="send" class="width-15 height-15"></i>
                            </button>
                            <button class="btn btn-outline-light" type="button" title="الحاق فایل" data-toggle="modal" data-target="#fileSelectModal">
                                <!-- SVG Box Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                <!-- end::chat footer -->
            </div>
            <!-- begin::chat content -->
        </div>
        <!-- begin::chat block -->
</main>

<!-- Modal for File Selection -->
<div class="modal fade" id="fileSelectModal" tabindex="-1" role="dialog" aria-labelledby="fileSelectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileSelectModalLabel">انتخاب فایل</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
                <div class="list-group">
                    <label class="list-group-item d-flex align-items-center">
                        <input class="form-check-input mr-2" type="checkbox" value="doc1.pdf" name="fileSelect">
                        <span>زرد چوبه</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
                <button type="button" class="btn btn-primary" id="confirmFileBtn">ثبت و افزودن</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#confirmFileBtn').click(function() {
            var selectedFiles = [];
            $('input[name="fileSelect"]:checked').each(function() {
                selectedFiles.push($(this).val());
            });

            if (selectedFiles.length > 0) {
                var messageInput = $('#messageInput');
                var currentText = messageInput.val();
                var fileText = selectedFiles.join(' + ');
                
                if (currentText.length > 0) {
                    messageInput.val(currentText + ' [File: ' + fileText + ']');
                } else {
                    messageInput.val('[File: ' + fileText + ']');
                }
                
                // Close modal
                $('#fileSelectModal').modal('hide');
            } else {
                alert('لطفا حداقل یک فایل را انتخاب کنید.');
            }
        });
    });
</script>
@endsection