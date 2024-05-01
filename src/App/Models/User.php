<?php

namespace App\Models;

use App\Query\CollectionQuery;
use App\Query\UserQuery;
use ArrayHelpers\Arr;
use Carbon\Carbon;

class User extends BaseModel
{

    public ?int $id = null;

    public string $email;

    public string $password;

    public string $projectName;

    public string $token;

    public Carbon $tokenExpiresAt;

    public ?string $emailValidationToken = null;

    public ?string $resetPasswordToken = null;

    public ?Carbon $createdAt = null;

    public ?Carbon $updatedAt = null;

    public ?Carbon $deletedAt = null;

    public function isAdmin()
    {
        return $this->email === env('ADMIN_EMAIL');
    }

    public function initNew(array $values)
    {
        $user = $this->fromArray($values);

        return $user->save();
    }

    public function fromArray(array $values): User
    {
        $user = new $this;
        if ($id = Arr::get($values, 'id')) {
            $user->id = $id;
        }
        $user->email = Arr::get($values, 'email');
        $user->password = Arr::get($values, 'password');
        $user->projectName = Arr::get($values, 'project_name');
        $user->token = Arr::get($values, 'token');
        if ($tokenExpiresAt = Arr::get($values, 'token_expires_at')) {
            $user->tokenExpiresAt = Carbon::parse($tokenExpiresAt);
        }
        $user->emailValidationToken = Arr::get($values, 'email_validation_token');
        $user->resetPasswordToken = Arr::get($values, 'reset_password_token');
        if ($createdAt = Arr::get($values, 'created_at')) {
            $user->createdAt = Carbon::parse($createdAt);
        }
        if ($updatedAt = Arr::get($values, 'updated_at')) {
            $user->updatedAt = Carbon::parse($updatedAt);
        }
        if ($deletedAt = Arr::get($values, 'deleted_at')) {
            $user->deletedAt = Carbon::parse($deletedAt);
        }

        return $user;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->id) {
            $array['id'] = $this->id;
        }
        $array['email'] = $this->email;
        $array['password'] = $this->password;
        $array['project_name'] = $this->projectName;
        $array['token'] = $this->token;
        $array['token_expires_at'] = $this->tokenExpiresAt;
        if ($this->emailValidationToken) {
            $array['email_validation_token'] = $this->emailValidationToken;
        }
        if ($this->resetPasswordToken) {
            $array['reset_password_token'] = $this->resetPasswordToken;
        }
        if ($this->createdAt) {
            $array['created_at'] = $this->createdAt;
        }
        if ($this->updatedAt) {
            $array['updated_at'] = $this->updatedAt;
        }
        if ($this->deletedAt) {
            $array['deleted_at'] = $this->deletedAt;
        }

        return $array;
    }

    /**
     * @return array|Collection[]
     * @throws \Exception
     */
    public function getCollections(): array
    {
        $collectionsQuery = new CollectionQuery();

        return $collectionsQuery->getCollectionsForUser($this);
    }

    /**
     * @return array|Collection[]
     * @throws \Exception
     */
    public function getCollectionsForChain(string $chain): array
    {
        $collectionsQuery = new CollectionQuery();

        return $collectionsQuery->getCollectionsForUserByChain($this, $chain);
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        if ($this->getQueryObject()->doesProjectExist($this->projectName)) {
            throw new \Exception('Project `' . $this->projectName . '` exists!');
        }

        return $this->getQueryObject()->createNewUser($this->toArray($this));
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function getQueryObject()
    {
        return new UserQuery();
    }
}
