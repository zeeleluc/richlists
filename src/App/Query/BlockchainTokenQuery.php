<?php
namespace App\Query;

use Carbon\Carbon;

class BlockchainTokenQuery extends Query
{

    public function getNFTs(string $table)
    {
        return $this->db
            ->get($table);
    }

    public function hasNFT(string $table, string $column, string $value): bool
    {
        return (bool) $this->db
            ->where($column, $value)
            ->get($table);
    }

    public function insertNFTdata(string $table, array $params, string $whereColumn, string $whereValue): bool
    {
        if ($this->hasNFT($table, $whereColumn, $whereValue)) {
            $params['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
            return $this->db
                ->where($whereColumn, $whereValue)
                ->update($table, $params);
        } else {
            return $this->db->insert($table, $params);
        }
    }

    public function hasTable(string $tableName): bool
    {
        return $this->db->tableExists([$tableName]);
    }

    public function getResultsPerOwner(string $table, string $groupByColumn): array
    {
        $sql = <<<SQL
SELECT {$groupByColumn}, COUNT(*) AS total_nfts FROM {$table} GROUP BY {$groupByColumn};
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

    public function createTableNFTsXRPL(string $tableName)
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

    public function createTableNFTsEthereum(string $tableName)
    {
        $sql = <<<SQL
CREATE TABLE {$tableName} (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amount INT,
    token_id INT,
    token_address VARCHAR(42),
    contract_type VARCHAR(10),
    owner_of VARCHAR(42),
    last_metadata_sync VARCHAR(24),
    last_token_uri_sync VARCHAR(24),
    metadata TEXT,
    block_number INT,
    block_number_minted INT,
    name VARCHAR(255),
    symbol VARCHAR(10),
    token_hash VARCHAR(32),
    token_uri VARCHAR(255),
    minter_address VARCHAR(42),
    rarity_rank VARCHAR(255),
    rarity_percentage VARCHAR(255),
    rarity_label VARCHAR(255),
    verified_collection BOOLEAN,
    possible_spam BOOLEAN,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

        return $this->db->rawQuery($sql);
    }

    public function createTableNFTsBase(string $tableName)
    {
        $sql = <<<SQL
CREATE TABLE {$tableName} (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amount INT,
    token_id INT,
    token_address VARCHAR(42),
    contract_type VARCHAR(10),
    owner_of VARCHAR(42),
    last_metadata_sync VARCHAR(24),
    last_token_uri_sync VARCHAR(24),
    metadata TEXT,
    block_number INT,
    block_number_minted INT,
    name VARCHAR(255),
    symbol VARCHAR(10),
    token_hash VARCHAR(32),
    token_uri VARCHAR(255),
    minter_address VARCHAR(42),
    rarity_rank VARCHAR(255),
    rarity_percentage VARCHAR(255),
    rarity_label VARCHAR(255),
    verified_collection BOOLEAN,
    possible_spam BOOLEAN,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
SQL;

        return $this->db->rawQuery($sql);
    }
}
