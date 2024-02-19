<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'user_id' => User::factory(),
            'attachment' => function () {
                $extension = 'pdf';
                $filename = Str::random(25);
                $path = "attachments/$filename.$extension";
                Storage::disk('public')->put($path, '');
                return $path;
            },
        ];
    }
}
