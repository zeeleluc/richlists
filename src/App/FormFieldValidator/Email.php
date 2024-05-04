<?php

namespace App\FormFieldValidator;

class Email extends FormFieldValidator
{

    public function validate(): void
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->setMessage('This is not a valid e-mail.');
        }
    }
}
