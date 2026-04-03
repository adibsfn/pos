<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Pelanggan;
use Illuminate\Auth\Access\HandlesAuthorization;

class PelangganPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_pelanggan');
    }

    public function view(AuthUser $authUser, Pelanggan $pelanggan): bool
    {
        return $authUser->can('view_pelanggan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_pelanggan');
    }

    public function update(AuthUser $authUser, Pelanggan $pelanggan): bool
    {
        return $authUser->can('update_pelanggan');
    }

    public function delete(AuthUser $authUser, Pelanggan $pelanggan): bool
    {
        return $authUser->can('delete_pelanggan');
    }

}