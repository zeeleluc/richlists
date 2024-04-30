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

    public function getCollectionsForUser(User $user): array
    {
        return $this->db
            ->where('user_id', $user->id)
            ->get($this->table);
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
