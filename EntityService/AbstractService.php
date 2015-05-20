<?php

namespace Ctrl\RadBundle\EntityService;

use Ctrl\RadBundle\EntityService\Criteria\Resolver;
use Ctrl\RadBundle\Tools\Paginator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

abstract class AbstractService
{
    /**
     * @var ObjectManager|EntityManager
     */
    private $doctrine;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var string
     */
    protected $rootAlias;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Resolver
     */
    protected $criteriaResolver;

    /**
     * @var string|null
     */
    private $queryResultType = null;

    /**
     * @var int
     */
    protected $resultPage = 15;

    /**
     * @var int
     */
    protected $resultPageSize = 15;

    /**
     * @param ObjectManager|EntityManager $doctrine
     * @return $this
     */
    public function setDoctrine($doctrine)
    {
        $this->doctrine = $doctrine;
        return $this;
    }

    /**
     * @return EntityRepository
     */
    protected function getEntityRepository()
    {
        if (!$this->repository) {
            if (!$this->doctrine) {
                throw new \RuntimeException('doctrine not set');
            }
            $this->repository = $this->doctrine->getRepository($this->getEntityClass());
        }

        return $this->repository;
    }

    /**
     * @return Resolver
     */
    protected function getCriteriaResolver()
    {
        if (!$this->criteriaResolver) {
            $this->criteriaResolver = new Resolver($this->getRootAlias());
        }
        return $this->criteriaResolver;
    }

    /**
     * @return string
     */
    abstract function getEntityClass();

    /**
     * @param object $entity
     * @return bool
     */
    public function assertEntityInstance($entity)
    {
        $class = $this->getEntityClass();
        if (!(is_object($entity) && $entity instanceof $class)) {
            throw new \InvalidArgumentException(
                sprintf("Service can only handle entities of class %s", $class)
            );
        }
    }

    /**
     * @return null|string
     */
    private function getQueryResultType()
    {
        $type = $this->queryResultType;
        $this->queryResultType = null;
        return $type;
    }

    /**
     * @param bool $paginate
     * @param int $offset only valid if $paginate is true
     * @return $this
     */
    public function findOneOrNull($paginate = false, $offset = 0)
    {
        $this->queryResultType = $paginate ? 'paginator': 'one_or_null';
        $this->resultOffset = $offset;
        $this->resultLimit = 1;

        return $this;
    }

    /**
     * @return $this
     */
    public function findFirstOrNull()
    {
        $this->queryResultType = 'first_or_null';
        $this->resultOffset = 0;
        $this->resultLimit = 1;

        return $this;
    }

    /**
     * @param int $page
     * @param int|null $pageSize
     * @return $this
     */
    public function paginate($page = 1, $pageSize = null)
    {
        $this->queryResultType = 'paginator';
        $this->resultPage = $page;
        if ($pageSize) $this->resultPageSize = $pageSize;

        return $this;
    }

    /**
     * @param string|null $type
     * @return null|string
     */
    protected function setQueryResultType($type)
    {
        $this->queryResultType = $type;
        return $type;
    }

    /**
     * @param QueryBuilder $query
     * @return array|Paginator
     */
    protected function assertQueryResult(QueryBuilder $queryBuilder)
    {
        $type = $this->getQueryResultType();

        switch ($type) {
            case 'one_or_null':
                return $queryBuilder->getQuery()->getOneOrNullResult();
            case 'first_or_null':
                return $queryBuilder->getQuery()->setMaxResults(1)->getOneOrNullResult();
            case 'paginator':
                return $this->getPaginator($queryBuilder, $this->resultPage, $this->resultPageSize);
            default:
                return $queryBuilder->getQuery()->getResult();
        }
    }

    /**
     * @return string
     */
    public function getRootAlias()
    {
        $arr = explode('\\', $this->getEntityClass());
        return lcfirst(end($arr));
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getBaseQueryBuilder()
    {
        $rootAlias = $this->getRootAlias();
        $queryBuilder = $this->getEntityRepository()->createQueryBuilder($rootAlias);

        return $queryBuilder;
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        if (!$this->logger) {
            // default logger php error_log()
            $this->logger = new Logger("service_error");
            $this->logger->pushHandler(new ErrorLogHandler());
        }

        return $this->logger;
    }

    /**
     * Fetches an entity based on id,
     * fails if entity is not found
     *
     * @param int $id
     * @return object
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get($id)
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->andWhere($this->getRootAlias() . ".id = :id")
            ->setParameter('id', $id);

        return $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * Fetches one entity based on criteria
     * criteria must result in only 1 possible entity being selected
     *
     * @param array $criteria
     * @param array $orderBy
     * @return object[]
     */
    public function getBy(array $criteria = array(), array $orderBy = array())
    {
        $queryBuilder = $this->getBaseQueryBuilder();
        $this->getCriteriaResolver()
            ->resolveCriteria($queryBuilder, $criteria)
            ->resolveOrderBy($queryBuilder, $orderBy);

        return $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * Find all entities, filtered and ordered
     *
     * @pagination
     * @param array $criteria
     * @param array $orderBy
     * @return object[]|Paginator
     */
    public function findAll(array $criteria = array(), array $orderBy = array())
    {
        $queryBuilder = $this->getBaseQueryBuilder();
        $this->getCriteriaResolver()
            ->resolveCriteria($queryBuilder, $criteria)
            ->resolveOrderBy($queryBuilder, $orderBy);

        return $this->assertQueryResult($queryBuilder);
    }

    /**
     * Find first entity based on filtering and ordering
     *
     * @param array $criteria
     * @param array $orderBy
     * @return object[]
     */
    public function findOne(array $criteria = array(), array $orderBy = array())
    {
        $queryBuilder = $this->getBaseQueryBuilder();
        $this->getCriteriaResolver()
            ->resolveCriteria($queryBuilder, $criteria)
            ->resolveOrderBy($queryBuilder, $orderBy);

        $queryBuilder->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param object|int $idOrEntity
     * @param bool $failOnNotFound
     * @return $this
     * @throws EntityNotFoundException
     */
    public function remove($idOrEntity, $failOnNotFound = false)
    {
        $entity = (is_object($idOrEntity)) ?
            $idOrEntity:
            $this->get($idOrEntity);

        if (!$entity && $failOnNotFound) {
            throw new EntityNotFoundException(sprintf(
                "Entity of type %s with id %s could not be found",
                $this->getEntityClass(),
                is_object($idOrEntity) ? $idOrEntity->getId(): $idOrEntity
            ));
        }

        $this->assertEntityInstance($entity);

        $this->doctrine->remove($entity);
        $this->doctrine->flush();

        return $this;
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return $this
     */
    public function persist($entity, $flush = true)
    {
        $this->assertEntityInstance($entity);

        $this->doctrine->persist($entity);
        if ($flush) $this->doctrine->flush();

        return $this;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param int $page
     * @param int $pageSize
     * @param array $orderBy
     * @return Paginator
     */
    protected function getPaginator(QueryBuilder $queryBuilder, $page = 1, $pageSize = 15, array $orderBy = array())
    {
        $this->criteriaResolver->resolveOrderBy($queryBuilder, $orderBy);

        $paginator = new Paginator($queryBuilder);
        $paginator->configure($page, $pageSize);

        return $paginator;
    }
}