<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request)
    {
        //$path = Storage::disk('public')->put('avatars', $request->file('avatar'));
        $path = $request->file('avatar')->store('avatars', 'public');

        if ($request->user()->avatar) {
            Storage::delete('/public/' . $request->user()->avatar);
        }

        auth()->user()->update(['avatar' => $path]);

        //return response()->redirectTo(route('profile.edit'));
        return back()->with('message', 'Avatar is changed.');
    }
}
