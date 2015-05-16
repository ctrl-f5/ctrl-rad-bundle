<?php

namespace Ctrl\RadBundle\EntityService;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceManager
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        $service = $this->container->get($id);

        if (!($service instanceof AbstractService)) {
            throw new \RuntimeException(
                sprintf('service with id %s does not extend AbstractService')
            );
        }

        return $service;
    }
}