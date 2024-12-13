<?php

// app/Policies/MakulPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Makul;

class MakulPolicy
{
    /**
     * Determine if the user can view the makul.
     */
    public function view(User $user, Makul $makul)
    {
        return $user->role === 'admin'; // Example: only admin can view
    }

    /**
     * Determine if the user can create a makul.
     */
    public function create(User $user)
    {
        return $user->role === 'admin'; // Example: only admin can create
    }

    /**
     * Determine if the user can update the makul.
     */
    public function update(User $user, Makul $makul)
    {
        return $user->role === 'admin'; // Example: only admin can update
    }

    /**
     * Determine if the user can delete the makul.
     */
    public function delete(User $user, Makul $makul)
    {
        return $user->role === 'admin'; // Example: only admin can delete
    }
}
