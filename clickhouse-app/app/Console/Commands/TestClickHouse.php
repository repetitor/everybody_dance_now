<?php

namespace App\Console\Commands;

use ClickHouseDB\Client;
use Illuminate\Console\Command;

class TestClickHouse extends Command
{
    protected $signature = 'clickhouse:test';
    protected $description = 'Test ClickHouse connection';

    public function handle()
    {
        $client = new Client([
            'host' => 'clickhouse',
            'port' => 8123,
            'username' => 'default',
            'password' => '',
            'settings' => ['connect_timeout' => 10]
        ]);

        try {
            $version = $client->select('SELECT version()')->fetchOne('version');
            $this->info("Connected to ClickHouse! Version: {$version}");
        } catch (\Exception $e) {
            $this->error("Connection failed: " . $e->getMessage());
        }

        $this->testInsert();
    }

    public function testInsert(): void
    {
        $client = new Client([
            'host' => 'clickhouse',
            'port' => 8123,
            'username' => 'default',
            'password' => '',
        ]);

        // Явно указываем структуру таблицы при создании
        $client->write('
        CREATE TABLE IF NOT EXISTS test_data (
            id UInt32,
            message String,
            created_at DateTime DEFAULT now()
        ) ENGINE = MergeTree()
        ORDER BY created_at
    ');

        // Правильный способ вставки данных (с экранированием)
        $client->insert('test_data', [
            ['id' => 1, 'message' => 'Первая запись'],
            ['id' => 2, 'message' => 'Вторая запись'],
        ], ['id', 'message']); // Явно указываем названия колонок

        // Альтернативный вариант с сырым SQL (для сложных случаев)
        $client->write("
        INSERT INTO test_data (id, message) VALUES
        (1, 'Первая запись'),
        (2, 'Вторая запись')
    ");

        // Получаем данные
        $results = $client->select('SELECT * FROM test_data');

        $this->table(
            ['ID', 'Message', 'Created At'],
            $results->rows()
        );
    }
}