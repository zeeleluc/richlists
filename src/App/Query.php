<?php
namespace App;

use App\Object\BaseObject;

class Query extends BaseObject
{
    private function config()
    {
//        $dotenv = Dotenv::createImmutable(ROOT);
//        $dotenv->load();

        return [
            'host' => $_ENV['DB_HOST'],
            'dbname' => $_ENV['DB_DBNAME'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
        ];
    }
}
