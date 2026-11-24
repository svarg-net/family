<?php

declare(strict_types=1);

namespace App\Infrastructure\Elasticsearch\Repositories;

use App\Application\Task\DTOs\TaskSearchCriteria;
use App\Domain\Task\Contracts\TaskSearchInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ElasticTaskSearchRepository implements TaskSearchInterface
{
    public function search(TaskSearchCriteria $criteria): LengthAwarePaginator
    {
        // Здесь код работы с ElasticSearch PHP Client
        // $response = $this->client->search([ ... ]);

        // Маппинг ответа Elastic в коллекцию моделей Laravel или DTO
         return new LengthAwarePaginator();
    }
}
