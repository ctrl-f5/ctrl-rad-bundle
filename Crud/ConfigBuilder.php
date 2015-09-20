<?php

namespace Ctrl\RadBundle\Crud;

use Ctrl\Common\EntityService\ServiceInterface;
use Ctrl\RadBundle\TableView\Table;
use Symfony\Component\Form\FormInterface;

class ConfigBuilder
{
    protected $config = array();

    protected $action = array();

    protected $routes = array();

    /**
     * @param array $defaultOptions
     */
    public function __construct(array $defaultOptions = array())
    {
        $this->config = array_merge(
            $this->config,
            $defaultOptions
        );

        if (array_key_exists('action', $this->config)) {
            $this->routes = $this->config['action'];
        }
        if (array_key_exists('routes', $this->config)) {
            $this->routes = $this->config['routes'];
        }
    }

    /**
     * @param string $name
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->action['template'] = $template;

        return $this;
    }

    /**
     * @param ServiceInterface $service
     * @return $this
     */
    public function setEntityService(ServiceInterface $service)
    {
        $this->config['entity_service'] = $service;

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
            $this->config['label'] = $single;
        }
        if (!is_null($plural)) {
            $this->config['label_plural'] = $plural;
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
        $this->routes[$name] = $route;

        return $this;
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setRoutePrefix($prefix)
    {
        $this->routes['prefix'] = $prefix;

        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setFilterEnabled($enabled)
    {
        $this->action['filter_enabled'] = $enabled;

        return $this;
    }

    /**
     * @param FormInterface $form
     * @return $this
     */
    public function setFilterForm(FormInterface $form, $template = null)
    {
        $this->action['filter_form'] = $form;
        $this->setFilterEnabled(true);

        if ($template) {
            $this->action['template_filter_form'] = $template;
        }

        return $this;
    }

    /**
     * @param FormInterface $form
     * @return $this
     */
    public function setForm(FormInterface $form)
    {
        $this->action['form'] = $form;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setActionConfig($name, $value)
    {
        $this->action[$name] = $value;
    }

    /**
     * @param Table $table
     * @return $this
     */
    public function setTable(Table $table)
    {
        $this->action['table'] = $table;

        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setActionClass($class)
    {
        $this->config['action_class'] = $class;

        return $this;
    }

    /**
     * @param array $sort
     * @return $this
     */
    public function setSort(array $sort)
    {
        $this->action['sort'] = $sort;

        return $this;
    }

    /**
     * @param int|null $id
     * @return $this
     */
    public function setEntityId($id = null)
    {
        $this->action['entity_id'] = $id;

        if ($id && isset($this->config['entity_service'])) {
            $this->setEntity(
                $this->config['entity_service']->getFinder()->get($id)
            );
        }

        return $this;
    }

    /**
     * @return null|int
     */
    public function getEntityId()
    {
        if (isset($this->action['entity_id'])) return $this->action['entity_id'];

        return null;
    }

    /**
     * @param $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->action['entity'] = $entity;

        return $this;
    }

    /**
     * @return null|object
     */
    public function getEntity()
    {
        if (
            !isset($this->action['entity']) &&
            isset($this->action['entity_id'], $this->config['entity_service'])
        ) {
            $this->action['entity'] = $this->config['entity_service']->getFinder()->get($this->action['entity_id']);
        }

        if (isset($this->action['entity'])) {
            return $this->action['entity'];
        }

        return null;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function setPrePersist($callable)
    {
        $this->action['pre_persist'] = $callable;

        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function setPostPersist($callable)
    {
        $this->action['post_persist'] = $callable;

        return $this;
    }

    /**
     * @param bool $redirect
     * @return $this
     */
    public function redirectAfterPersist($redirect = true)
    {
        $this->action['save_success_redirect'] = $redirect;

        return $this;
    }

    /**
     * @return Config
     */
    public function buildConfig()
    {
        $this->config['action_config'] = $this->action;
        $this->config['routes'] = $this->routes;
        return new Config($this->config);
    }

    public function createTable()
    {
        return new Table();
    }
}
