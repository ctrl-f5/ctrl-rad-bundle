<?php

namespace Ctrl\RadBundle\TableView;

use Ctrl\Common\Tools\Doctrine\Paginator;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Table
{
    /**
     * @var string
     */
    protected $template = 'CtrlRadBundle:partial:_table.html.twig';

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var array
     */
    protected $actions = array();

    /**
     * @var array|Paginator
     */
    protected $data = array();

    /**
     * @var bool
     */
    protected $enablePaginator = true;

    /**
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array|Paginator
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnablePaginator()
    {
        return $this->enablePaginator;
    }

    /**
     * @param boolean $enablePaginator
     * @return $this
     */
    public function setEnablePaginator($enablePaginator)
    {
        $this->enablePaginator = $enablePaginator;
        return $this;
    }

    /**
     * @return bool
     */
    public function showPagination()
    {
        return $this->enablePaginator && $this->data instanceof Paginator;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setColumns(array $config = array())
    {
        $this->columns = array();

        foreach ($config as $prop => $col) {
            $this->addColumn($prop, $col);
        }

        return $this;
    }

    /**
     * @param string $property
     * @param string|array $config string label or config array
     * @return $this
     */
    public function addColumn($property, $config)
    {
        if (!is_array($config)) {
            $config = array(
                'label' => $config
            );
        }

        $config['property'] = $property;

        $this->columns[] = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnHeaders()
    {
        $headers = array();

        foreach ($this->columns as $col) {
            $headers[] = $col['label'];
        }

        if (count($this->actions)) {
            $headers[] = 'crud.index.column.actions';
        }

        return $headers;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        $rows = array();
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->data as $data) {
            // extract route params
            $actions = array();
            foreach ($this->actions as $action) {
                if (is_callable($action['route_params'])) {
                    $action['route_params'] = call_user_func($action['route_params'], $data);
                }
                $actions[] = $action;
            }

            $values = [];
            foreach ($this->columns as $col) {
                try {
                    $values[$col['property']] = $accessor->getValue($data, $col['property']);
                } catch (UnexpectedTypeException $e) {
                    $values[$col['property']] = null;
                }
            }

            $rows[] = array(
                'data'      => $data,
                'columns'   => $this->columns,
                'values'    => $values,
                'actions'   => $actions,
            );
        }

        return $rows;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setActions(array $config = array())
    {
        $this->actions = array();

        foreach ($config as $action) {
            $this->addAction($action);
        }

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function addAction($config)
    {
        $this->actions[] = array_merge(
            array(
                'type'          => 'a',
                'label'         => 'action',
                'icon'          => null,
                'class'         => 'default',
                'attributes'    => [],
                'route_params'  => [],
            ),
            $config
        );

        return $this;
    }
}
