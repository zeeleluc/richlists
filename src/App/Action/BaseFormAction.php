<?php
namespace App\Action;

use App\FormFieldValidator\FormFieldValidator;

abstract class BaseFormAction extends BaseAction
{

    private string $formRoute;

    protected array $formErrors = [];

    protected array $validatedFormValues = [];

    abstract protected function performGet();

    abstract protected function performPost();

    abstract protected function handleForm();

    public function __construct(string $formRoute)
    {
        $this->formRoute = $formRoute;

        parent::__construct();
    }

    public function hasFormErrors(): bool
    {
        return count($this->formErrors) >= 1;
    }

    public function getValidatedFormValues(): array
    {
        return $this->validatedFormValues;
    }

    protected function validateFormValues(array $formFieldValidators): void
    {
        foreach ($formFieldValidators as $formFieldValidator) { /* @var $formFieldValidator FormFieldValidator */
            $formFieldValidator->validate();
            if (!$formFieldValidator->isValid()) {
                foreach ($formFieldValidator->getMessages() as $message) {
                    $this->formErrors[] = $message;
                }
            } else {
                $this->validatedFormValues[$formFieldValidator->key] = $formFieldValidator->value;
            }
        }

        if ($this->hasFormErrors()) {
            warning($this->formRoute, 'Fix the form errors and try again.');
        } else {
            $this->handleForm();
        }
    }
}
