<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\RichList\Service;

class Api extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        header('Content-Type: application/json');
        $this->setTerminal(true);

        $key = $this->getRequest()->getParam('api');
        $project = env('API_' . $key);
        if (!$project) {
            abort();
        }

        $service = new Service($project);
        $countsPerWallet = $service->getCountsPerWalletFromCache();
        if (!$countsPerWallet) {
            $countsPerWallet = $service->getCountsPerWallet();
        }

        $json = json_encode($countsPerWallet);
        echo $json;
        exit;
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
