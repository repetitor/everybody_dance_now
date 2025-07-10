<?php declare(strict_types=1);

namespace App\Providers;

use App\Repositories\ClickHouseRepository;
use App\Repositories\Contracts\ClickHouseRepositoryInterface;
use ClickHouseDB\Client;
use Illuminate\Support\ServiceProvider;

class ClickHouseServiceProvider extends ServiceProvider
{
//    public function register(): void
//    {
//        $this->app->singleton(Client::class, function ($app) {
//            return new Client([
//                'host' => config('database.connections.clickhouse.host'),
//                'port' => config('database.connections.clickhouse.port'),
//                'username' => config('database.connections.clickhouse.username'),
//                'password' => config('database.connections.clickhouse.password'),
//                'settings' => ['connect_timeout' => 10]
//            ]);
//        });
//    }

//    use App\Repositories\ClickHouseRepository;
//    use App\Repositories\Contracts\ClickHouseRepositoryInterface;
//    use ClickHouseDB\Client;

    public function register(): void
    {
        // Bind the interface to implementation
        $this->app->bind(
            ClickHouseRepositoryInterface::class,
            ClickHouseRepository::class
        );

        // Client singleton (if needed)
        $this->app->singleton(Client::class, fn () => new Client([
            'host' => config('database.connections.clickhouse.host'),
            'port' => config('database.connections.clickhouse.port'),
            'username' => config('database.connections.clickhouse.username'),
            'password' => config('database.connections.clickhouse.password'),
        ]));
    }
}