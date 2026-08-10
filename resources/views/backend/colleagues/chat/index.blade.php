@extends('backend.views.view')

@section('main')
<main class="main-content">
    <div class="container-fluid">
        <div class="page-header">
            <h4>چت با پشتیبانی</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ admin_home_url() }}">خانه</a></li>
                    <li class="breadcrumb-item active">چت با پشتیبانی</li>
                </ol>
            </nav>
        </div>

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <style>
            .cs-chat { height: calc(100vh - 190px); min-height: 460px; display: flex; overflow: hidden; }
            .cs-chat > .chat-content { flex: 1; display: flex; flex-direction: column; min-width: 0; }
            .cs-chat .messages { flex: 1; overflow-y: auto; background: #f5f8fb; padding: 16px; }
            .cs-chat .message-item .message-item-content { max-width: 70%; }
            .cs-chat .chat-footer { background: #fff; }
            .cs-chat .chat-footer textarea:focus { box-shadow: none; }
        </style>

        <div class="row no-gutters cs-chat shadow-sm rounded bg-white">
            <div class="col-12 chat-content">
                <div class="chat-header border-bottom d-flex align-items-center px-3 py-2">
                    <div class="pr-3">
                        <span class="avatar">
                            <span class="avatar-title bg-info rounded-circle"><i data-feather="headphones" class="width-16 height-16"></i></span>
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-0">پشتیبانی همکاران</h6>
                        <span class="small text-muted">معمولاً در کمتر از یک ساعت پاسخ می‌دهیم</span>
                    </div>
                    <div class="mr-auto">
                        <span class="badge badge-info" id="chatStatus">متصل</span>
                    </div>
                </div>

                <div class="messages" id="chatMessages">
                    <div class="text-center text-muted py-4" id="chatEmpty">در حال بارگذاری گفتگو...</div>
                </div>

                <div class="chat-footer border-top d-flex align-items-center p-2">
                    <form class="d-flex w-100 align-items-center" id="chatForm">
                        <div class="flex-grow-1 pl-2">
                            <textarea class="form-control border-0" id="chatInput" rows="1" placeholder="پیام خود را بنویسید... (اینتر = ارسال، شیفت+اینتر = خط جدید)" style="resize:none; max-height:120px;"></textarea>
                        </div>
                        <div class="chat-footer-buttons d-flex">
                            <button class="btn btn-primary rounded-pill px-4" type="submit" id="chatSendBtn">
                                <i data-feather="send" class="width-15 height-15"></i>
                                <span class="d-none d-md-inline"> ارسال</span>
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
    var container = document.getElementById('chatMessages');
    var input = document.getElementById('chatInput');
    var form = document.getElementById('chatForm');
    var lastId = 0;
    var initialized = false;
    var busy = false;

    function autoGrow() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    }

    function autoScroll() {
        container.scrollTop = container.scrollHeight;
    }

    function bubble(message) {
        var wrap = document.createElement('div');
        wrap.className = message.sender_type === 'support' ? 'message-item' : 'message-item me';
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

    function emptyState() {
        var d = document.createElement('div');
        d.className = 'text-center text-muted py-4';
        d.textContent = 'هنوز پیامی نیست؛ اولین پیام را بنویسید.';
        return d;
    }

    function renderAll(items) {
        container.innerHTML = '';
        if (!items.length) {
            container.appendChild(emptyState());
            return;
        }
        items.forEach(function (m) {
            container.appendChild(bubble(m));
            lastId = Math.max(lastId, parseInt(m.id, 10));
        });
        autoScroll();
    }

    function appendNew(items) {
        items.forEach(function (m) {
            var id = parseInt(m.id, 10);
            if (id > lastId) {
                container.appendChild(bubble(m));
                lastId = id;
            }
        });
        autoScroll();
    }

    function loadMessages() {
        var url = @json(route('admin.colleague.chat.messages'));
        if (initialized) {
            url += '?after=' + lastId;
        }

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d.ok) return;
                if (!initialized) {
                    initialized = true;
                    renderAll(d.messages);
                } else {
                    appendNew(d.messages);
                }
            })
            .catch(function () {});
    }

    function send() {
        var text = input.value.trim();
        if (!text || busy) return;
        busy = true;
        var fd = new FormData();
        fd.append('body', text);

        fetch('{{ route('admin.colleague.chat.send') }}', {
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

    loadMessages();
    setInterval(loadMessages, 3000);
})();
</script>
@endsection