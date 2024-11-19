<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Read;

use App\Profile\Domain\Betcity\Betcity;
use App\Profile\Entry\Http\Admin\Api\Contract\Betcity\CommonOutputContract;
use IWD\SymfonyDoctrineSearch\Service\QueryBus\Aggregate\Bus;
use IWD\SymfonyDoctrineSearch\Service\QueryBus\Aggregate\Query;
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
    public const NAME = 'api_admin_app_profile_betcity_read';

    /**
     * @OA\Tag(name="Profile.Betcity")
     * @OA\Response(
     *     response=200,
     *     description="Read query for Client",
     *     @OA\JsonContent(
     *         allOf={
     *             @OA\Schema(ref=@Model(type=ApiFormatter::class)),
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="data",
     *                     ref=@Model(type=CommonOutputContract::class)
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     example="200"
     *                 )
     *             )
     *         }
     *     )
     *  )
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
        path: '/api/admin/profile/betcities/{id}.{_format}',
        name: self::NAME,
        defaults: ['_format' => 'json'],
        methods: ['GET'],
    )]
    public function action(
        string $id,
        Bus $bus,
        OutputFormat $outputFormat,
        Presenter $presenter,
    ): Response {
        $query = new Query(
            aggregateId: $id,
            targetEntityClass: Betcity::class
        );

        /** @var Betcity $betcity */
        $betcity = $bus->query($query);

        return $presenter->present(
            data: ApiFormatter::prepare(
                CommonOutputContract::create($betcity)
            ),
            outputFormat: $outputFormat
        );
    }
}
