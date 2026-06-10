<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function canCreateAccount(User $user)
    {
        return $user->is_admin;
    }
    
    public function canCreateCourt(User $user)
    {
        return $user->is_owner;
    }

    public function canViewCourts(User $user)
    {
        return $user->is_owner;
    }

    public function update(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id;
    }

}
