<?php
namespace App\Action\Actions;

use App\Action\BaseAction;
use App\RichList\Config;
use App\Variable;

class AllRichLists extends BaseAction
{
    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/all-richlists');

        $this->setVariable(new Variable('projects', (new Config())->getProjectsIssuerTaxon()));
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
