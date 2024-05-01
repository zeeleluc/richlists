<?php
namespace App\Action\Actions\XRPL;

use App\Action\BaseAction;
use App\Services\XRPL\CalcRichListService;

class Json extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        header('Content-Type: application/json');
        $this->setTerminal(true);

        $project = $this->getRequest()->getParam('project');
        $key = $this->getRequest()->getParam('api');
        $user = $this->getUserQuery()->getUserByProject($project);

        if ($key !== $user->token) {
            $response = [
                'error' => 'RichList not found.',
            ];
            echo json_encode($response);
            exit;
        }

        try {
            $service = new CalcRichListService($project);
            $countsPerWallet = $service->getCountsPerWalletFromCache();
            if (!$countsPerWallet) {
                $countsPerWallet = $service->getCountsPerWallet();
            }

            $json = json_encode($countsPerWallet);
            echo $json;
            exit;
        } catch (\Exception $e) {
            $projectName = $project; // @todo replace
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
