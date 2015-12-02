<?php

namespace Ctrl\RadBundle\TableView;

use Ctrl\Common\Paginator\PaginatedDataInterface;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ColumnBuilder
{
    /**
     * @var TableBuilder
     */
    protected $tableBuilder;

    /**
     * @var array
     */
    protected $columns = array();

    /**
     * @var array
     */
    protected $actionColumn;

    public function __construct(TableBuilder $tableBuilder)
    {
        $this->tableBuilder = $tableBuilder;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function addColumn(array $config = array())
    {
        $config = $this->assertConfig(
            $config,
            [
                'type'                  => '',
                'label'                 => '',
                'template'              => TableView::TMPL_TABLE_CELL,
            ]
        );

        $this->columns[] = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        $cols = $this->columns;

        if ($this->actionColumn) {
            $cols[] = $this->actionColumn;
        }

        return $cols;
    }

    public function getActionColumn()
    {
        return $this->actionColumn;
    }

    public function addTextColumn($property, $config = array())
    {
        $config = $this->assertConfig(
            $config,
            ['type' => 'text'],
            [
                'property'              => $property,
                'translate'             => false,
                'translation_domain'    => null,
                'translation_params'    => [],
            ]
        );

        return $this->addColumn($config);
    }

    public function addDateColumn($property, $config = array())
    {
        $config = $this->assertConfig(
            $config,
            ['type' => 'datetime'],
            [
                'property'  => $property,
                'format'    => 'Y-m-d H:i:s',
            ]
        );

        return $this->addColumn($config);
    }

    public function addTemplateColumn($template, $config = array())
    {
        $config = $this->assertConfig(
            $config,
            [
                'type' => 'template',
                'template' => $template,
            ]
        );

        return $this->addColumn($config);
    }

    public function addActionColumn(array $actions = array(), array $config = array())
    {
        if ($this->actionColumn) {
            return $this->addActions($actions);
        }

        $config = $this->assertConfig(
            $config,
            [
                'type'      => 'action',
                'label'     => 'table.column.actions',
                'template'  => TableView::TMPL_TABLE_CELL_ACTIONS,
            ],
            ['actions'   => $actions]
        );

        return $this->actionColumn = $config;
    }

    public function addActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }

    public function addAction(array $action)
    {
        if (!$this->actionColumn) {
            $this->addActionColumn([$action]);
        }

        $this->actionColumn['options']['actions'][] = $action;

        return $this;
    }

    protected function assertConfig($config, array $defaultConfig = array(), array $defaultOptions = array())
    {
        if (is_string($config)) {
            $config = array(
                'label' => $config,
            );
        }
        if (!is_array($config)) {
            return array();
        }

        $config = array_merge($defaultConfig, $config);
        $config['options'] = $this->assertOptions($config, $defaultOptions);

        return $config;
    }

    protected function assertOptions(array $config = array(), array $defaults = array())
    {
        if (!array_key_exists('options', $config)) {
            return $defaults;
        } else {
            return array_merge(
                $defaults,
                $config['options']
            );
        }
    }

    /**
     * @return TableBuilder
     */
    public function table()
    {
        return $this->tableBuilder;
    }
}
