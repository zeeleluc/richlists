<?php
namespace App\Action\Actions;

use App\Action\BaseFormAction;
use App\Auth;
use App\FormFieldValidator\Email;
use App\FormFieldValidator\ProjectName;
use App\Models\User;

class Register extends BaseFormAction
{

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct('register');

        $this->setLayout('default');
        $this->setView('website/register');

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
            new ProjectName('project_name', $this->getRequest()->getPostParam('project_name')),
        ]);
    }

    protected function handleForm(): void
    {
        $auth = new Auth();

        $email = $this->validatedFormValues['email'];
        $password = $auth->hashPassword($auth->createTempPassword());
        $projectName = $this->validatedFormValues['project_name'];
        $projectSlug = flatten_string($projectName);

        $user = new User();
        $user = $user->fromArray([
            'email' => $email,
            'password' => $password,
            'project_name' => $projectName,
            'project_slug' => $projectSlug,
            'token' => generate_token(),
            'token_expires_at' => \Carbon\Carbon::now()->addYearNoOverflow()->format('Y-m-d H:i:s'),
        ]);
        $user = $user->save();

//        Event::do(UserRegistered::class, $user);

        success('register', 'Check your mail for your temporarily password');
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
