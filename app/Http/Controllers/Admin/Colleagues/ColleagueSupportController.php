<?php

namespace App\Http\Controllers\Admin\Colleagues;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ColleagueMessage;
use App\Support\JalaliCalendar;
use Illuminate\Http\Request;

class ColleagueSupportController extends Controller
{
    public function index()
    {
        $colleagues = Admin::query()
            ->where('is_colleague', true)
            ->orderBy('created_at')
            ->get();

        $lastMessages = [];
        $unreadCounts = [];

        foreach ($colleagues as $colleague) {
            $lastMessages[$colleague->id] = ColleagueMessage::query()
                ->where('colleague_id', $colleague->id)
                ->orderByDesc('id')
                ->first();

            $unreadCounts[$colleague->id] = ColleagueMessage::query()
                ->where('colleague_id', $colleague->id)
                ->where('sender_type', ColleagueMessage::SENDER_COLLEAGUE)
                ->whereNull('read_at')
                ->count();
        }

        return view('backend.colleagues.support.index', [
            'colleagues' => $colleagues,
            'lastMessages' => $lastMessages,
            'unreadCounts' => $unreadCounts,
        ]);
    }

    public function conversations()
    {
        $colleagues = Admin::query()
            ->where('is_colleague', true)
            ->orderBy('created_at')
            ->get();

        $list = [];

        foreach ($colleagues as $colleague) {
            $last = ColleagueMessage::query()
                ->where('colleague_id', $colleague->id)
                ->orderByDesc('id')
                ->first();

            $unread = ColleagueMessage::query()
                ->where('colleague_id', $colleague->id)
                ->where('sender_type', ColleagueMessage::SENDER_COLLEAGUE)
                ->whereNull('read_at')
                ->count();

            $list[] = [
                'id' => $colleague->id,
                'name' => $colleague->full_name,
                'store_name' => $colleague->store_name,
                'phone' => $colleague->phone,
                'last_body' => $last?->body,
                'last_time' => $last ? JalaliCalendar::formatShamsiDateTime($last->created_at) : null,
                'unread' => $unread,
            ];
        }

        return response()->json(['ok' => true, 'conversations' => $list]);
    }

    public function messages(Request $request)
    {
        $colleague = Admin::query()->where('is_colleague', true)->findOrFail((int) $request->query('colleague_id'));

        ColleagueMessage::query()
            ->where('colleague_id', $colleague->id)
            ->where('sender_type', ColleagueMessage::SENDER_COLLEAGUE)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $query = ColleagueMessage::query()->where('colleague_id', $colleague->id);
        if ($after = (int) $request->query('after', 0)) {
            $query->where('id', '>', $after);
        }

        $messages = $query->orderBy('id')->get()->map(fn ($m) => $this->serialize($m));

        return response()->json([
            'ok' => true,
            'colleague' => [
                'id' => $colleague->id,
                'name' => $colleague->full_name,
                'store_name' => $colleague->store_name,
                'phone' => $colleague->phone,
            ],
            'messages' => $messages,
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'colleague_id' => ['required', 'integer', 'exists:admins,id'],
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $colleague = Admin::query()->where('is_colleague', true)->findOrFail((int) $validated['colleague_id']);

        $message = ColleagueMessage::create([
            'colleague_id' => $colleague->id,
            'sender_type' => ColleagueMessage::SENDER_SUPPORT,
            'sender_id' => auth('admin')->id(),
            'body' => $validated['body'],
            'read_at' => null,
        ]);

        return response()->json(['ok' => true, 'message' => $this->serialize($message)]);
    }

    public function unread()
    {
        $count = ColleagueMessage::query()
            ->where('sender_type', ColleagueMessage::SENDER_COLLEAGUE)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread' => $count]);
    }

    private function serialize(ColleagueMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'sender_name' => $message->sender_type === ColleagueMessage::SENDER_SUPPORT
                ? 'پشتیبانی'
                : ($message->sender?->full_name ?: 'همکار'),
            'body' => $message->body,
            'time' => JalaliCalendar::formatShamsiDateTime($message->created_at),
        ];
    }
}