<?php

namespace Ctrl\RadBundle\Crud;

use Ctrl\RadBundle\Crud\Action\EditAction;
use Ctrl\RadBundle\Crud\Action\IndexAction;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Config
{
    const SAVE_SUCCESS_RELOAD   = 'reload';
    const SAVE_SUCCESS_REDIRECT = 'redirect';

    protected $options = array();

    protected $routeConfig = array();

    protected $templateConfig = array();

    public function __construct(array $options, array $routeConfig, array $templateConfig = array())
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);

        $optionsResolver = new OptionsResolver();
        $this->configureRoutes($optionsResolver);
        $this->routeConfig = $optionsResolver->resolve($routeConfig);

        $optionsResolver = new OptionsResolver();
        $this->configureTemplates($optionsResolver);
        $this->templateConfig = $optionsResolver->resolve($templateConfig);
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'entity_label'          => 'Entity',
            'entity_label_plural'   => function (Options $options) {
                return $options['entity_label'] . 's';
            },
            'entity'                => null,
            'entity_id'             => null,
            'filter_enabled'        => true,
            'filter_form'           => null,
            'form'                  => null,
            'context'               => array(),
            'view_vars'             => array(),
            'columns'               => array(),
            'sort'                  => null,
            'actions'               => array(),
            'action_index'          => IndexAction::class,
            'action_create'         => EditAction::class,
            'action_edit'           => EditAction::class,
            'save_success_redirect' => false,
            'post_persist'          => null,
            'pre_persist'           => null,
        ));
        $resolver->setRequired(array(
            'entity_service'
        ));
        $resolver->setAllowedTypes('entity_service', '\Ctrl\Common\EntityService\ServiceInterface');
    }

    protected function configureRoutes(OptionsResolver $resolver)
    {
        $entityNameCanonical = str_replace(' ', '_', strtolower($this->options['entity_label']));

        $resolver->setDefaults(array(
            'prefix' => '',
            'route_index'   => function (Options $options) use ($entityNameCanonical) { return $options['prefix'] . $entityNameCanonical . '_index'; },
            'route_edit'    => function (Options $options) use ($entityNameCanonical) { return $options['prefix'] . $entityNameCanonical . '_edit'; },
            'route_create'  => function (Options $options) use ($entityNameCanonical) { return $options['prefix'] . $entityNameCanonical . '_create'; },
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
     * @return array
     */
    public function getTemplateConfig()
    {
        return $this->templateConfig;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->templateConfig;
    }
}
