<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Api\Action\User\Remove;

use App\Auth\Application\User\UseCase\Remove\Result;
use App\Auth\Entry\Http\Admin\Api\Contract\User\CommonOutputContract;
use DomainException;
use IWD\SymfonyEntryContract\Dto\Input\OutputFormat;
use IWD\SymfonyEntryContract\Dto\Output\ApiFormatter;
use IWD\SymfonyEntryContract\Service\Presenter;
use Symfony\Component\HttpFoundation\Response;

class ResponsePresenter
{
    public function __construct(private readonly Presenter $presenter)
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
        if ($result->isUserNotExists()) {
            return $this->presenter->present(
                data: ApiFormatter::prepare(
                    data: null !== $result->user ? CommonOutputContract::create($result->user) : null,
                    messages: ['user not exists']
                ),
                outputFormat: $outputFormat,
                status: Response::HTTP_BAD_REQUEST,
            );
        }

        throw new DomainException('Unexpected termination of the script');
    }
}
