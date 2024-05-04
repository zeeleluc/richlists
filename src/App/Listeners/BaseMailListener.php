<?php
namespace App\Listeners;

use App\Events\BaseUserEvent;

abstract class BaseMailListener extends BaseListener
{
    public function __construct(protected readonly BaseUserEvent $event)
    {

    }

    public function getEvent(): BaseUserEvent
    {
        return parent::getEvent();
    }
}
