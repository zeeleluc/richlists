<?php
namespace App;

use App\Models\User;

class Auth
{

    public function getUser(): User
    {
        return new User();
    }

    public function register(array $values): User
    {

    }

    public function createTempPassword(): string
    {
        return generate_token(10);
    }

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function validateRegistration(array $values): bool
    {
        return true;
    }
}
