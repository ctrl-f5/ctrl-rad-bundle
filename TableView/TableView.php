<?php

namespace Ctrl\RadBundle\TableView;

use Ctrl\Common\Paginator\PaginatedDataInterface;

class TableView
{
    const TMPL_TABLE                = 'CtrlRadBundle:partial:_table.html.twig';
    const TMPL_TABLE_ROW            = 'CtrlRadBundle:partial:_table_row.html.twig';
    const TMPL_TABLE_ROW_EMPTY      = 'CtrlRadBundle:partial:_table_row_empty.html.twig';
    const TMPL_TABLE_FOOTER         = 'CtrlRadBundle:partial:_table_footer.html.twig';
    const TMPL_TABLE_CELL           = 'CtrlRadBundle:partial:_table_cell.html.twig';
    const TMPL_TABLE_CELL_ACTIONS   = 'CtrlRadBundle:partial:_table_cell_actions.html.twig';

    protected $template = self::TMPL_TABLE;

    protected $footerTemplate = self::TMPL_TABLE_FOOTER;

    protected $rowTemplate = self::TMPL_TABLE_ROW;

    protected $rowEmptyTemplate = self::TMPL_TABLE_ROW_EMPTY;

    protected $classes = array();

    /**
     * @var array
     */
    protected $rows = array();

    /**
     * @var array
     */
    protected $columnHeaders = array();

    /**
     * @var array|PaginatedDataInterface
     */
    protected $data = array();

    /**
     * @var bool
     */
    protected $showPagination = true;

    /**
     * @var array
     */
    protected $variables = array();

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
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @return string
     */
    public function getFooterTemplate()
    {
        return $this->footerTemplate;
    }

    /**
     * @param string $footerTemplate
     * @return $this
     */
    public function setFooterTemplate($footerTemplate)
    {
        $this->footerTemplate = $footerTemplate;
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
     * @param string $rowTemplate
     * @return $this
     */
    public function setRowTemplate($rowTemplate)
    {
        $this->rowTemplate = $rowTemplate;
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
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param array $rows
     * @return $this
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
        return $this;
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
     * @return array
     */
    public function getColumnHeaders()
    {
        return $this->columnHeaders;
    }

    /**
     * @param array $columnHeaders
     * @return $this
     */
    public function setColumnHeaders($columnHeaders)
    {
        $this->columnHeaders = $columnHeaders;
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
     * @param array|PaginatedDataInterface $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return boolean
     */
    public function showPagination()
    {
        return $this->showPagination;
    }

    /**
     * @param boolean $showPagination
     * @return $this
     */
    public function setShowPagination($showPagination)
    {
        $this->showPagination = $showPagination;
        return $this;
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param array $variables
     * @return $this
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
        return $this;
    }
}
