<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Services\TicketCommentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketCommentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $ticketCommentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketCommentService = new TicketCommentService();
    }

    public function test_create_ticket_comment()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $content = "This is a test comment.";

        $comment = $this->ticketCommentService->create($content, $user->id, $ticket->id);

        $this->assertNotNull($comment);
        $this->assertDatabaseHas('ticket_comments', [
            'content' => $content,
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
        ]);
    }
}
