<?php
namespace App\Services\Base;

use App\Action\Actions\Cli\Ethereum\CalcRichLists;
use App\Query\BlockchainTokenQuery;
use App\Query\UserQuery;

class CalcRichListService {

    private const CHAIN = 'base';

    private UserQuery $userQuery;

    private BlockchainTokenQuery $blockchainTokenQuery;

    private array $countsPerWallet = [];

    private array $countsPerWalletBluePrint = [];

    /**
     * @throws \Exception
     */
    public function __construct(readonly private string $project)
    {
        $this->userQuery = new UserQuery();
        $this->blockchainTokenQuery = new BlockchainTokenQuery();
        $this->countsPerWalletBluePrint = $this->createCountsPerWalletBluePrint();
    }

    public function getCountsPerWalletBluePrint(): array
    {
        return $this->countsPerWalletBluePrint;
    }

    /**
     * @throws \Exception
     */
    private function createCountsPerWalletBluePrint(): array
    {
        $array = [];
        $array['total'] = 0;
        $array['collections'] = [];

        $collections = $this->userQuery->getUserByProject($this->project)->getCollectionsForChain(self::CHAIN);
        foreach ($collections as $collection) {
            $contract = $collection->config['$contract'];

            $prepareCollection = [
                'contract' => $contract,
                'total' => 0,
            ];
            $array['collections'][$collection->name] = $prepareCollection;
        }

        return $array;
    }

    /**
     * @throws \Exception
     */
    public function getCountsPerWalletFromCache(): bool|array
    {
        $jsonFile = './data/richlists-cache/' . $this->project . '-' . self::CHAIN . '.json';
        if (!file_exists($jsonFile)) {
            $calcRichLists = new CalcRichLists();
            $calcRichLists->run($this->project);
        }

        $json = file_get_contents($jsonFile);
        if ($json) {
            return (array) json_decode($json, true);
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    public function getCountsPerWallet(): array
    {
        $user = $this->userQuery->getUserByProject($this->project);
        if (!$user) {
            return [];
        }

        foreach ($user->getCollectionsForChain(self::CHAIN) as $collection) {
            $contract = $collection->config['contract'];
            $countResults = $this->blockchainTokenQuery->getResultsPerOwner(
                $this->getTableNFTs($contract),
                'owner_of'
            );

            $countsPerWalletBluePrint = $this->createCountsPerWalletBluePrint();

            foreach ($countResults as $countResult) {
                if (!array_key_exists($countResult['owner_of'], $this->countsPerWallet)) {
                    $this->countsPerWallet[$countResult['owner_of']] = $countsPerWalletBluePrint;
                }
                $this->handleCountForCollectionPerWallet(
                    $countResult['owner_of'],
                    $collection->name,
                    $countResult['total_nfts']
                );
            }
        }

        uasort($this->countsPerWallet, function($a, $b) {
            return $a['total'] < $b['total'];
        });

        foreach ($this->countsPerWallet as $wallet => $data) {
            uasort($this->countsPerWallet[$wallet]['collections'], function($a, $b) {
                return $a['total'] < $b['total'];
            });
        }

        return $this->countsPerWallet;
    }

    private function getTableNFTs(string $contract)
    {
        return self::CHAIN . '_' . $contract . '_nfts';
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
