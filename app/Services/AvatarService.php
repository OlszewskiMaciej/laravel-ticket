<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class AvatarService
{
    public function updateAvatar($user, $avatar)
    {
        $path = $avatar->store('avatars', 'public');

        if ($user->avatar) {
            Storage::delete('/public/' . $user->avatar);
        }

        $user->update(['avatar' => $path]);
    }
}
