<?php
namespace App\Action\Actions\Base;

use App\Action\BaseAction;
use App\Services\Base\CalcRichListService;
use App\Variable;

class RichList extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/richlist');

        $project = $this->getRequest()->getParam('project');
        $user = $this->getUserQuery()->getUserByProject($project);
        $projectName = $user->projectName;

        try {
            $service = new CalcRichListService($project);
            $countsPerWallet = $service->getCountsPerWalletFromCache();
            if (!$countsPerWallet) {
                $countsPerWallet = $service->getCountsPerWallet();
            }
            $this->setVariable(new Variable('chain', 'Base'));
            $this->setVariable(new Variable('user', $user));
            $this->setVariable(new Variable('countsPerWallet', $countsPerWallet));
            $this->setVariable(new Variable('collections', $service->getCountsPerWalletBluePrint()['collections']));
        } catch (\Exception $e) {
            abort('RichList for ' . $projectName . ' almost ready, try again later.', 'danger');
        }

        $this->setVariable(new Variable('projectName', $projectName));
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
