<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_permission');
    }

    public function view(AuthUser $authUser, Permission $permission): bool
    {
        return $authUser->can('view_permission');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_permission');
    }

    public function update(AuthUser $authUser, Permission $permission): bool
    {
        return $authUser->can('update_permission');
    }

    public function delete(AuthUser $authUser, Permission $permission): bool
    {
        return $authUser->can('delete_permission');
    }

}