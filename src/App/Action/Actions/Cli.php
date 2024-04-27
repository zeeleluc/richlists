<?php
namespace App\Action\Actions;

use App\Action\BaseAction;

class Cli extends BaseAction
{

    public function __construct()
    {
        if (!$_SERVER['argv']) {
            exit;
        }

        if (!isset($_SERVER['argv'][1])) {
            exit;
        }

        $this->terminal = true;
        parent::__construct();

        $action = $_SERVER['argv'][1];

        if ($action === 'create-data-csv-loading-punks') {


        }

        exit;
    }

    public function run()
    {
        exit;
    }
}
