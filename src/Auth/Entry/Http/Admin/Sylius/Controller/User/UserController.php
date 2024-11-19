<?php

declare(strict_types=1);

namespace App\Auth\Entry\Http\Admin\Sylius\Controller\User;

use App\Auth\Application\User\UseCase\ChangePassword\Command as ChangePasswordCommand;
use App\Auth\Application\User\UseCase\ChangePassword\Handler as ChangePasswordHandler;
use App\Auth\Application\User\UseCase\Create\Command as CreateCommand;
use App\Auth\Application\User\UseCase\Create\Handler as CreateHandler;
use App\Auth\Application\User\UseCase\Edit\Command as EditCommand;
use App\Auth\Application\User\UseCase\Edit\Handler as EditHandler;
use App\Auth\Application\User\UseCase\Remove\Command as RemoveCommand;
use App\Auth\Application\User\UseCase\Remove\Handler as RemoveHandler;
use App\Auth\Domain\User\User;
use App\Auth\Domain\User\ValueObject\UserId;
use App\Auth\Entry\Http\Admin\Sylius\Controller\User\Form\ChangePasswordType;
use App\Auth\Entry\Http\Admin\Sylius\Controller\User\Form\CreateType;
use App\Auth\Entry\Http\Admin\Sylius\Controller\User\Form\EditType;
use App\Auth\Entry\Http\Admin\Sylius\Controller\User\Form\MainInfoType;
use App\Auth\Infrastructure\Security\JwtTokenizer;
use App\Auth\Infrastructure\Security\UserIdentity;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/users', name: 'app_auth.user_')]
class UserController extends ResourceController
{
    #[Route(path: '/create/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        CreateHandler $handler,
        TranslatorInterface $translator,
    ): Response {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $form = $this->createForm(CreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payload = $form->getData();
            $result = $handler->handle(
                new CreateCommand(
                    email: $payload['email'],
                    plainPassword: $payload['plainPassword'],
                    role: $payload['role'],
                    name: $payload['name'],
                )
            );
            if ($result->isEmailIsBusy()) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.auth.user.flash.error_email_is_busy'));
            }
            if ($result->isSuccess()) {
                $this->addFlash('success', $translator->trans('app.admin.ui.modules.auth.user.flash.success_created'));

                return $this->redirectToRoute('app_auth.user_show', ['id' => $result->user?->getId()->getValue()]);
            }
        }

        return $this->render(
            '@app/admin/layout/crud/create.html.twig',
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

        /** @var User $user */
        $user = $this->findOr404($configuration);
        if (Request::METHOD_GET === $request->getMethod()) {
            $form = $this->createForm(EditType::class, $user);
        } else {
            $form = $this->createForm(EditType::class);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payload = $form->getData();
            $result = $handler->handle(
                new EditCommand(
                    id: $user->getId(),
                    name: $payload['name'],
                    email: $payload['email'],
                    role: $payload['role'],
                )
            );
            if ($result->isEmailIsBusy()) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.auth.user.flash.error_email_is_busy'));
            }
            if ($result->isUserNotExists()) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.auth.user.flash.error_user_not_exists'));
            }
            if ($result->isSuccess()) {
                $this->addFlash('success', $translator->trans('app.admin.ui.modules.auth.user.flash.success_edited'));

                return $this->redirectToRoute('app_auth.user_show', ['id' => $result->user?->getId()->getValue()]);
            }
        }

        return $this->render(
            '@auth/admin/user/update.html.twig',
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
        TranslatorInterface $translator,
        RemoveHandler $handler
    ): Response {
        $result = $handler->handle(
            new RemoveCommand(
                id: new UserId($id)
            )
        );
        if ($result->isUserNotExists()) {
            $this->addFlash('error', $translator->trans('app.admin.ui.modules.auth.user.flash.error_user_not_exists'));
        }
        if ($result->isSuccess()) {
            $this->addFlash('success', $translator->trans('app.admin.ui.modules.auth.user.flash.success_deleted'));
        }

        return $this->redirectToRoute('app_auth.user_index');
    }

    #[Route(path: '/account/show', name: 'account', methods: ['GET', 'POST'])]
    public function account(
        Request $request,
        TranslatorInterface $translator,
        ChangePasswordHandler $changePasswordHandler,
        EditHandler $editHandler,
        JwtTokenizer $jwtTokenizer,
        UserIdentity $userIdentity,
    ): Response {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var User $user */
        $user = $this->getUser();

        if (Request::METHOD_GET === $request->getMethod()) {
            $mainInfoForm = $this->createForm(MainInfoType::class, $user);
            $changePasswordForm = $this->createForm(ChangePasswordType::class, $user);
        } else {
            $mainInfoForm = $this->createForm(MainInfoType::class);
            $changePasswordForm = $this->createForm(ChangePasswordType::class);
        }

        $mainInfoForm->handleRequest($request);
        $changePasswordForm->handleRequest($request);

        if ($mainInfoForm->isSubmitted() && $mainInfoForm->isValid()) {
            $payload = $mainInfoForm->getData();
            $result = $editHandler->handle(
                new EditCommand(
                    id: $user->getId(),
                    name: $payload['name'],
                    email: $payload['email'],
                    role: $payload['role']
                )
            );
            if ($result->isEmailIsBusy()) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.auth.user.flash.error_email_is_busy'));
            }
            if ($result->isUserNotExists()) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.auth.user.flash.error_user_not_exists'));
            }
            if ($result->isSuccess()) {
                $this->addFlash('success', $translator->trans('app.admin.ui.modules.auth.user.flash.success_edited'));
            }

            return $this->redirectToRoute('app_auth.user_account');
        }

        if ($changePasswordForm->isSubmitted() && $changePasswordForm->isValid()) {
            $payload = $changePasswordForm->getData();

            /** @var array|null $changePassword */
            $changePassword = $request->request->all()['change_password'] ?? null;
            if (!is_array($changePassword)) {
                $changePassword = [];
            }
            $oldPassword = array_key_exists('oldPassword', $changePassword)
                ? $changePassword['oldPassword']
                : ''
            ;

            $result = $changePasswordHandler->handle(
                new ChangePasswordCommand(
                    id: $user->getId(),
                    newPassword: $payload['plainPassword'],
                    oldPassword: $oldPassword
                )
            );
            if ($result->isInvalidCredentials()) {
                $this->addFlash('error', $translator->trans('app.admin.ui.modules.auth.user.flash.error_invalid_credentials'));
            }
            if ($result->isSuccess()) {
                $this->addFlash('success', $translator->trans('app.admin.ui.modules.auth.user.flash.success_password_was_updated'));
            }

            return $this->redirectToRoute('app_auth.user_account');
        }

        return $this->render('@auth/admin/user/account.html.twig', [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'resource' => $user,
            $this->metadata->getName() => $user,
            'mainInfoForm' => $mainInfoForm->createView(),
            'changePasswordForm' => $changePasswordForm->createView(),
            'accessToken' => $jwtTokenizer->generateAccessToken($userIdentity),
        ]);
    }
}
