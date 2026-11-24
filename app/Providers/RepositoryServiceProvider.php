<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Task\Contracts\TaskRepositoryInterface;
use App\Infrastructure\Database\Repositories\EloquentTaskRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Массив bindings — самый чистый способ регистрации интерфейсов в Laravel 11+
     */
    public array $bindings = [
        TaskRepositoryInterface::class => EloquentTaskRepository::class,

        //TODO Когда будет готова реализацию для Elastic, добавить сюда:
        // TaskSearchInterface::class => ElasticTaskSearchRepository::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
