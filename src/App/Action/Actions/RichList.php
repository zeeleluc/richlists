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
        $projectName = Config::mapProjectNameSlug($project);

        try {
            $service = new Service($project);
            $countsPerWallet = $service->getCountsPerWalletFromCache();
            if (!$countsPerWallet) {
                $countsPerWallet = $service->getCountsPerWallet();

                // we prefer to return always the cache, where the unwanted wallets are already respected.
                // however after a new release caches are deleted, and you must wait max. 15 minutes for the nex cache.
                // symlink a folder could solve this problem, but for now we do it simply like this.
                $unwantedWallets = env('WALLETS_IGNORE_' . strtoupper($project));
                if ($unwantedWallets) {
                    $unwantedWallets = explode(',', $unwantedWallets);
                    foreach ($unwantedWallets as $unwantedWallet) {
                        if (array_key_exists($unwantedWallet, $countsPerWallet)) {
                            unset($countsPerWallet[$unwantedWallet]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            abort('RichList for ' . $projectName . ' almost ready, try again later.', 'danger');
        }

        $this->setVariable(new Variable('projectName', $projectName));
        $this->setVariable(new Variable('countsPerWallet', $countsPerWallet));
        $this->setVariable(new Variable('collections', $service->getCountsPerWalletBluePrint()['collections']));
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
