<?php

namespace Ctrl\RadBundle\TableView;

use Ctrl\Common\Paginator\PaginatedDataInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class TableBuilder
{
    /**
     * @var string
     */
    protected $template = TableView::TMPL_TABLE;

    /**
     * @var string
     */
    protected $rowTemplate = TableView::TMPL_TABLE_ROW;

    /**
     * @var string
     */
    protected $rowEmptyTemplate = TableView::TMPL_TABLE_ROW_EMPTY;

    /**
     * @var string
     */
    protected $footerTemplate = TableView::TMPL_TABLE_FOOTER;

    /**
     * @var array
     */
    protected $classes = array();

    /**
     * @var ColumnBuilder
     */
    protected $columns;

    /**
     * @var array|PaginatedDataInterface
     */
    protected $data = array();

    /**
     * @var bool
     */
    protected $enablePaginator = true;

    /**
     * @var callable|null
     */
    protected $rowProcessor;

    /**
     * @var bool
     */
    protected $alwaysRenderActionColumn = false;

    /**
     * @var array
     */
    protected $viewVariables = array();

    /**
     * @var Translator
     */
    protected $translator;

    public function __construct()
    {
        $this->columns = new ColumnBuilder($this);

        $this->classes = array(
            'table',
            'table-striped',
            'table-bordered',
            'table-hover',
            'table-test',
        );
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param Translator $translator
     * @return $this
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @param array|mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array|PaginatedDataInterface
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
     * @return string
     */
    public function getRowTemplate()
    {
        return $this->rowTemplate;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setRowTemplate($template)
    {
        $this->rowTemplate = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function getRowEmptyTemplate()
    {
        return $this->rowEmptyTemplate;
    }

    /**
     * @param string $rowEmptyTemplate
     * @return $this
     */
    public function setRowEmptyTemplate($rowEmptyTemplate)
    {
        $this->rowEmptyTemplate = $rowEmptyTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getFooterTemplate()
    {
        return $this->footerTemplate;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setFooterTemplate($template)
    {
        $this->footerTemplate = $template;
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
        return $this->enablePaginator && $this->data instanceof PaginatedDataInterface;
    }

    /**
     * @return boolean
     */
    public function getAlwaysRenderActionColumn()
    {
        return $this->alwaysRenderActionColumn;
    }

    /**
     * @param boolean $alwaysRenderActionColumn
     * @return $this
     */
    public function setAlwaysRenderActionColumn($alwaysRenderActionColumn = true)
    {
        $this->alwaysRenderActionColumn = $alwaysRenderActionColumn;
        return $this;
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param array $classes
     * @return $this
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function addClass($class)
    {
        $this->classes[] = $class;
        return $this;
    }

    /**
     * @param array $configs
     * @return ColumnBuilder
     */
    public function columns(array $configs = array())
    {
        foreach ($configs as $property => $config) {
            if (is_string($property)) {
                $this->columns->addTextColumn($property, $config);
            } else {
                $this->columns->addColumn($config);
            }
        }

        return $this->columns;
    }

    /**
     * @return array
     */
    public function getColumnHeaders()
    {
        $headers = array();

        foreach ($this->columns()->getColumns() as $col) {
            $headers[] = $col['label'];
        }

        return $headers;
    }

    /**
     * @return array
     */
    protected function getRows()
    {
        $rows           = array();
        $columns        = $this->columns();
        $accessor       = PropertyAccess::createPropertyAccessor();
        $rowProcessor   = $this->getRowProcessor();

        foreach ($this->data as $data) {

            $cells = [];
            foreach ($columns->getColumns() as $col) {
                try {

                    $val = null;
                    $actions = [];
                    switch (strtolower($col['type'])) {
                        case 'template':
                            break;
                        case 'action':
                            if ($columns->getActionColumn()) {
                                foreach ($columns->getActionColumn()['options']['actions'] as $action) {
                                    if (is_callable($action['route_params'])) {
                                        $action['route_params'] = call_user_func($action['route_params'], $data);
                                    }
                                    $actions[$action['group']][] = $action;
                                }
                            }
                            $col['options']['actions'] = $actions;
                            break;
                        case 'text':
                        case 'datetime':
                            $val = $accessor->getValue($data, $col['options']['property']);

                            if ($col['type'] === 'text') {
                                if ($this->translator && $col['options']['translate'] !== false) {
                                    $val = $this->translator->trans(
                                        $val,
                                        $col['options']['translation_params'],
                                        $col['options']['translation_domain']
                                    );
                                }
                            } else if ($col['type'] === 'datetime') {
                                if ($val instanceof \DateTime && isset($col['options']['format'])) {
                                    $val = date_format($val, $col['options']['format']);
                                }
                            }
                        break;
                    }

                    $cells[] = array_merge($col, ['value' => $val]);

                } catch (UnexpectedTypeException $e) {
                    $cells[] = array_merge($col, ['value' => null]);
                }
            }

            $row = array(
                'actions'       => $actions,
                'data'          => $data,
                'columns'       => $this->columns,
                'cells'         => $cells,
                'attributes'    => [],
                'template'      => $this->rowTemplate,
            );

            if ($rowProcessor) {
                $row = $rowProcessor($row, $this);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param array $config
     * @return array
     */
    public function createAction(array $config = array())
    {
        return array_merge(
            array(
                'type'          => 'a',
                'label'         => 'action',
                'icon'          => null,
                'class'         => 'default',
                'group'         => 'default',
                'attributes'    => [],
                'route_params'  => [],
            ),
            $config
        );
    }

    /**
     * @param array $config
     * @return array
     */
    public function createEditAction(array $config = array())
    {
        $config = array_merge(
            [
                'icon'         => 'edit',
                'label'        => 'edit',
                'class'        => 'primary btn-xs',
                'route_params' => function ($data) {
                    return [ 'id' => $data->getId() ];
                }
            ],
            $config
        );

        return $this->createAction($config);
    }

    /**
     * @param array $config
     * @return array
     */
    public function createDetailAction(array $config = array())
    {
        $config = array_merge(
            array(
                'icon'         => 'file',
                'label'        => 'detail',
                'class'        => 'primary btn-xs',
                'route_params' => function ($data) {
                    return [ 'id' => $data->getId() ];
                }
            ),
            $config
        );

        return $this->createAction($config);
    }

    /**
     * @param array $config
     * @return array
     */
    public function createDeleteAction(array $config = array())
    {
        $config = array_merge(
            [
                'icon'         => 'remove',
                'label'        => 'delete',
                'class'        => 'danger btn-xs',
                'route_params' => function ($data) {
                    return [ 'id' => $data->getId() ];
                }
            ],
            $config
        );

        return $this->createAction($config);
    }

    /**
     * @return callable|null
     */
    public function getRowProcessor()
    {
        return $this->rowProcessor;
    }

    /**
     * @param callable|null $rowProcessor
     * @return $this
     */
    public function setRowProcessor($rowProcessor)
    {
        $this->rowProcessor = $rowProcessor;
        return $this;
    }

    /**
     * @return array
     */
    public function getViewVariables()
    {
        return $this->viewVariables;
    }

    /**
     * @param array $viewVariables
     * @return $this
     */
    public function setViewVariables($viewVariables)
    {
        $this->viewVariables = $viewVariables;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setViewVariable($name, $value)
    {
        $this->viewVariables[$name] = $value;
        return $this;
    }

    /**
     * @return TableView
     */
    public function createView()
    {
        $view = new TableView();
        $view
            ->setData($this->getData())
            ->setVariables($this->getViewVariables())
            ->setTemplate($this->getTemplate())
            ->setClasses($this->getClasses())
            ->setRowTemplate($this->getRowTemplate())
            ->setRowEmptyTemplate($this->getRowEmptyTemplate())
            ->setFooterTemplate($this->getFooterTemplate())
            ->setColumnHeaders($this->getColumnHeaders())
            ->setRows($this->getRows())
            ->setShowPagination($this->showPagination())
        ;

        return $view;
    }
}
