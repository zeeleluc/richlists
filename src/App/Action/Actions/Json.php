<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\RichList\Config;
use App\RichList\Service;

class Json extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        header('Content-Type: application/json');
        $this->setTerminal(true);

        $key = $this->getRequest()->getParam('api');
        $project = env('API_' . $key);
        if (!$project) {
            $response = [
                'error' => 'RichList not found.',
            ];
            echo json_encode($response);
            exit;
        }

        try {
            $service = new Service($project);
            $countsPerWallet = $service->getCountsPerWalletFromCache();
            if (!$countsPerWallet) {
                $countsPerWallet = $service->getCountsPerWallet();
            }

            $json = json_encode($countsPerWallet);
            echo $json;
            exit;
        } catch (\Exception $e) {
            $projectName = Config::mapProjectNameSlug($project);
            $response = [
                'error' => 'RichList for ' . $projectName . ' almost ready, try again later.',
            ];
            echo json_encode($response);
            exit;
        }
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
