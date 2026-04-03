<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Kategori;
use Illuminate\Auth\Access\HandlesAuthorization;

class KategoriPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_kategori');
    }

    public function view(AuthUser $authUser, Kategori $kategori): bool
    {
        return $authUser->can('view_kategori');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_kategori');
    }

    public function update(AuthUser $authUser, Kategori $kategori): bool
    {
        return $authUser->can('update_kategori');
    }

    public function delete(AuthUser $authUser, Kategori $kategori): bool
    {
        return $authUser->can('delete_kategori');
    }

}