<?php
namespace App;

use App\Object\BaseObject;
use Carbon\Carbon;

class Query extends BaseObject
{
    private \MysqliDb $db;

    public function __construct()
    {
        $this->db = $this->db();
    }

    private function db(): \MysqliDb
    {
        return new \MysqliDb(
            env('DB_HOST'),
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DBNAME')
        );
    }

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
    uri varchar(180) NOT NULL,
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
