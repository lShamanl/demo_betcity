<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Search;

use App\Profile\Domain\Betcity\Betcity;
use App\Profile\Entry\Http\Admin\Api\Contract\Betcity\CommonOutputContract;
use IWD\SymfonyDoctrineSearch\Dto\Input\SearchQuery;
use IWD\SymfonyDoctrineSearch\Dto\Output\OutputPagination;
use IWD\SymfonyDoctrineSearch\Service\QueryBus\Search\Bus;
use IWD\SymfonyDoctrineSearch\Service\QueryBus\Search\Query;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;
use IWD\SymfonyEntryContract\Dto\Output\ApiFormatter;
use IWD\SymfonyEntryContract\Service\Presenter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Action
{
    public const NAME = 'api_admin_app_profile_betcity_search';

    /**
     * @OA\Tag(name="Profile.Betcity")
     * @OA\Get(
     *     @OA\Parameter(
     *          name="searchQuery",
     *          in="query",
     *          required=false,
     *          @OA\Schema(
     *              ref=@Model(type=QueryParams::class)
     *          ),
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Search query for Client",
     *     @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref=@Model(type=ApiFormatter::class)),
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                      @OA\Property(
     *                          property="data",
     *                          ref=@Model(type=CommonOutputContract::class),
     *                          type="object"
     *                      ),
     *                      @OA\Property(
     *                          property="pagination",
     *                          ref=@Model(type=OutputPagination::class),
     *                          type="object"
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="status",
     *                      example="200"
     *                 )
     *             )
     *         }
     *     )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Bad Request"
     * ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     * @OA\Response(
     *     response=403,
     *     description="Forbidden"
     * ),
     * @OA\Response(
     *     response=404,
     *     description="Resource Not Found"
     * )
     * @Security(name="Bearer")
     */
    #[Route(
        path: '/api/admin/profile/betcities.{_format}',
        name: self::NAME,
        defaults: ['_format' => 'json'],
        methods: ['GET'],
    )]
    public function action(
        SearchQuery $searchQuery,
        Bus $bus,
        OutputFormat $outputFormat,
        Presenter $presenter,
    ): Response {
        $query = new Query(
            targetEntityClass: Betcity::class,
            pagination: $searchQuery->pagination,
            filters: $searchQuery->filters,
            sorts: $searchQuery->sorts
        );

        $searchResult = $bus->query($query);

        return $presenter->present(
            data: ApiFormatter::prepare([
                'data' => array_map(static function (Betcity $betcity) {
                    return CommonOutputContract::create($betcity);
                }, $searchResult->entities),
                'pagination' => $searchResult->pagination,
            ]),
            outputFormat: $outputFormat
        );
    }
}
