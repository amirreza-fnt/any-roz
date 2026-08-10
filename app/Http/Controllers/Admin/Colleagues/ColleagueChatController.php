<?php

namespace App\Http\Controllers\Admin\Colleagues;

use App\Http\Controllers\Controller;
use App\Models\ColleagueMessage;
use App\Support\JalaliCalendar;
use Illuminate\Http\Request;

class ColleagueChatController extends Controller
{
    public function index()
    {
        return view('backend.colleagues.chat.index');
    }

    public function messages(Request $request)
    {
        $colleagueId = auth('admin')->id();

        ColleagueMessage::query()
            ->where('colleague_id', $colleagueId)
            ->where('sender_type', ColleagueMessage::SENDER_SUPPORT)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $query = ColleagueMessage::query()->where('colleague_id', $colleagueId);
        if ($after = (int) $request->query('after', 0)) {
            $query->where('id', '>', $after);
        }

        $messages = $query->orderBy('id')->get()->map(fn ($m) => $this->serialize($m));

        return response()->json(['ok' => true, 'messages' => $messages]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $message = ColleagueMessage::create([
            'colleague_id' => auth('admin')->id(),
            'sender_type' => ColleagueMessage::SENDER_COLLEAGUE,
            'sender_id' => auth('admin')->id(),
            'body' => $validated['body'],
            'read_at' => null,
        ]);

        return response()->json(['ok' => true, 'message' => $this->serialize($message)]);
    }

    public function unread()
    {
        $count = ColleagueMessage::query()
            ->where('colleague_id', auth('admin')->id())
            ->where('sender_type', ColleagueMessage::SENDER_SUPPORT)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread' => $count]);
    }

    private function serialize(ColleagueMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_type' => $message->sender_type,
            'body' => $message->body,
            'time' => JalaliCalendar::formatShamsiDateTime($message->created_at),
        ];
    }
}