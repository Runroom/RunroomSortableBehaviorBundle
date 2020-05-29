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

namespace Runroom\SortableBehaviorBundle\Services;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractPositionHandler implements PositionHandlerInterface
{
    /** @var PropertyAccessor */
    private $accessor;

    abstract public function getLastPosition(object $entity): int;

    abstract public function getPositionFieldByEntity($entity): string;

    public function getCurrentPosition(object $entity): int
    {
        if (null === $this->accessor) {
            $this->accessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->accessor->getValue($entity, $this->getPositionFieldByEntity($entity));
    }

    public function getPosition(object $entity, string $movePosition, int $lastPosition): int
    {
        $currentPosition = $this->getCurrentPosition($entity);
        $newPosition = 0;

        switch ($movePosition) {
            case 'up':
                $newPosition = $currentPosition - 1;
                break;

            case 'down':
                $newPosition = $currentPosition + 1;
                break;

            case 'top':
                $newPosition = 0;
                break;

            case 'bottom':
                $newPosition = $lastPosition;
                break;

            default:
                if (is_numeric($movePosition)) {
                    $newPosition = (int) $movePosition;
                }
        }

        return max(0, min($newPosition, $lastPosition));
    }
}
