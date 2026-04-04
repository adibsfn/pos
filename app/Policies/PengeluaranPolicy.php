<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Pengeluaran;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengeluaranPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_pengeluaran');
    }

    public function view(AuthUser $authUser, Pengeluaran $pengeluaran): bool
    {
        return $authUser->can('view_pengeluaran');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_pengeluaran');
    }

    public function update(AuthUser $authUser, Pengeluaran $pengeluaran): bool
    {
        return $authUser->can('update_pengeluaran');
    }

    public function delete(AuthUser $authUser, Pengeluaran $pengeluaran): bool
    {
        return $authUser->can('delete_pengeluaran');
    }

}