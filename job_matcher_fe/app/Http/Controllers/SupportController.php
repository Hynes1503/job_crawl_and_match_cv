<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->latest()->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'title' => 'required|max:255',
            'message' => 'required',
            'category' => 'required',
            'priority' => 'required',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'title' => $r->title,
            'category' => $r->category,
            'priority' => $r->priority,
            'status' => 'open',
            'last_reply_by' => 'user'
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
            'message' => $r->message
        ]);

        return redirect()->route('support.show', $ticket)->with('success', 'Đã gửi phản ánh');
    }

    public function show(SupportTicket $ticket)
    {
        abort_if($ticket->user_id != auth()->id(), 403);
        $messages = $ticket->messages()->oldest()->get();

        return view('support.show', compact('ticket', 'messages'));
    }

    public function reply(Request $r, SupportTicket $ticket)
    {
        abort_if($ticket->user_id != auth()->id(), 403);

        if ($ticket->status === 'closed') {
            return back()->with('error', 'Ticket đã được đóng, bạn không thể phản hồi thêm.');
        }

        $r->validate(['message' => 'required']);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
            'message' => $r->message
        ]);

        $ticket->update([
            'last_reply_by' => 'user',
            'status' => 'open'
        ]);

        return back();
    }
}
