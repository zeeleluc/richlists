<?php
namespace App\Query;

use App\Models\User;
use ArrayHelpers\Arr;
use Carbon\Carbon;

class UserQuery extends Query
{

    private string $table = '_users';

    public function createNewUser(array $values): User
    {
        foreach ($values as $key => $value) {
            if ($value instanceof Carbon) {
                $values[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        $result = $this->db->insert($this->table, $values);
        if (!$result) {
            throw new \Exception('User not created.');
        }

        return $this->getUserByEmail(Arr::get($values, 'email'));
    }

    /**
     * @return array|User[]
     * @throws \Exception
     */
    public function getAll(): array
    {
        $results = $this->db->get($this->table);

        $users = [];
        foreach ($results as $result) {
            $users[] = (new User())->fromArray($result);
        }

        return $users;
    }

    public function getUserByEmail(string $email): User
    {
        $values = $this->db
            ->where('email', $email)
            ->getOne($this->table);

        $user = new User();
        return $user->fromArray($values);
    }

    public function getUserById(int $id): ?User
    {
        $results = $this->db
            ->where('id', $id)
            ->getOne($this->table);

        if ($results) {
            return (new User())->fromArray($results);
        }

        return null;
    }

    public function getUserByProject(string $project): ?User
    {
        $results = $this->db
            ->where('project_name', $project)
            ->getOne($this->table);

        if ($results) {
            return (new User())->fromArray($results);
        }

        return null;
    }

    public function doesProjectExist(string $projectName): bool
    {
        return (bool) $this->db
            ->where('project_name', $projectName)
            ->getOne($this->table);
    }
}
