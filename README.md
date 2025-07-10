```shell
cd clickhouse-app
cp .env.example .env

docker-compose up -d
composer install
php artisan key:generate
```

