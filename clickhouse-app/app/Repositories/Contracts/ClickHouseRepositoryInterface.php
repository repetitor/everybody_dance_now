<?php declare(strict_types=1);

namespace App\Repositories\Contracts;

interface ClickHouseRepositoryInterface
{
    public function testConnection(): string;

    public function createTestTable(): void;

    public function insertTestData(array $data): void;

    public function getTestData(): array;
}