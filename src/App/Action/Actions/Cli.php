<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\Migrate;
use App\Action\Actions\Cli\XRPL\AnalyzeNFTs;
use App\Action\Actions\Cli\XRPL\CalcRichLists;
use App\Action\Actions\Cli\XRPL\UpdateDataNFT;
use App\Action\BaseAction;

class Cli extends BaseAction
{

    private string $action;

    public function __construct()
    {
        $this->terminal = true;
        parent::__construct();

        if (!$_SERVER['argv']) {
            exit;
        }

        if (!isset($_SERVER['argv'][1])) {
            exit;
        }

        $this->action = $_SERVER['argv'][1];

        if ($this->action === 'update-data-nft') {

            // temp. for monitoring
            $start = date_create();
            $slack = new \App\Slack();
            $slack->sendInfoMessage('Started with cronjob `update-data-nft`');
            // temp. for monitoring

            $cliAction = new UpdateDataNFT();
            $cliAction->run();

            // temp. for monitoring
            $took = date_diff($start, date_create())->format('%H:%I:%S');
            $slack->sendInfoMessage('Done with `update-data-nft`, took ' . $took);
            // temp. for monitoring
        }

        if ($this->action === 'calc-richlists') {
            $cliAction = new CalcRichLists();
            $cliAction->run();
        }

        if ($this->action === 'analyze-nfts') {
            $cliAction = new AnalyzeNFTs();
            $cliAction->run();
        }

        if ($this->action === 'migrate') {
            $cliAction = new Migrate();
            $cliAction->run();
        }
    }
}
