<?php

namespace Ctrl\RadBundle\Crud;

use Ctrl\RadBundle\Crud\Action\AbstractAction;
use Ctrl\RadBundle\Crud\Action\EditAction;
use Ctrl\RadBundle\Crud\Action\IndexAction;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @package Ctrl\RadBundle\Crud
 *
 * @property ContainerInterface container
 */
trait Crud
{
    /**
     * @throws \Exception
     */
    private function checkContainerAware()
    {
        if (!is_subclass_of($this, ContainerAwareInterface::class)) {
            throw new \Exception(sprintf('trait can only be used in classes implementing %s', ContainerAwareInterface::class));
        }
    }

    /**
     * @param string $actionClass
     * @return ConfigBuilder
     * @throws \Exception
     */
    public function getCrudConfigBuilder($actionClass)
    {
        $this->checkContainerAware();

        $builder = $this->container->get('ctrl_rad.crud.config_builder');
        $builder->setActionClass($actionClass);
        $this->configureCrudBuilder($builder);

        return $builder;
    }

    /**
     * @param ConfigBuilder $builder
     * @return ConfigBuilder
     */
    public function configureCrudBuilder(ConfigBuilder $builder)
    {
        return $builder;
    }

    /**
     * @param ConfigBuilder $builder
     * @return AbstractAction
     */
    public function buildCrud($builder)
    {
        $this->checkContainerAware();

        return $this->container->get('ctrl_rad.crud.action_factory')->create(
            $builder->buildConfig()
        );
    }
}
