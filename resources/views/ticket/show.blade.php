<x-app-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <h1 class="text-white text-lg font-bold">{{ $ticket->title }}</h1>
        <div class="w-full sm:max-w-xl mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">

            <div class="text-white py-4">
                <p class="py-4">{{$ticket->title}}</p>
                <p class="py-4">{{$ticket->description}}</p>
                <p class="py-4">{{$ticket->created_at}}</p>

                @if($ticket->attachment)
                <a href="{{'/storage/'.$ticket->attachment}}" target="_blank" class="py-4">Attachment</a>
                @endif

            </div>

            <div class="flex justify-between">
                <div class="flex">
                    <a href="{{route('ticket.edit', $ticket->id)}}">
                        <x-primary-button>Edit</x-primary-button>
                    </a>

                    <form action="{{ route('ticket.destroy', $ticket->id) }}" method="post">
                        @method('delete')
                        @csrf
                        <x-primary-button>Delete</x-primary-button>
                    </form>
                </div>

                @if(auth()->user()->isAdmin)
                <div class="flex">
                    <form action="{{ route('ticket.update', $ticket->id) }}" method="post">
                        @method('patch')
                        @csrf
                        <div class="flex items-center mb-4">
                            <select name="status" id="status" class="border rounded">
                                <option value="open" {{ $ticket->status == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="rejected" {{ $ticket->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            <x-primary-button type="submit">Update Status</x-primary-button>
                        </div>
                    </form>
                </div>
                @else
                <p class="text-white">Status: {{ $ticket->status }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>