<?php
namespace App\Query;

use Carbon\Carbon;

class BlockchainTokenQuery extends Query
{

    public function hasNFT(string $table, string $nftID): bool
    {
        return (bool) $this->db
            ->where('nft_id', $nftID)
            ->get($table);
    }

    public function insertNFTdata(string $table, array $params): bool
    {
        if ($this->hasNFT($table, $params['nft_id'])) {
            $params['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
            return $this->db
                ->where('nft_id', $params['nft_id'])
                ->update($table, $params);
        } else {
            return $this->db->insert($table, $params);
        }
    }

    public function hasTable(string $tableName): bool
    {
        return $this->db->tableExists([$tableName]);
    }

    public function getResultsPerOwner(string $table): array
    {
        $sql = <<<SQL
SELECT owner, COUNT(*) AS total_nfts FROM {$table} GROUP BY owner;
SQL;

        return $this->db->rawQuery($sql);
    }

    public function getOldestRecord(string $table):? array
    {
        if ($result = $this->db->orderBy('created_at', 'ASC')->get($table, [0,1])) {
            return $result[0];
        }

        return null;
    }

    public function getNewestRecord(string $table):? array
    {
        if ($result = $this->db->orderBy('created_at')->get($table, [0,1])) {
            return $result[0];
        }

        return null;
    }

    public function createTableRichList(string $tableName)
    {
        $sql = <<<SQL
CREATE TABLE {$tableName} (
    id int auto_increment primary key,
    wallet varchar(48) NOT NULL,
    collection_issuer integer(48) NOT NULL,
    collection_taxon integer(6) NOT NULL,
    total_holdings varchar(6) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NULL
);
SQL;

        return $this->db->rawQuery($sql);
    }

    public function createTableNFTs(string $tableName)
    {
        $sql = <<<SQL
CREATE TABLE {$tableName} (
    id int auto_increment primary key,
    nft_id varchar(72) NOT NULL,
    ledger_index integer(11) NOT NULL,
    owner varchar(48) NOT NULL,
    is_burned tinyint(1) NOT NULL,
    uri varchar(255) NOT NULL,
    flags integer(6) NOT NULL,
    transfer_fee integer(8) NOT NULL,
    issuer varchar(48) NOT NULL,
    nft_taxon integer(6) NOT NULL,
    nft_serial integer(6) NOT NULL,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NULL
);
SQL;

        return $this->db->rawQuery($sql);
    }
}
