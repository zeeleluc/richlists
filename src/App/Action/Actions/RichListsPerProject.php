<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\Variable;

class RichListsPerProject extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/richlists-per-project');

        $project = $this->getRequest()->getParam('project');
        $user = $this->getUserQuery()->getUserByProject($project);
        $projectName = $user->projectName;

        $this->setVariable(new Variable('projectName', $projectName));
        $this->setVariable(new Variable('user', $user));
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
