<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\Response;

class VideoPolicy
{
    public function view(User $user, Video $video): Response
    {
        return $video->user_id === $user->id
            ? Response::allow()
            : Response::deny('You can only view your own videos.');
    }

    public function update(User $user, Video $video): Response
    {
        return $video->user_id === $user->id
            ? Response::allow()
            : Response::deny('You can only update your own videos.');
    }

    public function delete(User $user, Video $video): Response
    {
        return $video->user_id === $user->id
            ? Response::allow()
            : Response::deny('You can only delete your own videos.');
    }
}
