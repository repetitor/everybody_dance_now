<?php declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Contracts\ClickHouseRepositoryInterface;
use ClickHouseDB\Client;

class ClickHouseRepository implements ClickHouseRepositoryInterface
{
    public function __construct(
        private Client $client,
    ) {}

    public function testConnection(): string
    {
        try {
            // return $client->select('SELECT version()')->fetchOne('version');
            $version = $this->client->select('SELECT version()')->fetchOne('version');

            return "Connected to ClickHouse! Version: {$version}";
        } catch (\Exception $e) {
            throw new \RuntimeException("ClickHouse connection failed: " . $e->getMessage());
        }
    }

    public function createTestTable(): void
    {
        $this->client->write('
            CREATE TABLE IF NOT EXISTS test_data (
                id UInt32,
                message String,
                created_at DateTime DEFAULT now()
            ) ENGINE = MergeTree()
            ORDER BY created_at
        ');
    }

    public function insertTestData(array $data): void
    {
        $this->client->insert('test_data', $data, ['id', 'message']);
    }

    public function getTestData(): array
    {
        return $this->client->select('SELECT * FROM test_data')->rows();
    }
}