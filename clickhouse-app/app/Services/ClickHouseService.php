<?php declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ClickHouseRepositoryInterface;

class ClickHouseService
{
    public function __construct(
        private ClickHouseRepositoryInterface $repository,
    ) {}

    public function testConnection(): string
    {
        return $this->repository->testConnection();
    }

    public function runTestScenario(): array
    {
        $this->repository->createTestTable();
        $this->repository->insertTestData([
            ['id' => 1, 'message' => 'Первая запись'],
            ['id' => 2, 'message' => 'Вторая запись'],
        ]);

        return $this->repository->getTestData();
    }
}