<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\RichList\Config;
use App\XRPL\NFTsByIssuerRequest;
use App\XRPL\NFTsByIssuerResponse;
use GuzzleHttp\Exception\GuzzleException;
use Hardcastle\XRPL_PHP\Client\JsonRpcClient;

class UpdateDataNFT extends BaseAction implements CliActionInterface
{
    private Config $config;

    private JsonRpcClient $client;

    public function __construct()
    {
        $this->config = new Config();
        $this->client = new JsonRpcClient(env('CLIO_SERVER'));
    }

    public function run()
    {
        $this->handleTables();
        $this->updateNFTdata();
    }

    public function updateNFTdata(): void
    {
        foreach ($this->config->getProjectsIssuerTaxon() as $project => $collections) {
            foreach ($collections as $collection) {
                try {
                    $request = new NFTsByIssuerRequest(
                        issuer: $collection['issuer'],
                        nft_taxon: $collection['nft_taxon']
                    );
                    $response = $this->client->syncRequest($request); /* @var $response NFTsByIssuerResponse */
                    foreach ($response->getResult() as $index => $result) {
                        $tableNameNFTs = $this->getTableNFTs($project, $collection['issuer'], $collection['taxon']);
                        $this->getQuery()->insertNFTdata(
                            $tableNameNFTs,
                            $result
                        );
                    }

                } catch (GuzzleException $e) {
                    var_dump($e->getMessage());
                }
            }
        }
    }

    private function handleTables()
    {
        foreach ($this->config->getProjectsIssuerTaxon() as $project => $collections) {
            $tableNameRichList = $this->getTableNameRichList($project);
            if (!$this->getQuery()->hasTable($tableNameRichList)) {
                $this->getQuery()->createTableRichList($tableNameRichList);
            }

            foreach ($collections as $collection) {
                $tableNameNFTs = $this->getTableNFTs($project, $collection['issuer'], $collection['taxon']);
                if (!$this->getQuery()->hasTable($tableNameNFTs)) {
                    $this->getQuery()->createTableNFTs($tableNameNFTs);
                }
            }
        }
    }

    private function getTableNameRichList(string $project)
    {
        return $project . '_rich_list';
    }

    private function getTableNFTs(string $project, string $issuer, string $taxon)
    {
        return $project . '_' . $issuer . '_' . $taxon . '_nfts';
    }
}