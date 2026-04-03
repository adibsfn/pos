<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Session;
use Illuminate\Auth\Access\HandlesAuthorization;

class SessionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_session');
    }

    public function view(AuthUser $authUser, Session $session): bool
    {
        return $authUser->can('view_session');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_session');
    }

    public function update(AuthUser $authUser, Session $session): bool
    {
        return $authUser->can('update_session');
    }

    public function delete(AuthUser $authUser, Session $session): bool
    {
        return $authUser->can('delete_session');
    }

}