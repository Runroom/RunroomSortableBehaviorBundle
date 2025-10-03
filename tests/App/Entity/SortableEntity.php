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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Runroom\SortableBehaviorBundle\Behaviors\Sortable;

/**
 * @psalm-suppress ClassMustBeFinal
 */
#[ORM\Entity]
class SortableEntity extends AbstractSortableEntity
{
    use Sortable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Gedmo\SortableGroup]
    private ?int $simpleGroup = null;

    #[ORM\OneToOne(targetEntity: SortableGroup::class)]
    #[Gedmo\SortableGroup]
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
