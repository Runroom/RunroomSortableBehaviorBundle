<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SortableBehaviorBundle\Action;

use Runroom\SortableBehaviorBundle\Service\PositionHandlerInterface;
use Sonata\AdminBundle\Request\AdminFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MoveAction extends AbstractController
{
    private PropertyAccessorInterface $accessor;
    private TranslatorInterface $translator;
    private AdminFetcherInterface $adminFetcher;
    private PositionHandlerInterface $positionHandler;

    public function __construct(
        PropertyAccessorInterface $accessor,
        TranslatorInterface $translator,
        AdminFetcherInterface $adminFetcher,
        PositionHandlerInterface $positionHandler
    ) {
        $this->accessor = $accessor;
        $this->translator = $translator;
        $this->adminFetcher = $adminFetcher;
        $this->positionHandler = $positionHandler;
    }

    public function __invoke(Request $request, string $position): Response
    {
        $admin = $this->adminFetcher->get($request);

        if (!$admin->isGranted('EDIT')) {
            $this->addFlash(
                'sonata_flash_error',
                $this->translator->trans('flash_error_no_rights_update_position')
            );

            return new RedirectResponse($admin->generateUrl(
                'list',
                ['filter' => $admin->getFilterParameters()]
            ));
        }

        if ($admin->hasSubject()) {
            $object = $admin->getSubject();
            $lastPositionNumber = $this->positionHandler->getLastPosition($object);
            $newPositionNumber = $this->positionHandler->getPosition($object, $position, $lastPositionNumber);

            $this->accessor->setValue($object, $this->positionHandler->getPositionFieldByEntity($object), $newPositionNumber);

            $admin->update($object);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'result' => 'ok',
                    'objectId' => $admin->getNormalizedIdentifier($object),
                ]);
            }

            $this->addFlash(
                'sonata_flash_success',
                $this->translator->trans('flash_success_position_updated')
            );
        }

        return new RedirectResponse($admin->generateUrl(
            'list',
            ['filter' => $admin->getFilterParameters()]
        ));
    }
}
