<?php

namespace Ctrl\RadBundle\Tools;

use Symfony\Component\HttpFoundation\Request;

class Paginator extends \Doctrine\ORM\Tools\Pagination\Paginator
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var int
     */
    protected $pageSize = 15;

    /**
     * @var int
     */
    protected $pageCount = 1;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function setPageSize($size = 15)
    {
        $this->pageSize = $size;
        $this->assertPageConfig();
        return $this;
    }

    /**
     * @return int
     */
    public function getPageCount()
    {
        return $this->pageCount;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function handleRequest(Request $request)
    {
        $this->request = $request;
        $query = $request->query;

        $this->currentPage = (int)$query->get('pager[page]', 1, true);
        $this->pageSize = (int)$query->get('pager[pageSize]', $this->pageSize, true);

        $this->assertPageConfig();
    }

    public function getRequestParams($page = 1)
    {
        return array(
            'pager' => array(
                'page' => $page,
                'pageSize' => $this->pageSize,
            )
        );
    }

    protected function assertPageConfig()
    {
        $this->pageCount = ceil($this->count() / $this->pageSize);
        if ($this->currentPage > $this->pageCount) $this->currentPage = $this->pageCount;
        if ($this->currentPage < 1) $this->currentPage = 1;

        $this->getQuery()
            ->setFirstResult(($this->currentPage * $this->pageSize) - ($this->pageSize))
            ->setMaxResults($this->pageSize);
    }
}