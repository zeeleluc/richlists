<?php
namespace App;

use App\Object\BaseObject;

class Query extends BaseObject
{
    private function db(): \MysqliDb
    {
        return new \MysqliDb(
            env('DB_HOST'),
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_DBNAME')
        );
    }

    public function insertNFTdata(string $table, array $params): bool
    {
        return $this->db()->insert($table, $params);
    }

    public function hasTable(string $tableName): bool
    {
        return $this->db()->tableExists([$tableName]);
    }

    public function createTableRichList(string $tableName)
    {
        // @todo
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

        return $this->db()->rawQuery($sql);
    }
}
