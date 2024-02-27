<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;


class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $ticketService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketService = new TicketService();
        Storage::fake('public');
    }

    public function test_get_tickets()
    {
        $user = User::factory()->create();

        Ticket::factory()->count(6)->create(['user_id' => $user->id]);

        $tickets = $this->ticketService->getTickets($user);

        $this->assertCount(6, $tickets);
    }

    public function test_create_ticket()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = new Request([
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket.',
        ]);

        $this->ticketService->createTicket($requestData);

        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket.',
            'user_id' => $user->id,
        ]);
    }

    public function test_create_ticket_with_attachment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $requestData = new Request([
            'title' => 'Ticket title',
            'description' => 'Ticket description.',
            'attachment' => UploadedFile::fake()->create('test_attachment.pdf'),
        ]);

        $this->ticketService->createTicket($requestData);

        $createdTicket = Ticket::where('title', 'Ticket title')->first();

        Storage::disk('public')->assertExists($createdTicket->attachment);

        $this->assertDatabaseHas('tickets', [
            'title' => 'Ticket title',
            'description' => 'Ticket description.',
            'user_id' => $user->id,
        ]);
    }

    public function test_update_ticket()
    {
        $user = User::factory()->create();

        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
        ]);

        $requestData = new Request([
            'title' => 'Updated ticket title',
            'description' => 'Updated ticket description.',
        ]);

        $this->ticketService->updateTicket($requestData, $ticket);

        $updatedTicket = Ticket::find($ticket->id);

        $this->assertEquals($requestData['title'], $updatedTicket->title);
        $this->assertEquals($requestData['description'], $updatedTicket->description);
    }

    public function test_update_ticket_with_new_attachment()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        $oldAttachmentPath = Storage::disk('public')->path($ticket->attachment);

        $requestData = new Request([
            'title' => 'Updated ticket title',
            'description' => 'Updated ticket description.',
            'attachment' => UploadedFile::fake()->create('test_attachment.pdf'),
        ]);

        $this->ticketService->updateTicket($requestData, $ticket);

        $updatedTicket = Ticket::find($ticket->id);

        $this->assertEquals($requestData['title'], $updatedTicket->title);
        $this->assertEquals($requestData['description'], $updatedTicket->description);

        Storage::disk('public')->assertMissing($oldAttachmentPath);
        Storage::disk('public')->assertExists($updatedTicket->attachment);
    }

    public function test_delete_ticket()
    {
        $ticket = Ticket::factory()->create();

        $this->ticketService->deleteTicket($ticket);

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }

    public function test_delete_ticket_with_attachment()
    {
        $ticket = Ticket::factory()->create(['attachment' => 'attachments/test_attachment.pdf']);

        $this->ticketService->deleteTicket($ticket);

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
        Storage::disk('public')->assertMissing($ticket->attachment);
    }
}
