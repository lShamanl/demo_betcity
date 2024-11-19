<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Edit;

use App\Common\Exception\Domain\DomainException;
use App\Profile\Application\Betcity\UseCase\Edit\Handler;
use App\Profile\Domain\Betcity\Betcity;
use App\Profile\Entry\Http\Admin\Api\Contract\Betcity\CommonOutputContract;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;
use IWD\SymfonyEntryContract\Dto\Output\ApiFormatter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Action
{
    public const NAME = 'api_admin_app_profile_betcity_edit';

    /**
     * @OA\Tag(name="Profile.Betcity")
     * @OA\Post(
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 ref=@Model(type=InputContract::class)
     *             )
     *         )
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Update command for Client",
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
        path: '/api/admin/profile/betcities/edit.{_format}',
        name: self::NAME,
        defaults: ['_format' => 'json'],
        methods: ['POST'],
    )]
    public function action(
        InputContract $contract,
        CommandFactory $commandFactory,
        Handler $handler,
        OutputFormat $outputFormat,
        ResponsePresenter $responsePresenter,
        MetricSender $metricSender,
    ): Response {
        $command = $commandFactory->create($contract);
        try {
            $result = $handler->handle($command);
        } catch (DomainException $domainException) {
        } finally {
            $result ??= null;
            $domainException ??= null;
        }

        $metricSender->send($result, $domainException);

        return $responsePresenter->present($result, $domainException, $outputFormat);
    }
}
