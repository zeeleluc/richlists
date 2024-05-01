<?php
namespace App\Action\Actions\Cli\XRPL;

use App\Action\Actions\Cli\CliActionInterface;
use App\Action\BaseAction;
use App\Blockchain\XRPL\NFTsByIssuerRequest;
use App\Blockchain\XRPL\NFTsByIssuerResponse;
use App\Slack;
use GuzzleHttp\Exception\GuzzleException;
use Hardcastle\XRPL_PHP\Client\JsonRpcClient;
use Hardcastle\XRPL_PHP\Models\ErrorResponse;

class UpdateDataNFT extends BaseAction implements CliActionInterface
{
    private const CHAIN = 'xrpl';

    private JsonRpcClient $client;

    private Slack $slack;

    public function __construct()
    {
        $this->client = new JsonRpcClient(env('CLIO_SERVER'));
        $this->slack = new Slack();
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        $this->handleTables();
        $this->updateNFTdata();
    }

    /**
     * @throws \Exception
     */
    public function updateNFTdata(): void
    {
        $collectionsDoneByIssuerTaxon = [];

        foreach ($this->getCollectionQuery()->getAllForChain(self::CHAIN) as $collection) {
            $this->slack->sendInfoMessage('Starting with ' . $collection->name . '...');

            $issuer = $collection->config['issuer'];
            $taxon = $collection->config['taxon'] ?? null;

            $configIdentifier = $issuer;
            if ($taxon) {
                $configIdentifier .= $taxon;
            }

            if (in_array($configIdentifier, $collectionsDoneByIssuerTaxon)) {
                $this->slack->sendInfoMessage('Skipping! ' . $collection->name . ' already done in this loop.');
                continue;
            }
            try {
                $marker = null;
                do {
                    $request = new NFTsByIssuerRequest(
                        issuer: $issuer,
                        nft_taxon: $taxon,
                        marker: $marker
                    );
                    $response = $this->client->syncRequest($request); /* @var $response NFTsByIssuerResponse */

                    if ($response instanceof ErrorResponse) {
                        $this->slack->sendErrorMessage($response->getError());
                        break;
                    }

                    $responseResults = $response->getResult();
                    foreach ($responseResults as $key => $results) {
                        $tableNameNFTs = $this->getTableNFTs($issuer, $taxon);
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

            $collectionsDoneByIssuerTaxon[] = $configIdentifier;
        }
    }

    private function handleTables()
    {
        foreach ($this->getCollectionQuery()->getAll() as $collection) {
            $issuer = $collection->config['issuer'];
            $taxon = $collection->config['taxon'] ?? null;

            $tableNameNFTs = $this->getTableNFTs($issuer, $taxon);
            if (!$this->getBlockchainTokenQuery()->hasTable($tableNameNFTs)) {
                $this->getBlockchainTokenQuery()->createTableNFTs($tableNameNFTs);
            }
        }
    }

    private function getTableNFTs(string $issuer, int $taxon = null)
    {
        if ($taxon) {
            return self::CHAIN . '_' . $issuer . '_' . $taxon . '_nfts';
        }

        return self::CHAIN . '_' . $issuer . '_nfts';
    }
}
