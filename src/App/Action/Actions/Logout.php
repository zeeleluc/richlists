<?php
namespace App\Action\Actions;

use App\Action\BaseAction;

class Logout extends BaseAction
{

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct('login');

        if (!$this->auth->isLoggedIn()) {
            abort('Already logged out.');
        }

        $this->auth->clearLoggedIn();
        success('', 'Logged out!');
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
