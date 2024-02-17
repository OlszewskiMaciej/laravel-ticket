<?php

namespace App\Services;

use App\Models\Ticket;
use App\Notifications\TicketUpdatedNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketService
{
    public function getTickets($user)
    {
        if ($user->role == 'admin') {
            return Ticket::latest()->get();
        } else {
            return $user->tickets->sortByDesc('created_at');
        }
    }

    public function createTicket($request)
    {
        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        if ($request->file('attachment')) {
            $this->storeAttachment($request, $ticket);
        }
    }

    public function updateTicket($request, $ticket)
    {
        $ticket->update($request->except('attachment'));

        if ($request->has('status')) {
            $ticket->update(['status' => $request->status]);
            $ticket->user->notify(new TicketUpdatedNotification($ticket));
        }

        if ($request->file('attachment')) {
            if ($ticket->attachment) {
                Storage::disk('public')->delete($ticket->attachment);
            }

            $this->storeAttachment($request, $ticket);
        }
    }

    public function deleteTicket($ticket)
    {
        if ($ticket->attachment) {
            Storage::disk('public')->delete($ticket->attachment);
        }

        $ticket->delete();
    }

    protected function storeAttachment($request, $ticket)
    {
        $extension = $request->file('attachment')->extension();
        $contents = file_get_contents($request->file('attachment'));
        $filename = Str::random(25);
        $path = "attachments/$filename.$extension";

        Storage::disk('public')->put($path, $contents);

        $ticket->update(['attachment' => $path]);
    }
}
