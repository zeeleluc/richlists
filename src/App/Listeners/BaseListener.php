<?php
namespace App\Listeners;

use App\Events\BaseEvent;

abstract class BaseListener
{
    abstract public function do();

    public function getEvent(): BaseEvent
    {
        return $this->event;
    }
}
