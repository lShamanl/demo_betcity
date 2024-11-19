<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Search;

use IWD\SymfonyDoctrineSearch\Dto\Input\Filters;
use IWD\SymfonyDoctrineSearch\Dto\Input\SearchQuery;
use OpenApi\Annotations as OA;

/** @codeCoverageIgnore */
class QueryParams extends SearchQuery
{
    /**
     * @OA\Property(
     *     property="filter",
     *     type="object",
     *     example={
     *         "id": {"eq": "100"},
     *         "createdAt": {"range": "2023-01-01 00:00:00,2024-01-01 00:00:00"},
     *         "updatedAt": {"range": "2023-01-01 00:00:00,2024-01-01 00:00:00"},
     *         "userId": {"range": "1,100"},
     *         "name": {"eq": "foo"},
     *         "gender": {"eq": "secret"}
     *     }
     * )
     */
    public Filters $filters;
}
