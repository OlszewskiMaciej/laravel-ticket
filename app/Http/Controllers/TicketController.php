<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Notifications\TicketUpdatedNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role == 'admin') {
            $tickets = Ticket::latest()->get();
        } else {
            $tickets = $user->tickets->sortByDesc('created_at');
        }

        // return view('ticket.index', ['tickets' => $tickets]);
        return view('ticket.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ticket.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        if ($request->file('attachment')) {
            $this->storeAttachment($request, $ticket);
        }

        return redirect(route('ticket.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return view('ticket.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        return view('ticket.edit', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $ticket->update($request->except('attachment'));

        if ($request->has('status')) {
            $ticket->update(['status' => $request->status]);
            // $user = User::find($ticket->user_id);
            $ticket->user->notify(new TicketUpdatedNotification($ticket));

            // return (new TicketUpdatedNotification($ticket))->toMail($user);
        }

        if ($request->file('attachment')) {
            if ($ticket->attachment) {
                Storage::disk('public')->delete($ticket->attachment);
            }

            $this->storeAttachment($request, $ticket);
        }

        return redirect(route('ticket.show', $ticket->id));
    }

    /** 
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        if ($ticket->attachment) {
            Storage::disk('public')->delete($ticket->attachment);
        }

        $ticket->delete();

        return redirect(route('ticket.index'));
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
