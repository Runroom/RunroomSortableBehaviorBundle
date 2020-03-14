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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Sortable\SortableListener;

final class GedmoPositionHandler extends AbstractPositionHandler
{
    protected $maxPositions;
    protected $entityManager;
    protected $listener;

    public function __construct(
        EntityManagerInterface $entityManager,
        SortableListener $listener
    ) {
        $this->entityManager = $entityManager;
        $this->listener = $listener;
    }

    public function getLastPosition($entity): int
    {
        $meta = $this->entityManager->getClassMetadata(\get_class($entity));
        $config = $this->listener->getConfiguration($this->entityManager, $meta->name);

        $groups = [];
        if (isset($config['groups'])) {
            foreach ($config['groups'] as $group) {
                $groups[$group] = $meta->getReflectionProperty($group)->getValue($entity);
            }
        }

        $hash = $this->getHash($groups, $config);

        if (isset($this->maxPositions[$hash])) {
            return $this->maxPositions[$hash];
        }

        return $this->getMaxPosition($config, $meta, $groups);
    }

    public function getPositionFieldByEntity($entity): string
    {
        if (\is_object($entity)) {
            $entity = \get_class($entity);
        }

        $meta = $this->entityManager->getClassMetadata($entity);
        $config = $this->listener->getConfiguration($this->entityManager, $meta->name);

        return $config['position'];
    }

    private function getHash(array $groups, array $config): string
    {
        $data = $config['useObjectClass'];
        foreach ($groups as $group => $val) {
            if ($val instanceof \DateTime) {
                $val = $val->format('c');
            } elseif (\is_object($val)) {
                $val = spl_object_hash($val);
            }
            $data .= $group . $val;
        }

        return md5($data);
    }

    private function getMaxPosition(array $config, ClassMetadata $meta, array $groups): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('MAX(n.' . $config['position'] . ')')
            ->from($config['useObjectClass'], 'n');

        $index = 1;
        foreach ($groups as $group => $value) {
            if (null === $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->isNull('n.' . $group));
            } else {
                $queryBuilder->andWhere('n.' . $group . ' = :group__' . $index);
                $queryBuilder->setParameter('group__' . $index, $value);
            }
            ++$index;
        }

        $query = $queryBuilder->getQuery();
        $query->useQueryCache(false);
        $query->useResultCache(false);

        return (int) $query->getSingleScalarResult();
    }
}
