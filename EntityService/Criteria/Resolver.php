<?php

namespace Ctrl\RadBundle\EntityService\Criteria;

use Doctrine\ORM\QueryBuilder;

class Resolver
{
    /**
     * @var string
     */
    protected $rootAlias;

    public function __construct($rootAlias)
    {
        $this->rootAlias = $rootAlias;
    }

    /**
     * @return string
     */
    public function getRootAlias()
    {
        return $this->rootAlias;
    }

    /**
     * @param string $rootAlias
     * @return $this
     */
    public function setRootAlias($rootAlias)
    {
        $this->rootAlias = $rootAlias;
        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $criteria
     * @param string $rootAlias
     * @return $this
     */
    public function resolveCriteria(QueryBuilder $queryBuilder, array $criteria = array(), $rootAlias = null)
    {
        if (!$rootAlias) $rootAlias = $this->getRootAlias();

        foreach ($criteria as $key => $value) {

            if (is_numeric($key)) {
                $fieldConfig = $this->getFieldConfig($value, $rootAlias);
                if ($fieldConfig['is_property']) {
                    $queryBuilder->andWhere($value);
                }
            } else {
                $fieldConfig = $this->getFieldConfig($key, $rootAlias);
                $fieldConfig['is_property'] = true;
                $field = $fieldConfig['field'];

                $paramId = count($queryBuilder->getParameters()) + 1;
                if (strpos($key, ' ') === false) {
                    $field .= ' = ?' . $paramId;
                }
                $queryBuilder->andWhere($field)->setParameter($paramId, $value);
            }
            $path = $fieldConfig['path'];
            if ($fieldConfig['is_property']) array_pop($path);
            $this->addJoin($queryBuilder, $path, $rootAlias);
        }

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $orderBy
     * @return $this
     */
    public function resolveOrderBy(QueryBuilder $queryBuilder, array $orderBy = array())
    {
        foreach ($orderBy as $sortField => $order) {
            $queryBuilder->addOrderBy($sortField, $order);
        }

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|array $fieldOrPath
     * @param string $parentAlias
     */
    protected function addJoin(QueryBuilder $queryBuilder, $fieldOrPath, $parentAlias)
    {
        $p = $fieldOrPath;
        if (is_string($fieldOrPath)) {
            $config = $this->getFieldConfig($fieldOrPath, $parentAlias);
            $p = $config['path'];
        }
        for ($i = 0; $i < count($p); $i++) {
            foreach ($queryBuilder->getDQLPart('join') as $root => $joins) {
                foreach ($joins as $join) {
                    if ($join->getAlias() == $p[$i]) return;
                }
            }
            if ($parentAlias == $p[$i]) continue;
            $queryBuilder->join($parentAlias . '.' . $p[$i], $p[$i]);

            $parentAlias = $p[$i];
        }
    }

    /**
     * @param string $expr
     * @param string $parentAlias
     * @return array
     */
    protected function getFieldConfig($expr, $parentAlias)
    {
        $parts = explode(' ', $expr);
        $fieldPart = trim($parts[0], " ()");
        $path = explode('.', $fieldPart);

        if (count($path) > 1) {
            $parent = $path[count($path) - 2];
        } else {
            $parent = $parentAlias;
        }

        return array(
            'is_property' => count($parts) > 1,
            'alias' => end($path),
            'parent' => $parent,
            'path' => $path,
            'field' => $parent . '.' . end($path),
        );
    }
}