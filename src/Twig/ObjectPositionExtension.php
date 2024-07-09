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

namespace Runroom\SortableBehaviorBundle\Twig;

use Runroom\SortableBehaviorBundle\Service\PositionHandlerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ObjectPositionExtension extends AbstractExtension
{
    public function __construct(private readonly PositionHandlerInterface $positionHandler) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('currentObjectPosition', $this->currentPosition(...)),
            new TwigFunction('lastPosition', $this->lastPosition(...)),
        ];
    }

    public function currentPosition(object $entity): int
    {
        return $this->positionHandler->getCurrentPosition($entity);
    }

    public function lastPosition(object $entity): int
    {
        return $this->positionHandler->getLastPosition($entity);
    }
}
