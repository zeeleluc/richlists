<?php
namespace App;

use App\Models\User;
use App\Query\UserQuery;

class Auth
{

    public function getUser(): User
    {
        return new User();
    }

    public function createTempPassword(): string
    {
        return generate_token(10);
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function clearLoggedIn(): void
    {
        (new Session())->destroySession('loggedIn');
    }

    public function setLoggedIn(User $user): void
    {
        (new Session())->setSession('loggedIn', $user->id);
    }

    public function isLoggedIn(): bool
    {
        return ! is_null((new Session())->getItem('loggedIn'));
    }

    public function getLoggedInUser(): ?User
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userId = (int) (new Session())->getItem('loggedIn');
        if (!$userId || !is_numeric($userId)) {
            return null;
        }

        return (new UserQuery())->getUserById($userId);
    }
}
