<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Variable;

class Home extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/home');

        $this->setVariable(new Variable('test', 'Test'));
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
