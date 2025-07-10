<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\ClickHouseRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestClickHouseController extends Controller
{
    public function __construct(
        private ClickHouseRepository $repository
    ) {}

    public function testConnection(): JsonResponse
    {
        try {
            $version = $this->repository->testConnection();
            return response()->json([
                'status' => 'success',
                'version' => $version,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTestData(): JsonResponse
    {
        try {
            $data = $this->repository->runTestScenario();
            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
