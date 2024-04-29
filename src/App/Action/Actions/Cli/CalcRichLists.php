<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\RichList\Config;
use App\RichList\Service;

class CalcRichLists extends BaseAction implements CliActionInterface
{
    public function run()
    {
        $config = new Config();

        foreach ($config->getProjectsIssuerTaxon() as $project => $collections) {
            $countsPerWallet = (new Service($project))->getCountsPerWallet();

            file_put_contents(
                './data/richlists-cache/' . $project . '.json',
                json_encode($countsPerWallet)
            );
        }
    }
}
