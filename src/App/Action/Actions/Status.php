<?php
namespace App\Action\Actions;

use App\Action\Actions\Cli\Traits\UpdateDataNFTTrait;
use App\Action\BaseAction;
use App\Variable;
use Carbon\Carbon;
use function ArrayHelpers\array_has;

class Status extends BaseAction
{

    use UpdateDataNFTTrait;

    public function __construct()
    {
        parent::__construct();

        $this->setLayout('default');
        $this->setView('website/status');

        $query = $this->getBlockchainTokenQuery();

        $statuses = [];
        $collectionsDone = [];
        foreach ($this->getCollectionQuery()->getAll() as $collection) {
            $identifier = $collection->getIdentifier();
            if (!in_array($identifier, $collectionsDone)) {
                $tableName = $collection->getTableName();
                if ($query->hasTable($tableName)) {
                    $lastSyncedRecord = $query->getNewestRecord($tableName)['updated_at'];
                    $statuses[] = [
                        'name' => $collection->name,
                        'identifier' => $identifier,
                        'last_sync_record' => $lastSyncedRecord,
                    ];
                    $collectionsDone[] = $identifier;
                }
            }
        }

        uasort($statuses, function($a, $b) {
            return $a['last_sync_record'] < $b['last_sync_record'];
        });

        $this->setVariable(new Variable('statuses', $statuses));
    }

    public function run()
    {
        parent::run();

        return $this;
    }
}
