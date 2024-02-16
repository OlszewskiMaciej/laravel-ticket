<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Services\AvatarService;

class AvatarController extends Controller
{
    protected $avatarService;

    public function __construct(AvatarService $avatarService)
    {
        $this->avatarService = $avatarService;
    }

    public function update(UpdateAvatarRequest $request)
    {
        $this->avatarService->updateAvatar($request->user(), $request->file('avatar'));

        return back()->with('message', 'Avatar is changed.');
    }
}
