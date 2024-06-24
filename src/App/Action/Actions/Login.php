<?php
namespace App\Action\Actions;

use App\Action\BaseFormAction;
use App\Auth;
use App\FormFieldValidator\Email;
use App\FormFieldValidator\Password;

class Login extends BaseFormAction
{

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct('login');

        if ($this->auth->isLoggedIn()) {
            abort('Already logged in.');
        }

        $this->setLayout('default');
        $this->setView('website/login');

        if ($this->getRequest()->isGet()) {
            $this->performGet();
        } elseif ($this->getRequest()->isPost()) {
            $this->performPost();
        }
    }

    /**
     * @throws \Exception
     */
    protected function performPost()
    {
        $this->validateFormValues([
            new Email('email', $this->getRequest()->getPostParam('email')),
            new Password('password', $this->getRequest()->getPostParam('password')),
        ], 'Login failed.');
    }

    protected function handleForm(): void
    {
        $user = $this->getUserQuery()->getUserByEmail($this->validatedFormValues['email']);
        if (!$user) {
            warning('login', 'Login failed');
        }

        $verified = $this->auth
            ->verify($this->validatedFormValues['password'], $user->password);

        if ($verified) {
            $this->auth->setLoggedIn($user);
            success('', 'Login success.');
        } else {
            warning('login', 'Login failed.');
        }
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
