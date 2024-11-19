<?php

declare(strict_types=1);

namespace App\Common\Interface;

/**
 * @description Вы можете добавить этот интерфейс на любую сущность, и сгенерировать необходимы текст для хлебных крошек.
 */
interface CustomBreadcrumbsSyliusLabelInterface
{
    public function breadCrumbsLabel(): string;
}
