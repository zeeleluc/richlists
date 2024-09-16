<?php
namespace App\Query;

use App\Models\Collection;
use App\Models\User;
use ArrayHelpers\Arr;
use Carbon\Carbon;

class CollectionQuery extends Query
{

    private string $table = '_collections';

    public function createNewCollection(array $values): Collection
    {
        foreach ($values as $key => $value) {
            if ($value instanceof Carbon) {
                $values[$key] = $value->format('Y-m-d H:i:s');
            }
        }

        $result = $this->db->insert($this->table, $values);
        if (!$result) {
            throw new \Exception('Collection not created.');
        }

        $values = $this->getCollectionByChainAndNameAndUser(
            Arr::get($values, 'chain'),
            Arr::get($values, 'name'),
            Arr::get($values, 'user_id')
        );

        $collection = new Collection();
        $collection->fromArray($values);

        return $collection;
    }

    /**
     * @return array|Collection[]
     * @throws \Exception
     */
    public function getAll(): array
    {
        $results = $this->db->get($this->table);

        $collections = [];
        foreach ($results as $result) {
            $collections[] = (new Collection())->fromArray($result);
        }

        return $collections;
    }

    /**
     * @return array|Collection[]
     * @throws \Exception
     */
    public function getAllForChain(string $chain): array
    {
        $results = $this->db
            ->where('chain', $chain)
            ->get($this->table);

        $collections = [];
        foreach ($results as $result) {
            $collections[] = (new Collection())->fromArray($result);
        }

        return $collections;
    }

    public function getCollectionsForUser(User $user): array
    {
        $results = $this->db
            ->where('user_id', $user->id)
            ->get($this->table);

        $collections = [];
        foreach ($results as $result) {
            $collections[] = (new Collection())->fromArray($result);
        }

        return $collections;
    }

    public function getCollectionsForUserByChain(User $user, string $chain): array
    {
        $results = $this->db
            ->where('user_id', $user->id)
            ->where('chain', $chain)
            ->where('active', 1)
            ->get($this->table);

        $collections = [];
        foreach ($results as $result) {
            $collections[] = (new Collection())->fromArray($result);
        }

        return $collections;
    }

    public function getCollectionByChainAndNameAndUser(string $chain, string $name, int $userId): array
    {
        return $this->db
            ->where('chain', $chain)
            ->where('name', $name)
            ->where('user_id', $userId)
            ->getOne($this->table);
    }

    public function doesCollectionExistsOnChainForUser(string $chain, string $name, int $userId): bool
    {
        return (bool) $this->db
            ->where('chain', $chain)
            ->where('name', $name)
            ->where('user_id', $userId)
            ->getOne($this->table);
    }
}
