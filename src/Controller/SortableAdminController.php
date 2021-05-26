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

namespace Runroom\SortableBehaviorBundle\Controller;

use Runroom\SortableBehaviorBundle\Service\PositionHandlerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/** @extends CRUDController<object> */
class SortableAdminController extends CRUDController
{
    /** @var PropertyAccessorInterface */
    private $accessor;

    /** @var PositionHandlerInterface */
    private $positionHandler;

    public function __construct(
        PropertyAccessorInterface $accessor,
        PositionHandlerInterface $positionHandler
    ) {
        $this->accessor = $accessor;
        $this->positionHandler = $positionHandler;
    }

    final public function moveAction(string $position): Response
    {
        if (!$this->admin->isGranted('EDIT')) {
            $this->addFlash(
                'sonata_flash_error',
                $this->trans('flash_error_no_rights_update_position')
            );

            return new RedirectResponse($this->admin->generateUrl(
                'list',
                ['filter' => $this->admin->getFilterParameters()]
            ));
        }

        $object = $this->admin->getSubject();

        if (null !== $object) {
            $lastPositionNumber = $this->positionHandler->getLastPosition($object);
            $newPositionNumber = $this->positionHandler->getPosition($object, $position, $lastPositionNumber);

            $this->accessor->setValue($object, $this->positionHandler->getPositionFieldByEntity($object), $newPositionNumber);

            $this->admin->update($object);

            if ($this->isXmlHttpRequest()) {
                return $this->renderJson([
                    'result' => 'ok',
                    'objectId' => $this->admin->getNormalizedIdentifier($object),
                ]);
            }

            $this->addFlash(
                'sonata_flash_success',
                $this->trans('flash_success_position_updated')
            );
        }

        return new RedirectResponse($this->admin->generateUrl(
            'list',
            ['filter' => $this->admin->getFilterParameters()]
        ));
    }
}
