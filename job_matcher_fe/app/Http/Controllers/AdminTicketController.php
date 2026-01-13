<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\SupportMessage;

class AdminTicketController extends Controller
{
    public function index(Request $r)
    {
        $tickets = SupportTicket::when($r->status, function ($q, $s) {
            $q->where('status', $s);
        })
            ->latest()->paginate(20);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $messages = $ticket->messages()->oldest()->get();
        return view('admin.tickets.show', compact('ticket', 'messages'));
    }

    public function reply(Request $r, SupportTicket $ticket)
    {
        if ($ticket->status === 'closed') {
            return back()->with('error', 'Ticket đã được đóng, không thể trả lời thêm.');
        }

        $r->validate(['message' => 'required']);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => 'admin',
            'sender_id' => auth()->id(),
            'message' => $r->message
        ]);

        $ticket->update([
            'status' => 'processing',
            'last_reply_by' => 'admin'
        ]);

        return back();
    }

    public function changeStatus(Request $r, SupportTicket $ticket)
    {
        $r->validate(['status' => 'required|in:open,processing,resolved,closed']);

        $ticket->update(['status' => $r->status]);
        return back();
    }
}
