<?php

namespace App\Policies\Marking;

use App\Models\Marking\Criterion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CriterionPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user)
    {
        if ($user->roles->contains('name', 'Admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Marking\Criterion  $criterion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Criterion $criterion)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->roles->contains('name', 'Admin')
            || $user->roles->contains('name', 'HeadTeacher');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Marking\Criterion  $criterion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Criterion $criterion)
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Marking\Criterion  $criterion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Criterion $criterion)
    {
        return $this->create($user);
    }
}
