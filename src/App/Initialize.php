<?php
namespace App;

use App\Action;
use App\Action\Action as AbstractAction;
use App\Action\BaseAction;
use App\Object\BaseObject;
use App\Object\ObjectManager;
use App\Query\BlockchainTokenQuery;
use App\Query\CollectionQuery;
use App\Query\UserQuery;

class Initialize extends BaseObject
{
    public function __construct()
    {
        ObjectManager::set(new Request());
        ObjectManager::set(new Session());
        ObjectManager::set(new AbstractAction());
        ObjectManager::set(new Auth());

        // set query classes
        ObjectManager::set(new BlockchainTokenQuery());
        ObjectManager::set(new CollectionQuery());
        ObjectManager::set(new UserQuery());
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

        if ($get['action'] === 'projects') {
            return new Action\Actions\Projects();
        }

        if ($get['action'] === 'json') {
            if (isset($get['chain']) && $get['chain'] === 'xrpl') {
                return new Action\Actions\XRPL\Json();
            }
        }

        if ($get['action'] === 'html') {
            if (isset($get['chain']) && $get['chain'] === 'xrpl') {
                return new Action\Actions\XRPL\Html();
            }
        }

        if ($get['action'] === 'project') {
            foreach ($this->getUserQuery()->getAll() as $user) {
                if ($user->projectSlug === $get['project']) {
                    if ($get['chain'] === 'xrpl') {
                        return new Action\Actions\XRPL\RichList();
                    }
                }
            }
        }

        throw new \Exception('Page not found.');
    }

}
