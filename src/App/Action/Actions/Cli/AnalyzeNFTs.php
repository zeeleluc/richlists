<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\RichList\Config;
use App\Slack;
use Carbon\Carbon;

class AnalyzeNFTs extends BaseAction implements CliActionInterface
{
    private Config $config;

    private Slack $slack;

    public function __construct()
    {
        $this->config = new Config();
        $this->slack = new Slack();
    }

    public function run()
    {
        foreach ($this->config->getProjectsIssuerTaxon() as $project => $collections) {
            foreach ($collections as $collection) {
                $tableNameNFTs = $this->getTableNFTs($collection['issuer'], $collection['taxon']);
                $oldestCreatedAt = Carbon::parse($this->getQuery()->getOldestRecord($tableNameNFTs)['created_at']);
                $newestCreatedAt = Carbon::parse($this->getQuery()->getNewestRecord($tableNameNFTs)['created_at']);

                $diffInSeconds = $oldestCreatedAt->diffInSeconds($newestCreatedAt);
                $tenMinutesInSeconds = 60 * 10;

                if ($diffInSeconds > $tenMinutesInSeconds) {
                    $text = 'Diff between oldest and newest record for `' . $collection . '` is more than 10 minutes.';
                    $slack = new Slack();
                    $slack->sendErrorMessage($text);
                }
            }
        }
    }

    private function getTableNFTs(string $issuer, int $taxon = null)
    {
        if ($taxon) {
            return $issuer . '_' . $taxon . '_nfts';
        }

        return $issuer . '_nfts';
    }
}
