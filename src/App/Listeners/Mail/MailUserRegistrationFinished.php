<?php
namespace App\Listeners\Mail;

use App\Listeners\BaseMailListener;

class MailUserRegistrationFinished extends BaseMailListener
{


    public function do()
    {
        $user = $this->getEvent()->getUser();

        // send mail / todo
    }
}
