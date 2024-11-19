<?php

declare(strict_types=1);

namespace App\Profile\Entry\Http\Admin\Sylius\Controller\Betcity;

use App\Profile\Application\Betcity\UseCase\Create\Command as CreateCommand;
use App\Profile\Application\Betcity\UseCase\Create\Handler as CreateHandler;
use App\Profile\Application\Betcity\UseCase\Edit\Command as EditCommand;
use App\Profile\Application\Betcity\UseCase\Edit\Handler as EditHandler;
use App\Profile\Application\Betcity\UseCase\Remove\Command as DeleteCommand;
use App\Profile\Application\Betcity\UseCase\Remove\Handler as DeleteHandler;
use App\Profile\Domain\Betcity\Betcity;
use App\Profile\Domain\Betcity\Enum\Gender;
use App\Profile\Domain\Betcity\Exception\BetcityNotFoundException;
use App\Profile\Domain\Betcity\Exception\BetcityUserIdAlreadyTakenException;
use App\Profile\Domain\Betcity\ValueObject\BetcityId;
use App\Profile\Entry\Http\Admin\Sylius\Controller\Betcity\Form\CreateType;
use App\Profile\Entry\Http\Admin\Sylius\Controller\Betcity\Form\EditType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: 'profile/betcities',
    name: 'app_profile.betcity_',
)]
class BetcityController extends ResourceController
{
    #[Route(
        path: '/create/new',
        name: 'create',
        methods: ['GET', 'POST'],
    )]
    public function create(
        Request $request,
        CreateHandler $handler,
        TranslatorInterface $translator,
    ): Response {
        $formData = null;

        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $form = $this->createForm(CreateType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payload = $form->getData();
            /** @var Gender $gender */
            $gender = $payload['gender'];
            try {
                $result = $handler->handle(
                    new CreateCommand(
                        userId: (int) $payload['userId'],
                        name: $payload['name'],
                        gender: $gender,
                    )
                );
            } catch (BetcityUserIdAlreadyTakenException $exception) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.profile.betcity.flash.user_id_already_taken'));
            }
            if (isset($result)) {
                $this->addFlash('success', $translator->trans('app.admin.ui.modules.profile.betcity.flash.success'));

                return $this->redirectToRoute('app_profile.betcity_show', ['id' => $result->betcity->getId()->getValue()]);
            }
        }

        return $this->render(
            '@profile/admin/betcity/create.html.twig',
            [
                'metadata' => $this->metadata,
                'form' => $form->createView(),
                'resource' => $form->getData(),
                'configuration' => $configuration,
            ]
        );
    }

    #[Route(
        path: '/{id}/edit',
        name: 'update',
        methods: ['GET', 'POST'],
    )]
    public function update(
        Request $request,
        EditHandler $handler,
        TranslatorInterface $translator,
    ): Response {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var Betcity $betcity */
        $betcity = $this->findOr404($configuration);
        if (Request::METHOD_GET === $request->getMethod()) {
            $form = $this->createForm(EditType::class, $betcity);
        } else {
            $form = $this->createForm(EditType::class);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $payload = $form->getData();
            /** @var Gender $gender */
            $gender = $payload['gender'];
            try {
                $result = $handler->handle(
                    new EditCommand(
                        id: $betcity->getId(),
                        userId: (int) $payload['userId'],
                        name: $payload['name'],
                        gender: $gender,
                    )
                );
            } catch (BetcityNotFoundException $exception) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.profile.betcity.flash.betcity_not_found'));
            } catch (BetcityUserIdAlreadyTakenException $exception) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.profile.betcity.flash.user_id_already_taken'));
            }
            if (isset($result)) {
                $this->addFlash('success', $translator->trans('app.admin.ui.modules.profile.betcity.flash.success'));

                return $this->redirectToRoute('app_profile.betcity_show', ['id' => $result->betcity->getId()->getValue()]);
            }
        }

        return $this->render(
            '@profile/admin/betcity/update.html.twig',
            [
                'metadata' => $this->metadata,
                'form' => $form->createView(),
                'resource' => $form->getData(),
                'configuration' => $configuration,
            ]
        );
    }

    #[Route(
        path: '/{id}/delete',
        name: 'delete',
        methods: ['POST'],
    )]
    public function delete(
        string $id,
        DeleteHandler $handler,
        TranslatorInterface $translator,
    ): Response {
        try {
            $result = $handler->handle(
                new DeleteCommand(
                    id: new BetcityId($id)
                )
            );
        } catch (BetcityNotFoundException $exception) {
            $this->addFlash('error', $translator->trans('app.admin.ui.modules.profile.betcity.flash.betcity_not_found'));
        }
        if (isset($result)) {
            $this->addFlash('success', $translator->trans('app.admin.ui.modules.profile.betcity.flash.success'));
        }

        return $this->redirectToRoute('app_profile.betcity_index');
    }
}
