<?php

namespace App\Services;

use App\Models\TicketComment;

class TicketCommentService
{
    public function create($content, $userId, $ticketId)
    {
        return TicketComment::create([
            'content' => $content,
            'user_id' => $userId,
            'ticket_id' => $ticketId,
        ]);
    }
}
