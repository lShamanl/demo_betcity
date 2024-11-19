<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Search;

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
     *         "id": {"eq": "88263"},
     *         "createdAt": {"range": "1987-04-25 07:46:55,1987-04-29 07:46:55"},
     *         "updatedAt": {"range": "1993-01-13 22:13:45,1993-01-31 22:13:45"},
     *         "email": {"eq": "foo"},
     *         "userRoles": {"like": "thud"},
     *         "password": {"eq": "bar"},
     *         "name": {"like": "fred"}
     *     }
     * )
     */
    public Filters $filters;
}
