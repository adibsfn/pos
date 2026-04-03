<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_category');
    }

    public function view(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('view_category');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_category');
    }

    public function update(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('update_category');
    }

    public function delete(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('delete_category');
    }

}