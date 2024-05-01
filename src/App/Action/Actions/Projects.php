<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Variable;

class Projects extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/projects');

        $this->setVariable(new Variable('users', $this->getUserQuery()->getAll()));
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
