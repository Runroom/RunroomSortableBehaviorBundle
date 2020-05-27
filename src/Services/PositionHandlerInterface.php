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
    public function getLastPosition($entity): int;

    public function setPositionField(array $positionField): void;

    public function setSortableGroups(array $sortableGroups): void;

    public function getPositionFieldByEntity($entity): string;

    public function getSortableGroupsFieldByEntity($entity): array;

    public function getCurrentPosition($entity): int;

    public function getPosition($object, string $movePosition, int $lastPosition): int;
}
