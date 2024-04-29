<?php
namespace App;

use App\Action;
use App\Action\Action as AbstractAction;
use App\Action\BaseAction;
use App\Object\BaseObject;
use App\Object\ObjectManager;
use App\RichList\Config;

class Initialize extends BaseObject
{
    public function __construct()
    {
        ObjectManager::set(new Request());
        ObjectManager::set(new Session());
        ObjectManager::set(new Query());
        ObjectManager::set(new AbstractAction());
    }

    public function action(): Initialize
    {
        $this->getAbstractAction()->setAction($this->resolveAction());
        $this->getAbstractAction()->getAction()->run();

        return $this;
    }

    public function show(): void
    {
        $variables = $this->getAbstractAction()->getAction()->getVariables();

        extract($variables);

        ob_start();
        if (false === $this->getAbstractAction()->getAction()->getTemplate()->isTerminal()) {
            require_once ROOT . DS . 'templates' . DS . 'views' . DS . $this->getAbstractAction()->getAction()->getTemplate()->getView()->getViewName() . '.phtml';
        }
        $content = ob_get_contents();
        ob_end_clean();

        if (false === $this->getAbstractAction()->getAction()->getTemplate()->isTerminal()) {
            ob_start();
            require_once ROOT . DS . 'templates' . DS . 'layouts' . DS . $this->getAbstractAction()->getAction()->getTemplate()->getLayout()->getLayoutName() . '.phtml';
            $html = ob_get_contents();
            ob_end_clean();
        } else {
            $html = $content;
        }

        echo $html;
    }

    /**
     * @return BaseAction
     * @throws \Exception
     */
    private function resolveAction(): BaseAction
    {
        $get = $this->getRequest()->get();

        if (is_cli()) {
            return new Action\Actions\Cli();
        }

        if (false === isset($get['action']) || (true === isset($get['action']) && '' === $get['action'])) {
            return new \App\Action\Actions\Home();
        }

        if ($get['action'] === 'all-richlists') {
            return new Action\Actions\AllRichLists();
        }

        if ($get['action'] === 'json') {
            return new Action\Actions\Json();
        }

        if ($get['action'] === 'html') {
            return new Action\Actions\Html();
        }

        // check if this is a request for a richlist
        $config = new Config();
        $projects = array_keys($config->getProjectsIssuerTaxon());
        if (in_array($get['action'], $projects)) {
            return new \App\Action\Actions\RichList();
        }

        throw new \Exception('Page not found.');
    }

}
