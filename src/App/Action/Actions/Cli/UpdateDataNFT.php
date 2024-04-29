<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\RichList\Config;
use App\Slack;
use App\XRPL\NFTsByIssuerRequest;
use App\XRPL\NFTsByIssuerResponse;
use GuzzleHttp\Exception\GuzzleException;
use Hardcastle\XRPL_PHP\Client\JsonRpcClient;

class UpdateDataNFT extends BaseAction implements CliActionInterface
{
    private Config $config;

    private JsonRpcClient $client;

    private Slack $slack;

    public function __construct()
    {
        $this->config = new Config();
        $this->client = new JsonRpcClient(env('CLIO_SERVER'));
        $this->slack = new Slack();
    }

    public function run()
    {
        $this->handleTables();
        $this->updateNFTdata();
    }

    public function updateNFTdata(): void
    {
        $collectionsDoneByIssuerTaxon = [];

        foreach ($this->config->getProjectsIssuerTaxon() as $project => $collections) {
            foreach ($collections as $collection) {
                $this->slack->sendInfoMessage('Starting with ' . $collection['name'] . '...');
                if (in_array($collection['issuer'] . '-' . $collection['taxon'], $collectionsDoneByIssuerTaxon)) {
                    $this->slack->sendInfoMessage('Skipping! ' . $collection['name'] . ' already done in this loop.');
                    continue;
                }
                try {
                    $marker = null;
                    do {
                        $request = new NFTsByIssuerRequest(
                            issuer: $collection['issuer'],
                            nft_taxon: $collection['taxon'],
                            marker: $marker
                        );
                        $response = $this->client->syncRequest($request); /* @var $response NFTsByIssuerResponse */
                        $responseResults = $response->getResult();
                        if ($errorMessage = $response->getError()) {
                            $this->slack->sendErrorMessage($errorMessage);
                        }
                        $responseAsJson = json_encode($responseResults);
                        $this->slack->sendInfoMessage(substr($responseAsJson, 0, 100) . '....');
                        foreach ($responseResults as $key => $results) {
                            $tableNameNFTs = $this->getTableNFTs($collection['issuer'], $collection['taxon']);
                            if ($key === 'nfts') {
                                foreach ($results as $nftData) {
                                    $this->getQuery()->insertNFTdata(
                                        $tableNameNFTs,
                                        $nftData
                                    );
                                }
                            }
                        }

                        $marker = array_key_exists('marker', $responseResults) ?
                            $responseResults['marker'] :
                            null;

                    } while(is_string($marker));

                } catch (GuzzleException $e) {
                    var_dump($e->getMessage());
                }

                $collectionsDoneByIssuerTaxon[] = $collection['issuer'] . '-' . $collection['taxon'];
            }
        }
    }

    private function handleTables()
    {
        foreach ($this->config->getProjectsIssuerTaxon() as $project => $collections) {
            foreach ($collections as $collection) {
                $tableNameNFTs = $this->getTableNFTs($collection['issuer'], $collection['taxon']);
                if (!$this->getQuery()->hasTable($tableNameNFTs)) {
                    $this->getQuery()->createTableNFTs($tableNameNFTs);
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
