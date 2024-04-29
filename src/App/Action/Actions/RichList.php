<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\RichList\Config;
use App\RichList\Service;
use App\Variable;

class RichList extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/richlist');

        $project = $this->getRequest()->getParam('action');

        $service = new Service($project);
        $countsPerWallet = $service->getCountsPerWalletFromCache();
        if (!$countsPerWallet) {
            $countsPerWallet = $service->getCountsPerWallet();
        }

        $this->setVariable(new Variable('projectName', Config::mapProjectNameSlug($project)));
        $this->setVariable(new Variable('countsPerWallet', $countsPerWallet));
        $this->setVariable(new Variable('collections', $service->getCountsPerWalletBluePrint()['collections']));
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
