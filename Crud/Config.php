<?php

namespace Ctrl\RadBundle\Crud;

use Ctrl\RadBundle\Crud\Action\EditAction;
use Ctrl\RadBundle\Crud\Action\IndexAction;
use Ctrl\RadBundle\TableView\Table;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var array
     */
    protected $actionConfig = array();

    /**
     * @var array
     */
    protected $routeConfig = array();

    /**
     * @param array $config
     */
    public function __construct($actionClass, array $config)
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($config);

        $this->actionConfig = $config['action_config'];

        $optionsResolver = new OptionsResolver();
        $this->configureRoutes($optionsResolver);
        $this->routeConfig = $optionsResolver->resolve($config['routes']);
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label'             => 'Entity',
            'label_plural'      => function (Options $options) {
                return $options['label'] . 's';
            },
            'view_vars'         => array(),
            'action_config'     => array(),
            'routes'            => array(),
        ));
        $resolver->setRequired(array(
            'entity_service',
            'action_class',
        ));
        $resolver->setAllowedTypes('entity_service', '\Ctrl\Common\EntityService\ServiceInterface');
    }

    protected function configureRoutes(OptionsResolver $resolver)
    {
        $entityNameCanonical = str_replace(' ', '_', strtolower($this->options['label']));

        $resolver->setDefaults(array(
            'prefix' => '',
            'index'   => function (Options $options) use ($entityNameCanonical) { return $options['prefix'] . $entityNameCanonical . '_index'; },
            'edit'    => function (Options $options) use ($entityNameCanonical) { return $options['prefix'] . $entityNameCanonical . '_edit'; },
            'create'  => function (Options $options) use ($entityNameCanonical) { return $options['prefix'] . $entityNameCanonical . '_create'; },
        ));
    }

    protected function configureTemplates(OptionsResolver $resolver)
    {
        $resolver->setRequired(array(
            'base',
            'app',
            'app_topbar_extra',
            'crud_index',
            'index_table',
            'crud_edit',
            'filter_elements',
            'form_elements',
            'form_buttons',
        ));
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getRouteConfig()
    {
        return $this->routeConfig;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routeConfig;
    }

    /**
     * @return string
     */
    public function getActionClass()
    {
        return $this->options['action_class'];
    }

    /**
     * @return string
     */
    public function getActionConfig()
    {
        return $this->options['action_config'];
    }

    public function resolveCrudActionConfig(OptionsResolver $resolver)
    {
        $this->options['action_config'] = $resolver->resolve($this->options['action_config']);
    }
}
