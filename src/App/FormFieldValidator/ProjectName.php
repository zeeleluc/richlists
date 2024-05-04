<?php

namespace App\FormFieldValidator;

use App\Query\UserQuery;

class ProjectName extends FormFieldValidator
{

    public function validate(): void
    {
        if ($this->value === '' || !is_string($this->value)) {
            $this->setMessage('Project name is incorrect.');
        }

        if (ctype_digit($this->value)) {
            $this->setMessage('Project name cannot contain only numbers.');
        }

        $length = strlen($this->value);
        if ($length <= 3) {
            $this->setMessage('Project name must be longer then 3 characters');
        }
        if ($length >= 100) {
            $this->setMessage('Project name must be shorter then 100 characters');
        }

        $query = new UserQuery();
        if ($query->doesProjectExist($this->value)) {
            $this->setMessage('Project with this name already exists.');
        }
    }
}
