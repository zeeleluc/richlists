<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\CalcRichLists;
use App\Action\Actions\Cli\UpdateDataNFT;
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
            $slack->sendInfoMessage('Started with cronjob `updata-data-nft`');
            // temp. for monitoring

            $cliAction = new UpdateDataNFT();
            $cliAction->run();

            // temp. for monitoring
            $took = date_diff($start, date_create())->format('%H:%I:%S');
            $slack->sendInfoMessage('Done, took ' . $took);
            // temp. for monitoring
        }

        if ($this->action === 'calc-richlists') {

            // temp. for monitoring
            $start = date_create();
            $slack = new \App\Slack();
            $slack->sendInfoMessage('Started with cronjob `calc-richlists`');
            // temp. for monitoring

            $cliAction = new CalcRichLists();
            $cliAction->run();

            // temp. for monitoring
            $took = date_diff($start, date_create())->format('%H:%I:%S');
            $slack->sendInfoMessage('Done, took ' . $took);
            // temp. for monitoring
        }
    }
}
