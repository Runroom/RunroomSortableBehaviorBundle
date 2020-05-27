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

use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractPositionHandler implements PositionHandlerInterface
{
    protected $positionField;
    private $sortableGroups;
    private $accessor;

    abstract public function getLastPosition($entity): int;

    public function setPositionField(array $positionField): void
    {
        $this->positionField = $positionField;
    }

    public function setSortableGroups(array $sortableGroups): void
    {
        $this->sortableGroups = $sortableGroups;
    }

    public function getPositionFieldByEntity($entity): string
    {
        if (\is_object($entity)) {
            $entity = ClassUtils::getClass($entity);
        }

        if (isset($this->positionField['entities'][$entity])) {
            return $this->positionField['entities'][$entity];
        }

        return $this->positionField['default'];
    }

    public function getSortableGroupsFieldByEntity($entity): array
    {
        if (\is_object($entity)) {
            $entity = ClassUtils::getClass($entity);
        }

        $groups = [];

        if (isset($this->sortableGroups['entities'][$entity])) {
            $groups = $this->sortableGroups['entities'][$entity];
        }

        return $groups;
    }

    public function getCurrentPosition($entity): int
    {
        return $this->getAccessor()->getValue($entity, $this->getPositionFieldByEntity($entity));
    }

    public function getPosition($object, string $movePosition, int $lastPosition): int
    {
        $currentPosition = $this->getCurrentPosition($object);
        $newPosition = 0;

        switch ($movePosition) {
            case 'up':
                if ($currentPosition > 0) {
                    $newPosition = $currentPosition - 1;
                }
                break;

            case 'down':
                if ($currentPosition < $lastPosition) {
                    $newPosition = $currentPosition + 1;
                }
                break;

            case 'top':
                if ($currentPosition > 0) {
                    $newPosition = 0;
                }
                break;

            case 'bottom':
                if ($currentPosition < $lastPosition) {
                    $newPosition = $lastPosition;
                }
                break;

            default:
                if (is_numeric($movePosition)) {
                    $newPosition = (int) $movePosition;
                }
        }

        return $newPosition;
    }

    private function getAccessor(): PropertyAccessor
    {
        if (!$this->accessor) {
            $this->accessor = PropertyAccess::createPropertyAccessor();
        }

        return $this->accessor;
    }
}
