<?php
namespace App\Action\Actions\Cli;

use App\Action\BaseAction;
use App\Query\MigrationQuery;

class Migrate extends BaseAction implements CliActionInterface
{

    private MigrationQuery $migrationQuery;

    public function __construct()
    {
        $this->migrationQuery = new MigrationQuery();
    }

    public function run()
    {
        $identifiersDone = $this->migrationQuery->getAllIdentifiers();
        $migrations = $this->getMigrationFiles($identifiersDone);

        if (!$migrations) {
            echo 'No migrations found.' . PHP_EOL;
            exit;
        }

        foreach ($migrations as $migration) {
            $sql = file_get_contents(ROOT . '/migrations/' . $migration . '.sql');
            $this->migrationQuery->executeMigration($sql, $migration);
        }
    }

    private function getMigrationFiles(array $excludeMigrations = []): array
    {
        $migrationsToDo = [];

        $migrationFiles = glob(ROOT . '/migrations/*.sql');
        foreach ($migrationFiles as $index => $migrationFile) {
            $pathInfo = pathinfo($migrationFile);
            $identifier = $pathInfo['filename'];

            if (!in_array($identifier, $excludeMigrations)) {
                $migrationsToDo[] = $identifier;
            }
        }

        return $migrationsToDo;
    }
}
