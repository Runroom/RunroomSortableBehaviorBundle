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
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\MappingException;

final class ORMPositionHandler extends AbstractPositionHandler
{
    /**
     * @var array<string, int>
     */
    private static array $cacheLastPosition = [];

    /**
     * @param array{
     *     entities: array<class-string, string>,
     *     default: string
     * } $positionField
     * @param array{
     *     entities: array<class-string, string[]>
     * } $sortableGroups
     * */
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly array $positionField,
        private readonly array $sortableGroups,
    ) {}

    public function getLastPosition(object $entity): int
    {
        $entityClass = $this->getRealClass($entity);
        $parentEntityClass = true;

        while ($parentEntityClass) {
            $parentEntityClass = get_parent_class($entityClass);

            if (false !== $parentEntityClass && class_exists($parentEntityClass)) {
                $reflection = new \ReflectionClass($parentEntityClass);

                if ($reflection->isAbstract()) {
                    break;
                }

                $entityClass = $parentEntityClass;
            }
        }

        $groups = $this->getSortableGroupsFieldByEntity($entityClass);
        $cacheKey = $this->getCacheKeyForLastPosition($entity, $groups);

        if (!isset(self::$cacheLastPosition[$cacheKey])) {
            $manager = $this->registry->getManagerForClass($entityClass);

            if (null === $manager) {
                throw new \RuntimeException(\sprintf('Entity manager for class %s not found', $entityClass));
            }

            if (!$manager instanceof EntityManagerInterface) {
                throw new \RuntimeException(\sprintf('Entity manager for class %s is not an instance of EntityManagerInterface', $entityClass));
            }

            self::$cacheLastPosition[$cacheKey] = $this->queryLastPosition($manager, $entityClass, $entity, $groups);
        }

        return self::$cacheLastPosition[$cacheKey];
    }

    public function getPositionFieldByEntity($entity): string
    {
        if (\is_object($entity)) {
            $entity = $this->getRealClass($entity);
        }

        return $this->positionField['entities'][$entity] ?? $this->positionField['default'];
    }

    /**
     * @return string[]
     */
    private function getSortableGroupsFieldByEntity(string $entity): array
    {
        $groups = [];

        if (isset($this->sortableGroups['entities'][$entity])) {
            $groups = $this->sortableGroups['entities'][$entity];
        }

        return $groups;
    }

    /**
     * @param string[] $groups
     */
    private function getCacheKeyForLastPosition(object $entity, array $groups): string
    {
        $cacheKey = $this->getRealClass($entity);

        foreach ($groups as $groupName) {
            $value = '';
            $callback = [$entity, 'get' . $groupName];

            if (\is_callable($callback)) {
                $value = \call_user_func($callback);
            }

            $cacheKey .= '_' . ((\is_object($value) && method_exists($value, 'getId')) ? $value->getId() : $value);
        }

        return $cacheKey;
    }

    /**
     * @return class-string
     */
    private function getRealClass(object $object): string
    {
        $class = $object::class;
        $manager = $this->registry->getManagerForClass($class);

        if (null === $manager) {
            return $class;
        }

        try {
            return $manager->getClassMetadata($class)->getName();
        } catch (MappingException) {
            return $class;
        }
    }

    /**
     * @param class-string  $entityClass
     * @param array<string> $groups
     */
    private function queryLastPosition(EntityManagerInterface $manager, string $entityClass, object $entity, array $groups): int
    {
        $queryBuilder = $manager->createQueryBuilder()
            ->select(\sprintf('MAX(t.%s) as last_position', $this->getPositionFieldByEntity($entityClass)))
            ->from($entityClass, 't');

        if (\count($groups) > 0) {
            $index = 1;

            foreach ($groups as $groupName) {
                $value = null;
                $callback = [$entity, 'get' . $groupName];

                if (\is_callable($callback)) {
                    $value = \call_user_func($callback);
                }

                if (null !== $value) {
                    $queryBuilder
                        ->andWhere(\sprintf('t.%s = :group_%s', $groupName, $index))
                        ->setParameter(\sprintf('group_%s', $index), $value);

                    ++$index;
                }
            }
        }

        $query = $queryBuilder->getQuery();
        $query->disableResultCache();

        $lastPosition = $query->getSingleScalarResult();
        \assert(is_numeric($lastPosition));

        return (int) $lastPosition;
    }
}
