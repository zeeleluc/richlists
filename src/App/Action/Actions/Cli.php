<?php
namespace App\Action\Actions;

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
            $cliAction = new UpdateDataNFT();
            $cliAction->run();
        }
    }
}
