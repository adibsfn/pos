<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Satuan;
use Illuminate\Auth\Access\HandlesAuthorization;

class SatuanPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_satuan');
    }

    public function view(AuthUser $authUser, Satuan $satuan): bool
    {
        return $authUser->can('view_satuan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_satuan');
    }

    public function update(AuthUser $authUser, Satuan $satuan): bool
    {
        return $authUser->can('update_satuan');
    }

    public function delete(AuthUser $authUser, Satuan $satuan): bool
    {
        return $authUser->can('delete_satuan');
    }

}