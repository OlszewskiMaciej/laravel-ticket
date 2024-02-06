<?php

namespace App\Http\Controllers;

use App\Models\TicketComment;
use App\Http\Requests\StoreTicketCommentRequest;

class TicketCommentController extends Controller
{
    public function store(StoreTicketCommentRequest $request)
    {
        TicketComment::create([
            'content' => $request->content,
            'user_id' => auth()->id(),
            'ticket_id' => $request->ticket_id,
        ]);

        return redirect()->route('ticket.show', $request->ticket_id);
    }
}
