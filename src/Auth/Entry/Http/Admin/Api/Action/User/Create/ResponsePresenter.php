<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Create;

use App\Auth\Application\User\UseCase\Create\Result;
use App\Auth\Entry\Http\Client\Api\Contract\User\CommonOutputContract;
use DomainException;
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
        Result $result,
        OutputFormat $outputFormat,
    ): Response {
        if ($result->isSuccess()) {
            return $this->presenter->present(
                data: ApiFormatter::prepare(
                    data: null !== $result->user ? CommonOutputContract::create($result->user) : null,
                    messages: ['success']
                ),
                outputFormat: $outputFormat,
                status: Response::HTTP_OK,
            );
        }
        if ($result->isEmailIsBusy()) {
            return $this->presenter->present(
                data: ApiFormatter::prepare(
                    data: null !== $result->user ? CommonOutputContract::create($result->user) : null,
                    messages: ['email is busy']
                ),
                outputFormat: $outputFormat,
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        throw new DomainException('Unexpected termination of the script');
    }
}
