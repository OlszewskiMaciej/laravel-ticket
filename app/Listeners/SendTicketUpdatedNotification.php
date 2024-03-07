<?php

namespace App\Listeners;

use App\Events\TicketUpdated;
use App\Notifications\TicketUpdatedNotification;

class SendTicketUpdatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TicketUpdated $event): void
    {
        $event->ticket->user->notify(new TicketUpdatedNotification($event->ticket));
    }
}
