<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Api\Action\Betcity\Remove;

use App\Common\Exception\Domain\DomainException;
use App\Profile\Application\Betcity\UseCase\Remove\Result;
use App\Profile\Domain\Betcity\Exception\BetcityNotFoundException;
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
                status: Response::HTTP_OK,
            );
        }
        if ($exception instanceof BetcityNotFoundException) {
            return $this->presenter->present(
                data: ApiFormatter::prepare(
                    data: null,
                    messages: ['Betcity not found']
                ),
                outputFormat: $outputFormat,
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        throw new DomainException('Unexpected termination of the script');
    }
}
