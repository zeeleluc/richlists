<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\Query\CollectionQuery;
use App\Slack;
use Carbon\Carbon;

class AnalyzeNFTs extends BaseAction implements CliActionInterface
{
    private CollectionQuery $collectionQuery;

    private Slack $slack;

    public function __construct()
    {
        $this->collectionQuery = new CollectionQuery();
        $this->slack = new Slack();
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        foreach ($this->collectionQuery->getAllForChain('xrpl') as $collection) {
            $issuer = $collection->config['issuer'];
            $taxon = $collection->config['taxon'] ?? null;

            $tableNameNFTs = $this->getTableNFTs($issuer, $taxon);
            $oldestCreatedAt = Carbon::parse($this->getBlockchainTokenQuery()->getOldestRecord($tableNameNFTs)['created_at']);
            $newestCreatedAt = Carbon::parse($this->getBlockchainTokenQuery()->getNewestRecord($tableNameNFTs)['created_at']);

            $diffInSeconds = $oldestCreatedAt->diffInSeconds($newestCreatedAt);
            $tenMinutesInSeconds = 60 * 10;

            if ($diffInSeconds > $tenMinutesInSeconds) {
                $text = 'Diff between oldest and newest record for `' . $collection->name . '` is more than 10 minutes.';
                $slack = new Slack();
                $slack->sendErrorMessage($text);
            }
        }
    }

    private function getTableNFTs(string $issuer, int $taxon = null)
    {
        if ($taxon) {
            return 'xrpl_' . $issuer . '_' . $taxon . '_nfts';
        }

        return 'xrpl_' . $issuer . '_nfts';
    }
}
