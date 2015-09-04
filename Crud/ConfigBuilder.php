<?php

namespace Ctrl\RadBundle\Crud;

use Ctrl\Common\EntityService\ServiceInterface;
use Symfony\Component\Form\FormInterface;

class ConfigBuilder
{
    protected $config = array(
        'options'   => array(),
        'templates' => array(),
        'route'     => array(),
    );

    /**
     * @param array $defaultOptions
     */
    public function __construct(array $defaultOptions = array())
    {
        $this->config = array_merge(
            $this->config,
            $defaultOptions
        );
    }

    /**
     * @param string $name
     * @param string $template
     * @return $this
     */
    public function setTemplate($name, $template)
    {
        $this->config['templates'][$name] = $template;

        return $this;
    }

    /**
     * @param ServiceInterface $service
     * @return $this
     */
    public function setEntityService(ServiceInterface $service)
    {
        $this->config['options']['entity_service'] = $service;

        return $this;
    }

    /**
     * @param string|null $single
     * @param string|null $plural
     * @return $this
     */
    public function setEntityLabel($single = null, $plural = null)
    {
        if (!is_null($single)) {
            $this->config['options']['entity_label'] = $single;
        }
        if (!is_null($plural)) {
            $this->config['options']['entity_label_plural'] = $plural;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $route
     * @return $this
     */
    public function setRoute($name, $route)
    {
        $this->config['route'][$name] = $route;

        return $this;
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setRoutePrefix($prefix)
    {
        $this->config['route']['prefix'] = $prefix;

        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setFilterEnabled($enabled)
    {
        $this->config['options']['filter_enabled'] = $enabled;

        return $this;
    }

    /**
     * @param FormInterface $form
     * @return $this
     */
    public function setFilterForm(FormInterface $form)
    {
        $this->config['options']['filter_form'] = $form;

        return $this;
    }

    /**
     * @param FormInterface $form
     * @return $this
     */
    public function setForm(FormInterface $form)
    {
        $this->config['options']['form'] = $form;

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setColumns($columns = array())
    {
        $this->config['options']['columns'] = $columns;

        return $this;
    }

    /**
     * @param array $actions
     * @return $this
     */
    public function setActions($actions = array())
    {
        $this->config['options']['actions'] = $actions;

        return $this;
    }

    /**
     * @param array $context
     * @return $this
     */
    public function setContext(array $context = array())
    {
        $this->config['options']['context'] = $context;
        return $this;
    }

    /**
     * @param int|null $id
     * @return $this
     */
    public function setEntityId($id = null)
    {
        $this->config['options']['entity_id'] = $id;

        if ($id && isset($this->config['options']['entity_service'])) {
            $this->setEntity(
                $this->config['options']['entity_service']->getFinder()->get($id)
            );
        }

        return $this;
    }

    /**
     * @return null|int
     */
    public function getEntityId()
    {
        if (isset($this->config['options']['entity_id'])) return $this->config['options']['entity_id'];

        return null;
    }

    /**
     * @param $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->config['options']['entity'] = $entity;

        return $this;
    }

    /**
     * @return null|object
     */
    public function getEntity()
    {
        if (
            !isset($this->config['options']['entity']) &&
            isset($this->config['options']['entity_id']) &&
            isset($this->config['options']['entity_service'])
        ) {
            $this->config['options']['entity'] = $this->config['options']['entity_service']->getFinder()->get($this->config['options']['entity_id']);
        }

        if (isset($this->config['options']['entity'])) {
            return $this->config['options']['entity'];
        }

        return null;
    }

    /**
     * @return Config
     */
    public function buildConfig()
    {
        return new Config(
            $this->config['options'],
            $this->config['route'],
            $this->config['templates']
        );
    }
}
