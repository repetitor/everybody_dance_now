<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ClickHouseService;
use Illuminate\Console\Command;

class TestClickHouse extends Command
{
    protected $signature = 'clickhouse:test';
    protected $description = 'Test ClickHouse connection and basic operations';

    public function handle(ClickHouseService $service): int
    {
        try {
            $version = $service->testConnection();
            $this->info("Connected to ClickHouse! Version: {$version}");

            $data = $service->runTestScenario();

            $this->table(
                ['ID', 'Message', 'Created At'],
                $data
            );

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}