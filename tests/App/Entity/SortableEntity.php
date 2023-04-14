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

namespace Runroom\SortableBehaviorBundle\Tests\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Runroom\SortableBehaviorBundle\Behaviors\Sortable;

/**
 * @ORM\Entity
 */
class SortableEntity extends AbstractSortableEntity
{
    use Sortable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Gedmo\SortableGroup
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $simpleGroup = null;

    /**
     * @Gedmo\SortableGroup
     *
     * @ORM\OneToOne(targetEntity="SortableGroup")
     */
    private ?SortableGroup $sortableGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setSimpleGroup(?int $simpleGroup): self
    {
        $this->simpleGroup = $simpleGroup;

        return $this;
    }

    public function getSimpleGroup(): ?int
    {
        return $this->simpleGroup;
    }

    public function setSortableGroup(?SortableGroup $sortableGroup): self
    {
        $this->sortableGroup = $sortableGroup;

        return $this;
    }

    public function getSortableGroup(): ?SortableGroup
    {
        return $this->sortableGroup;
    }
}
