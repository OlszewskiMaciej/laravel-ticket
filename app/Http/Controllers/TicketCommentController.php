<?php

namespace App\Http\Controllers;

use App\Services\TicketCommentService;
use App\Http\Requests\StoreTicketCommentRequest;

class TicketCommentController extends Controller
{
    protected $ticketCommentService;

    public function __construct(TicketCommentService $ticketCommentService)
    {
        $this->ticketCommentService = $ticketCommentService;
    }

    public function store(StoreTicketCommentRequest $request)
    {
        $this->ticketCommentService->create($request->content, auth()->id(), $request->ticket_id);

        return redirect()->route('ticket.show', $request->ticket_id);
    }
}
