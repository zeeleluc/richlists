<?php

namespace App\Blockchain\XRPL;

use Hardcastle\XRPL_PHP\Models\BaseRequest;

class NFTsByIssuerRequest extends BaseRequest
{
    protected string $command = "nfts_by_issuer";

    public function __construct(
        protected string $issuer,
        protected ?int $nft_taxon = null,
        protected ?string $marker = null,
        protected ?int $limit = null,
    ) {}
}
