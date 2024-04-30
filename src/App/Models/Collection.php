<?php

namespace App\Models;

use App\Query\CollectionQuery;
use App\Query\UserQuery;
use ArrayHelpers\Arr;
use Carbon\Carbon;

class Collection extends BaseModel
{

    public ?int $id = null;

    public User $user;

    public string $chain;

    public string $name;

    public array $config;

    public ?Carbon $createdAt = null;

    public ?Carbon $updatedAt = null;

    public ?Carbon $deletedAt = null;

    public function initNew(array $values)
    {
        $collection = $this->fromArray($values);

        return $collection->save();
    }

    public function fromArray(array $values): Collection
    {
        $collection = new $this;
        if ($id = Arr::get($values, 'id')) {
            $collection->id = $id;
        }
        $collection->user = (new User())->getById(Arr::get($values, 'user_id'));
        $collection->chain = Arr::get($values, 'chain');
        $collection->name = Arr::get($values, 'name');
        $collection->config = (array) json_decode(Arr::get($values, 'config'), true);
        if ($createdAt = Arr::get($values, 'created_at')) {
            $collection->createdAt = Carbon::parse($createdAt);
        }
        if ($updatedAt = Arr::get($values, 'updated_at')) {
            $collection->updatedAt = Carbon::parse($updatedAt);
        }
        if ($deletedAt = Arr::get($values, 'deleted_at')) {
            $collection->deletedAt = Carbon::parse($deletedAt);
        }

        return $collection;
    }

    public function toArray(): array
    {
        $array = [];

        if ($this->id) {
            $array['id'] = $this->id;
        }
        $array['user_id'] = $this->user->id;
        $array['chain'] = $this->chain;
        $array['name'] = $this->name;
        $array['config'] = json_encode($this->config);
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
     * @throws \Exception
     */
    public function save()
    {
        if ($this->getQueryObject()->doesCollectionExistsOnChainForUser($this->chain, $this->name, $this->user->id)) {
            throw new \Exception('Collection `' . $this->name . '` for chain `' . $this->chain . '` for user `' . $this->user->id . '` exists!');
        }

        return $this->getQueryObject()->createNewCollection($this->toArray($this));
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
        return new CollectionQuery();
    }
}
