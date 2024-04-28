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
                    $marker = null;
                    do {
                        $request = new NFTsByIssuerRequest(
                            issuer: $collection['issuer'],
                            nft_taxon: $collection['taxon'],
                            marker: $marker
                        );
                        $response = $this->client->syncRequest($request); /* @var $response NFTsByIssuerResponse */
                        $responseResults = $response->getResult();
                        foreach ($responseResults as $key => $results) {
                            $tableNameNFTs = $this->getTableNFTs($project, $collection['issuer'], $collection['taxon']);
                            /**
                             * @todo throw Exception and send Slack message if no nfts found, or error returned from Clio
                             */
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
            }
        }
    }

    private function handleTables()
    {
        foreach ($this->config->getProjectsIssuerTaxon() as $project => $collections) {
            foreach ($collections as $collection) {
                $tableNameNFTs = $this->getTableNFTs($project, $collection['issuer'], $collection['taxon']);
                if (!$this->getQuery()->hasTable($tableNameNFTs)) {
                    $this->getQuery()->createTableNFTs($tableNameNFTs);
                }
            }
        }
    }

    private function getTableNFTs(string $project, string $issuer, string $taxon)
    {
        return $project . '_' . $issuer . '_' . $taxon . '_nfts';
    }
}
