<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\RichList\Config;
use App\RichList\Service;
use App\Variable;

class Html extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('async');
        $this->setView('terminal/html');

        $key = $this->getRequest()->getParam('api');
        $project = env('API_' . $key);
        if (!$project) {
            echo 'RichList not found.';
            exit;
        }

        try {
            $service = new Service($project);
            $countsPerWallet = $service->getCountsPerWalletFromCache();
            if (!$countsPerWallet) {
                $countsPerWallet = $service->getCountsPerWallet();
            }

            $this->setVariable(new Variable('countsPerWallet', $countsPerWallet));
        } catch (\Exception $e) {
            $projectName = Config::mapProjectNameSlug($project);
            echo 'RichList for ' . $projectName . ' almost ready, try again later.';
            exit;
        }
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
