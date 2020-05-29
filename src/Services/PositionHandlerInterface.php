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

    /** @param object|string $entity */
    public function getPositionFieldByEntity($entity): string;

    public function getCurrentPosition(object $entity): int;

    public function getPosition(object $entity, string $movePosition, int $lastPosition): int;
}
