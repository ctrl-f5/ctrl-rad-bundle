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
    public function resolveCriteria(QueryBuilder $queryBuilder, array $criteria = array())
    {
        $criteria = $this->unpack($criteria);

        foreach ($criteria['joins']         as $join => $alias)     $queryBuilder->join($join, $alias);
        foreach ($criteria['conditions']    as $where)              $queryBuilder->andWhere($where);
        foreach ($criteria['parameters']    as $key => $value)      $queryBuilder->setParameter($key, $value);

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $orderBy
     * @return $this
     */
    public function resolveOrderBy(QueryBuilder $queryBuilder, array $orderBy = array())
    {
        foreach ($orderBy as $sortField => $order) $queryBuilder->addOrderBy($sortField, $order);

        return $this;
    }

    /**
     * @param array|string $criteria
     * @return array
     */
    public function unpack($criteria)
    {
        $joins = array();
        $conditions = array();
        $parameters = array();
        $paramCount = 0;

        foreach ((array)$criteria as $key => $val) {
            $hasValue = is_string($key);
            $fieldConfig = $hasValue ? $key: $val;
            $val = (array)$val;

            $expressions = $this->unpackFieldExpression($fieldConfig, $this->getRootAlias());
            foreach ($expressions as $exr) {
                $config = $this->getFieldConfig($exr, $hasValue, $paramCount);
                $path = $config['path'];

                // add conditions and parameters
                if ($config['is_property']) {
                    $conditions[] = $config['field'];
                    if ($config['requires_value']) {
                        if ($config['has_named_param']) {
                            $parameters[$config['param_name']] = count($val) == 1 ? array_shift($val): $val[$config['param_name']];
                        } else {
                            $parameters[] = array_shift($val);
                        }
                    }
                    array_pop($path);
                }

                // add joins
                $joins[] = $path;
            }
        }

        $joins = $this->mergeJoinPaths($joins);

        return array(
            'joins' => $joins,
            'conditions' => $conditions,
            'parameters' => $parameters,
        );
    }

    protected function mergeJoinPaths($joins)
    {
        $merged = array();
        foreach ($joins as $path) {
            $previous = $path[0];
            for ($i = 1; $i < count($path); $i++) {
                $current = $path[$i];
                $merged[$previous . '.' . $current] = $current;
                $previous = $current;
            }
        }

        return $merged;
    }

    protected function unpackFieldExpression($expr, $rootAlias)
    {
        $result = array();
        $expr = str_replace(
            array(' AND ', ' OR '),
            array(' and ', ' or '),
            trim(trim($expr), '()')
        );

        $parts = explode(' and ', $expr);
        if (count($parts) > 1) {
            foreach ($parts as $part) $result[] = $this->unpackFieldExpression(trim($part), $rootAlias)[0];
            return $result;
        }

        for ($i = 0; $i < count($parts); $i++) {
            if (strpos($parts[$i], $rootAlias) !== 0) $parts[$i] = $rootAlias . '.' . $parts[$i];
        }

        return $parts;
    }

    /**
     * @param string $expr
     * @param bool $hasValue
     * @param int &$paramCount
     * @return array
     */
    protected function getFieldConfig($expr, $hasValue, &$paramCount = 0)
    {
        $parts = explode(' ', trim($expr, " ()"));
        $fieldPart = array_shift($parts);
        $path = explode('.', $fieldPart);
        $parent = $path[count($path) - 2];
        $alias = end($path);
        $isProp = $hasValue || count($parts) > 1;

        $hasNamedParam = strpos($expr, ' :') !== false;
        $paramName = $hasNamedParam ? trim(end($parts), ':'): null;

        $comp = '=';
        $requiresValue = true;
        if (count($parts)) {
            $condition = implode(' ', $parts);
            $requiresValue = $hasNamedParam;
        } else {
            $condition = $comp . ' ?' . $paramCount;
            $paramCount++;
        }

        return array(
            'is_property' => $isProp,
            'comparison' => $comp,
            'alias' => $alias,
            'parent' => $parent,
            'path' => $path,
            'requires_value' => $requiresValue,
            'has_named_param' => $hasNamedParam,
            'param_name' => $paramName,
            'field' => $parent . '.' . $alias . ' ' . $condition,
        );
    }
}