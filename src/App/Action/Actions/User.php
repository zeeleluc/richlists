<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\Traits\UpdateDataNFTTrait;
use App\Action\BaseAction;
use App\Variable;
use Carbon\Carbon;
use function ArrayHelpers\array_has;

class User extends BaseAction
{

    use UpdateDataNFTTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/user');

        $project = $this->getRequest()->getParam('project');
        if (!$project) {
            abort();
        }

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
