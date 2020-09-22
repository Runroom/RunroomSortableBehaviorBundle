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

namespace Runroom\SortableBehaviorBundle\Tests\App\Admin;

use Runroom\SortableBehaviorBundle\Admin\AbstractSortableAdmin;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\SortableEntity;

/** @extends AbstractSortableAdmin<SortableEntity> */
final class SortableEntityAdmin extends AbstractSortableAdmin
{
}
