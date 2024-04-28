<?php
namespace App\RichList;

use App\Query;

class Service {

    private array $config;

    private Query $query;

    private array $countsPerWallet = [];

    private array $countsPerWalletBluePrint = [];

    public function __construct(readonly private string $project)
    {
        $this->config = (new Config())->getProjectsIssuerTaxon();
        $this->query = new Query();
        $this->countsPerWalletBluePrint = $this->createCountsPerWalletBluePrint();
    }

    public function getCountsPerWalletBluePrint(): array
    {
        return $this->countsPerWalletBluePrint;
    }

    private function createCountsPerWalletBluePrint(): array
    {
        $array = [];
        $array['total'] = 0;
        $array['collections'] = [];

        foreach ($this->config[$this->project] as $collection) {
            $prepareCollection = [
                'issuer' => $collection['issuer'],
                'taxon' => $collection['taxon'],
                'total' => 0,
            ];
            $array['collections'][$collection['name']] = $prepareCollection;
        }

        return $array;
    }

    public function getCountsPerWallet(): array
    {
        if (!array_key_exists($this->project, $this->config)) {
            return [];
        }

        foreach ($this->config[$this->project] as $collection) {
            $countResults = $this->query->getResultsPerOwner(
                $this->getTableNFTs(
                    $this->project,
                    $collection['issuer'],
                    $collection['taxon']
                )
            );

            foreach ($countResults as $countResult) {
                if (!array_key_exists($countResult['owner'], $this->countsPerWallet)) {
                    $this->countsPerWallet[$countResult['owner']] = $this->createCountsPerWalletBluePrint();
                }
                $this->handleCountForCollectionPerWallet(
                    $countResult['owner'],
                    $collection['name'],
                    $countResult['total_nfts']
                );
            }
        }

        uasort($this->countsPerWallet, function($a, $b) {
            return $a['total'] < $b['total'];
        });

        return $this->countsPerWallet;
    }

    private function getTableNFTs(string $project, string $issuer, string $taxon)
    {
        return $project . '_' . $issuer . '_' . $taxon . '_nfts';
    }

    private function handleCountForCollectionPerWallet(
        string $wallet,
        string $name,
        int $total
    ): void {
        $this->countsPerWallet[$wallet]['total'] += $total;
        $this->countsPerWallet[$wallet]['collections'][$name]['total'] = $total;
    }
}
