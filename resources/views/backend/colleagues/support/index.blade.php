@extends('backend.views.view')

@section('main')
<main class="main-content">
    <div class="container-fluid">
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4>پشتیبانی همکاران</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                        <li class="breadcrumb-item active">پشتیبانی همکاران</li>
                    </ol>
                </nav>
            </div>
            <div class="small text-muted" id="supportOnlineTip">وضعیت: متصل</div>
        </div>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <style>
            .cs-chat { height: calc(100vh - 190px); min-height: 460px; display: flex; overflow: hidden; }
            .cs-chat .chat-sidebar { display: flex; flex-direction: column; border-left: 1px solid #ebedf3; min-width: 0; }
            .cs-chat .chat-sidebar-header { padding: 12px; border-bottom: 1px solid #ebedf3; }
            .cs-chat .chat-sidebar-content { flex: 1; overflow-y: auto; }
            .cs-chat .conversation-item { cursor: pointer; }
            .cs-chat .conversation-item.active { background: #eef4ff; }
            .cs-chat .conversation-item .last-body { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 210px; }
            .cs-chat .chat-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
            .cs-chat .messages { flex: 1; overflow-y: auto; background: #f5f8fb; padding: 16px; }
            .cs-chat .message-item .message-item-content { max-width: 70%; }
            .cs-chat .chat-footer { background: #fff; }
            .cs-chat .chat-footer textarea:focus { box-shadow: none; }
            .cs-placeholder { color: #9aa7b8; }
        </style>

        <div class="row no-gutters cs-chat shadow-sm rounded bg-white">
            <div class="col-lg-3 chat-sidebar d-none d-lg-flex">
                <div class="chat-sidebar-header">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm">
                            <span class="avatar-title bg-primary rounded-circle"><i data-feather="users"></i></span>
                        </div>
                        <div class="mr-2 pr-1">
                            <div class="font-weight-bold small">همکاران</div>
                        </div>
                    </div>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" id="convSearch" placeholder="جستجوی همکار...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-white border-0"><i data-feather="search" class="width-14 height-14 text-muted"></i></span>
                        </div>
                    </div>
                </div>
                <div class="chat-sidebar-content">
                    <div class="list-group list-group-flush" id="convList">
                        @forelse($colleagues as $colleague)
                            <a href="javascript:;" class="list-group-item list-group-item-action d-flex align-items-center conversation-item pl-0 pr-0 pb-2 pt-2 pr-2 pl-2"
                               data-id="{{ $colleague->id }}">
                                <div class="pr-2">
                                    <span class="avatar avatar-sm avatar-state-success"><span class="avatar-title rounded-circle">{{ mb_substr($colleague->first_name, 0, 1) }}</span></span>
                                </div>
                                <div class="flex-grow-1 pl-2">
                                    <h6 class="mb-0 small font-weight-600">{{ $colleague->full_name }}</h6>
                                    <span class="last-body small text-muted">
                                        @if(isset($lastMessages[$colleague->id]))
                                            {{ \Illuminate\Support\Str::limit($lastMessages[$colleague->id]->body, 60) }}
                                        @else
                                            بدون پیام
                                        @endif
                                    </span>
                                </div>
                                <div class="text-left ml-auto">
                                    @if(($unreadCounts[$colleague->id] ?? 0) > 0)
                                        <span class="badge badge-danger badge-pill conv-unread">{{ $unreadCounts[$colleague->id] }}</span>
                                    @endif
                                    <span class="small text-muted d-block">
                                        @isset($lastMessages[$colleague->id])
                                            {{ \App\Support\JalaliCalendar::formatShamsiDateTime($lastMessages[$colleague->id]->created_at) }}
                                        @endisset
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-center small text-muted">همکاری ثبت نشده است.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-9 chat-content">
                <div class="chat-header border-bottom d-flex align-items-center px-3 py-2" id="chatHeaderEmpty">
                    <div class="pr-3">
                        <span class="avatar"><span class="avatar-title bg-secondary rounded-circle"><i data-feather="message-circle" class="width-16 height-16"></i></span></span>
                    </div>
                    <div>
                        <h6 class="mb-0">یک همکار را انتخاب کنید</h6>
                        <span class="small text-muted">گفتگو را از لیست کناری باز کنید</span>
                    </div>
                </div>

                <div class="chat-header border-bottom d-flex align-items-center px-3 py-2 d-none" id="chatHeaderActive">
                    <div class="pr-3">
                        <span class="avatar avatar-state-success"><span class="avatar-title rounded-circle" id="chatAvatar">ک</span></span>
                    </div>
                    <div>
                        <h6 class="mb-0" id="chatName">—</h6>
                        <span class="small text-muted" id="chatMeta">—</span>
                    </div>
                    <div class="mr-auto">
                        <span class="badge badge-success" id="chatStatusActive">گفتگو باز است</span>
                    </div>
                </div>

                <div class="messages" id="chatMessages">
                    <div class="text-center cs-placeholder py-5" id="chatPlaceholder">برای شروع گفتگو، یک همکار را از لیست سمت راست انتخاب کنید.</div>
                </div>

                <div class="chat-footer border-top d-flex align-items-center p-2">
                    <form class="d-flex w-100 align-items-center" id="chatForm">
                        <div class="flex-grow-1 pl-2">
                            <textarea class="form-control border-0" id="chatInput" rows="1" placeholder="پیام خود را بنویسید... (اینتر = ارسال)" style="resize:none; max-height:120px;" disabled></textarea>
                        </div>
                        <div class="chat-footer-buttons d-flex">
                            <button class="btn btn-primary rounded-pill px-4" type="submit" id="chatSendBtn" disabled>
                                <i data-feather="send" class="width-15 height-15"></i><span class="d-none d-md-inline"> ارسال</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
(function () {
    var csrf = document.querySelector('meta[name="csrf-token"]');
    csrf = csrf ? csrf.content : '';

    var messagesUrl = @json(route('admin.colleague.support.messages'));
    var conversationsUrl = @json(route('admin.colleague.support.conversations'));
    var sendUrl = @json(route('admin.colleague.support.send'));

    var container = document.getElementById('chatMessages');
    var placeholder = document.getElementById('chatPlaceholder');
    var convList = document.getElementById('convList');
    var search = document.getElementById('convSearch');
    var input = document.getElementById('chatInput');
    var sendBtn = document.getElementById('chatSendBtn');
    var form = document.getElementById('chatForm');
    var headerIdle = document.getElementById('chatHeaderEmpty');
    var headerActive = document.getElementById('chatHeaderActive');
    var chatName = document.getElementById('chatName');
    var chatMeta = document.getElementById('chatMeta');
    var chatAvatar = document.getElementById('chatAvatar');

    var current = null;
    var lastId = 0;
    var initialized = false;
    var busy = false;

    function autoGrow() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    }

    function bubble(message, asSupport) {
        var wrap = document.createElement('div');
        var mine = message.sender_type === (asSupport ? 'support' : 'colleague');
        wrap.className = mine ? 'message-item me' : 'message-item';
        if (message.sender_name) {
            var who = document.createElement('div');
            who.className = 'small font-italic mb-1 text-muted';
            who.textContent = mine ? 'شما' : message.sender_name;
            wrap.appendChild(who);
        }
        var content = document.createElement('div');
        content.className = 'message-item-content';
        content.textContent = message.body;
        var time = document.createElement('span');
        time.className = 'time small text-muted font-italic';
        time.textContent = message.time;
        wrap.appendChild(content);
        wrap.appendChild(time);
        return wrap;
    }

    function renderAll(items) {
        container.innerHTML = '';
        if (!items.length) {
            var d = document.createElement('div');
            d.className = 'text-center cs-placeholder py-4';
            d.textContent = current === null ? 'یک گفتگو را از لیست سمت راست باز کنید.' : 'هنوز پیامی نیست؛ اولین پیام را بفرستید.';
            container.appendChild(d);
        } else {
            items.forEach(function (m) {
                container.appendChild(bubble(m, true));
                lastId = Math.max(lastId, parseInt(m.id, 10));
            });
        }
        container.scrollTop = container.scrollHeight;
    }

    function appendNew(items) {
        items.forEach(function (m) {
            var id = parseInt(m.id, 10);
            if (id > lastId) {
                container.appendChild(bubble(m, true));
                lastId = id;
            }
        });
        if (items.length) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function loadMessages() {
        if (current === null) return;
        var url = messagesUrl + '?colleague_id=' + current + (initialized ? '&after=' + lastId : '');
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d.ok) return;
                if (d.colleague) {
                    chatName.textContent = d.colleague.name;
                    chatAvatar.textContent = (d.colleague.name || 'ک').charAt(0);
                    var meta = [];
                    if (d.colleague.store_name) meta.push(d.colleague.store_name);
                    if (d.colleague.phone) meta.push(d.colleague.phone);
                    chatMeta.textContent = meta.join(' • ');
                }
                if (!initialized) {
                    initialized = true;
                    renderAll(d.messages);
                } else {
                    appendNew(d.messages);
                }
            })
            .catch(function () {});
    }

    function convItem(c, isCurrent) {
        var a = document.createElement('a');
        a.href = 'javascript:;';
        a.className = 'list-group-item list-group-item-action d-flex align-items-center conversation-item pl-2 pr-2 pb-2 pt-2' + (isCurrent ? ' active' : '');
        a.setAttribute('data-id', c.id);

        var av = document.createElement('div');
        av.className = 'pr-2';
        var span = document.createElement('span');
        span.className = 'avatar avatar-sm avatar-state-success';
        var inner = document.createElement('span');
        inner.className = 'avatar-title rounded-circle';
        inner.textContent = (c.name || 'ک').charAt(0);
        span.appendChild(inner);
        av.appendChild(span);
        a.appendChild(av);

        var body = document.createElement('div');
        body.className = 'flex-grow-1 pl-2';
        var h6 = document.createElement('h6');
        h6.className = 'mb-0 font-weight-600';
        h6.textContent = c.name;
        var prev = document.createElement('span');
        prev.className = 'last-body small text-muted';
        prev.textContent = c.last_body ? nudge(c.last_body, 60) : 'بدون پیام';
        body.appendChild(h6);
        body.appendChild(prev);
        a.appendChild(body);

        var right = document.createElement('div');
        right.className = 'text-left';
        if (c.unread > 0) {
            var badge = document.createElement('span');
            badge.className = 'badge badge-danger badge-pill conv-unread';
            badge.textContent = c.unread;
            right.appendChild(badge);
        }
        var tm = document.createElement('span');
        tm.className = 'small text-muted d-block';
        tm.textContent = c.last_time || '';
        right.appendChild(tm);
        a.appendChild(right);

        a.addEventListener('click', function () { selectConversation(parseInt(c.id, 10)); });
        return a;
    }

    function nudge(str, len) {
        str = String(str || '');
        return str.length > len ? str.substr(0, len) + '…' : str;
    }

    function renderConversations(data) {
        var q = (search.value || '').trim().toLowerCase();
        var frag = document.createDocumentFragment();
        var any = false;
        (data || []).forEach(function (c) {
            var hay = (c.name + ' ' + (c.store_name || '') + ' ' + (c.phone || '')).toLowerCase();
            if (q && hay.indexOf(q) === -1) return;
            any = true;
            frag.appendChild(convItem(c, parseInt(c.id, 10) === current));
        });
        convList.innerHTML = '';
        if (!any) {
            var div = document.createElement('div');
            div.className = 'p-3 text-center small text-muted';
            div.textContent = 'همکاری یافت نشد.';
            convList.appendChild(div);
        } else {
            convList.appendChild(frag);
        }
    }

    function loadConversations() {
        fetch(conversationsUrl, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.ok && Array.isArray(d.conversations)) {
                    renderList(d.conversations);
                }
            })
            .catch(function () {});
    }

    function selectConversation(id) {
        current = id;
        lastId = 0;
        initialized = false;
        headerIdle.classList.add('d-none');
        headerActive.classList.remove('d-none');
        input.disabled = false;
        sendBtn.disabled = false;
        container.innerHTML = '';
        var d = document.createElement('div');
        d.className = 'text-center cs-placeholder py-4';
        d.textContent = 'در حال بارگذاری گفتگو...';
        container.appendChild(d);
        loadMessages();
    }

    function send() {
        var text = input.value.trim();
        if (!text || busy || current === null) return;
        busy = true;
        var fd = new FormData();
        fd.append('colleague_id', current);
        fd.append('body', text);
        fetch(sendUrl, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: fd
        })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                busy = false;
                if (!d.ok || !d.message) return;
                input.value = '';
                input.style.height = 'auto';
                if (initialized) {
                    appendNew([d.message]);
                } else {
                    renderAll([d.message]);
                }
            })
            .catch(function () { busy = false; });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        send();
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            send();
            input.focus();
        }
    });

    input.addEventListener('input', autoGrow);
    search.addEventListener('keyup', loadConversations);

    loadConversations();
    setInterval(function () {
        if (current !== null) loadMessages();
        loadConversations();
    }, 3000);
})();
</script>
@endsection