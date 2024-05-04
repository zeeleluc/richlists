<?php

namespace App\Events;

use App\Models\User;

abstract class BaseUserEvent extends BaseEvent
{

    public function __construct(private readonly User $user)
    {

    }

    public function getUser(): User
    {
        return $this->user;
    }
}
