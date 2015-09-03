<?php

namespace Ctrl\RadBundle\Crud;

use Ctrl\RadBundle\Crud\Action\AbstractAction;
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
     * @return ConfigBuilder
     * @throws \Exception
     */
    public function getCrudConfigBuilder()
    {
        $this->checkContainerAware();

        $builder = $this->container->get('ctrl_rad.crud.config_builder');
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
     * @param Config|ConfigBuilder $config
     * @return AbstractAction
     */
    public function buildCrudIndex($config)
    {
        return $this->buildCrud(AbstractAction::ACTION_INDEX, $config);
    }

    /**
     * @param Config|ConfigBuilder $config
     * @return AbstractAction
     */
    public function buildCrudCreate($config)
    {
        return $this->buildCrud(AbstractAction::ACTION_CREATE, $config);
    }

    /**
     * @param Config|ConfigBuilder $config
     * @return AbstractAction
     */
    public function buildCrudEdit($config)
    {
        return $this->buildCrud(AbstractAction::ACTION_EDIT, $config);
    }

    /**
     * @param string $type
     * @param Config|ConfigBuilder $config
     * @return AbstractAction
     */
    public function buildCrud($type, $config)
    {
        $this->checkContainerAware();

        return $this->container
            ->get('ctrl_rad.crud.action_factory')
            ->create($type, $config);
    }

    protected function configureButton(array $options)
    {
        return array_merge(
            array(
                'type'      => 'a',
                'label'     => 'action',
                'icon'      => null,
                'class'     => 'default',
                'route'     => function ($entity) use ($options) {
                    return $this->generateUrl($options['route_name'], array('id' => $entity->getId()));
                }
            ),
            $options
        );
    }
}
