<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\Blockchain\XRPL\NFTsByIssuerRequest;
use App\Blockchain\XRPL\NFTsByIssuerResponse;
use App\RichList\Config;
use App\Slack;
use GuzzleHttp\Exception\GuzzleException;
use Hardcastle\XRPL_PHP\Client\JsonRpcClient;
use Hardcastle\XRPL_PHP\Models\ErrorResponse;

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

                        if ($response instanceof ErrorResponse) {
                            $this->slack->sendErrorMessage($response->getError());
                            break;
                        }

                        $responseResults = $response->getResult();
                        foreach ($responseResults as $key => $results) {
                            $tableNameNFTs = $this->getTableNFTs($collection['issuer'], $collection['taxon']);
                            if ($key === 'nfts') {
                                foreach ($results as $nftData) {
                                    $this->getBlockchainTokenQuery()->insertNFTdata(
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
                    $this->slack->sendErrorMessage($e->getMessage());
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
                if (!$this->getBlockchainTokenQuery()->hasTable($tableNameNFTs)) {
                    $this->getBlockchainTokenQuery()->createTableNFTs($tableNameNFTs);
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
