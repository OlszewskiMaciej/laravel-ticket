<x-app-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <h1 class="text-white text-lg font-bold">Tickets list</h1>
        <div class="w-full sm:max-w-2xl mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">

            <div class="flex items-center justify-end mt-4 py-4">
                <x-primary-button class="ml-3">
                    <a href="{{route('ticket.create')}}">
                        Create new ticket
                    </a>
                </x-primary-button>
            </div>

            @forelse ($tickets as $ticket)
            <a href="{{route('ticket.show', $ticket->id)}}">
                <div class="text-white flex justify-between py-4 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 rounded">
                    <p class="px-2">{{$ticket->title}}</p>
                    <p class="px-2">{{$ticket->created_at->diffForHumans()}}</p>
                </div>
            </a>

            @empty

            <p class="text-white flex justify-center py-4">You don't have any support ticket yet.</p>

            @endforelse

        </div>
</x-app-layout>