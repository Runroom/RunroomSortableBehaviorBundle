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

namespace Runroom\SortableBehaviorBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Sortable\SortableListener;

final class GedmoPositionHandler extends AbstractPositionHandler
{
    private EntityManagerInterface $entityManager;
    private SortableListener $listener;

    /**
     * @var array<string, int>
     */
    private array $cacheLastPosition = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        SortableListener $listener
    ) {
        $this->entityManager = $entityManager;
        $this->listener = $listener;
    }

    public function getLastPosition(object $entity): int
    {
        /** @var ClassMetadata<object> */
        $meta = $this->entityManager->getClassMetadata(\get_class($entity));
        /** @var array{ useObjectClass: string, position: string, groups?: class-string[] } */
        $config = $this->listener->getConfiguration($this->entityManager, $meta->getName());

        $groups = [];
        if (isset($config['groups'])) {
            foreach ($config['groups'] as $groupName) {
                $groups[$groupName] = $meta->getReflectionProperty($groupName)->getValue($entity);
            }
        }

        $hash = $this->getHash($config, $groups);

        if (!isset($this->cacheLastPosition[$hash])) {
            $this->cacheLastPosition[$hash] = $this->queryLastPosition($config, $groups);
        }

        return $this->cacheLastPosition[$hash];
    }

    public function getPositionFieldByEntity($entity): string
    {
        if (\is_object($entity)) {
            $entity = \get_class($entity);
        }

        $meta = $this->entityManager->getClassMetadata($entity);
        $config = $this->listener->getConfiguration($this->entityManager, $meta->getName());

        return $config['position'];
    }

    /**
     * @param array{
     *     useObjectClass: string,
     *     position: string,
     *     groups?: class-string[]
     * } $config
     * @param array<string, mixed> $groups
     */
    private function getHash(array $config, array $groups): string
    {
        $data = $config['useObjectClass'];
        foreach ($groups as $groupName => $value) {
            if ($value instanceof \DateTime) {
                $value = $value->format('c');
            } elseif (\is_object($value)) {
                $value = spl_object_hash($value);
            }
            $data .= $groupName . $value;
        }

        return md5($data);
    }

    /**
     * @param array{
     *     useObjectClass: string,
     *     position: string,
     *     groups?: class-string[]
     * } $config
     * @param array<string, mixed> $groups
     */
    private function queryLastPosition(array $config, array $groups): int
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select(sprintf('MAX(n.%s)', $config['position']))
            ->from($config['useObjectClass'], 'n');

        $index = 1;
        foreach ($groups as $groupName => $value) {
            if (null === $value) {
                $queryBuilder->andWhere(sprintf('n.%s IS NULL', $groupName));
            } else {
                $queryBuilder->andWhere(sprintf('n.%s = :group_%s', $groupName, $index));
                $queryBuilder->setParameter(sprintf('group_%s', $index), $value);
            }
            ++$index;
        }

        $query = $queryBuilder->getQuery();
        $query->disableResultCache();

        return (int) $query->getSingleScalarResult();
    }
}
