<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Create;

use App\Common\Exception\Domain\DomainException;
use App\Profile\Application\Betcity\UseCase\Create\Result;
use App\Profile\Domain\Betcity\Exception\BetcityUserIdAlreadyTakenException;
use App\Profile\Entry\Http\Admin\Api\Contract\Betcity\CommonOutputContract;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;
use IWD\SymfonyEntryContract\Dto\Output\ApiFormatter;
use IWD\SymfonyEntryContract\Service\Presenter;
use Symfony\Component\HttpFoundation\Response;

readonly class ResponsePresenter
{
    public function __construct(private Presenter $presenter)
    {
    }

    public function present(
        ?Result $result,
        ?DomainException $exception,
        OutputFormat $outputFormat,
    ): Response {
        if (null !== $result) {
            return $this->presenter->present(
                data: ApiFormatter::prepare(
                    data: CommonOutputContract::create($result->betcity),
                    messages: ['Success']
                ),
                outputFormat: $outputFormat,
                status: Response::HTTP_CREATED,
            );
        }
        if ($exception instanceof BetcityUserIdAlreadyTakenException) {
            return $this->presenter->present(
                data: ApiFormatter::prepare(
                    data: null,
                    messages: ['UserId already taken']
                ),
                outputFormat: $outputFormat,
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        throw new DomainException('Unexpected termination of the script');
    }
}
