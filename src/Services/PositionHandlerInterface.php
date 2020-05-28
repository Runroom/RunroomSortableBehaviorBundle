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

interface PositionHandlerInterface
{
    public function getLastPosition(object $entity): int;

    public function setPositionField(array $positionField): void;

    public function setSortableGroups(array $sortableGroups): void;

    /** @param object|string $entity */
    public function getPositionFieldByEntity($entity): string;

    /** @param object|string $entity */
    public function getSortableGroupsFieldByEntity($entity): array;

    public function getCurrentPosition(object $entity): int;

    public function getPosition(object $entity, string $movePosition, int $lastPosition): int;
}
