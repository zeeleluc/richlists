<?php
namespace App\Events\User;

use App\Events\BaseUserEvent;
use App\Models\User;

class UserRegistered extends BaseUserEvent
{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
