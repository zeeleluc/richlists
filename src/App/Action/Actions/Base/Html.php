<?php
namespace App\Action\Actions\Base;

use App\Action\BaseAction;
use App\Services\Base\CalcRichListService;
use App\Variable;

class Html extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('async');
        $this->setView('terminal/html');

        $project = $this->getRequest()->getParam('project');
        $key = $this->getRequest()->getParam('api');
        $user = $this->getUserQuery()->getUserByProject($project);

        if ($key !== $user->token) {
            echo 'RichList not found.';
            exit;
        }

        try {
            $service = new CalcRichListService($project);
            $countsPerWallet = $service->getCountsPerWalletFromCache();
            if (!$countsPerWallet) {
                $countsPerWallet = $service->getCountsPerWallet();
            }

            $this->setVariable(new Variable('countsPerWallet', $countsPerWallet));
            if ($css = $this->getRequest()->getParam('css')) {
                $cssLines = explode(',', $css);
                $cssRules = [];
                foreach ($cssLines as $cssLine) {
                    $cssStyle = explode('::', $cssLine);
                    $cssStyle[0] = str_replace('_', ' ', $cssStyle[0]);
                    $cssStyle[1] = str_replace('_', ' ', $cssStyle[1]);
                    $cssRules[] = $cssStyle;
                }
                $this->setVariable(new Variable('cssRules', $cssRules));
            } else {
                $this->setVariable(new Variable('cssRules',null));
            }
        } catch (\Exception $e) {
            $projectName = $user->projectName;
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
