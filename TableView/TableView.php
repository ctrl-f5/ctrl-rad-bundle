<?php

namespace Ctrl\RadBundle\TableView;

use Ctrl\Common\Paginator\PaginatedDataInterface;

class TableView
{
    /**
     * @var string
     */
    protected $template = 'CtrlRadBundle:partial:_table.html.twig';

    /**
     * @var array
     */
    protected $columnHeaders = array();

    /**
     * @var array
     */
    protected $rows = array();

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
