<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\Migrate;
use App\Action\Actions\Cli\XRPL\AnalyzeNFTs as AnalyzeNFTsXRPL;
use App\Action\Actions\Cli\XRPL\CalcRichLists as CalcRichListsXRPL;
use App\Action\Actions\Cli\XRPL\UpdateDataNFT as UpdateDataNFTXRPL;
use App\Action\Actions\Cli\Ethereum\UpdateDataNFT as UpdateDataNFTEthereum;
use App\Action\Actions\Cli\Ethereum\CalcRichLists as CalcRichListsEthereum;
use App\Action\BaseAction;

class Cli extends BaseAction
{

    private string $action;

    /**
     * @throws \Exception
     */
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

        if ($this->action === 'update-data-nft-xrpl') {
            $start = $this->startMonitoring('update-data-nft');
            (new UpdateDataNFTXRPL())->run();
            $this->stopMonitoring('update-data-nft', $start);
        }

        if ($this->action === 'update-data-nft-ethereum') {
            $start = $this->startMonitoring('update-data-ethereum');
            (new UpdateDataNFTEthereum())->run();
            $this->stopMonitoring('update-data-nft', $start);
        }

        if ($this->action === 'calc-richlists-xrpl') {
            (new CalcRichListsXRPL())->run();
        }

        if ($this->action === 'calc-richlists-ethereum') {
            (new CalcRichListsEthereum())->run();
        }

        if ($this->action === 'analyze-nfts-xrpl') {
            (new AnalyzeNFTsXRPL())->run();
        }

        if ($this->action === 'migrate') {
            (new Migrate())->run();
        }
    }

    private function startMonitoring(string $cronjob): \DateTime
    {
        $slack = new \App\Slack();
        $slack->sendInfoMessage('Started with cronjob `' . $cronjob . '`');

        return date_create();
    }

    private function stopMonitoring(string $cronjob, \DateTime $start): void
    {
        $took = date_diff($start, date_create())->format('%H:%I:%S');

        $slack = new \App\Slack();
        $slack->sendInfoMessage('Done with `update-data-nft`, took ' . $took);
    }
}
