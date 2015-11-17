<?php

namespace Ctrl\RadBundle\Twig;

use Ctrl\Common\Paginator\PaginatedDataInterface;
use Ctrl\RadBundle\TableView\Table;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BootstrapExtension extends \Twig_Extension
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var Translator
     */
    protected $translator;

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router, Translator $translator)
    {
        $this->request      = $requestStack->getCurrentRequest();
        $this->router       = $router;
        $this->translator   = $translator;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'bootstrap_extension';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('label', array($this, 'labelFilter'), array('is_safe' => array('html'))),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('page_title',  array($this, 'pageTitle'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('pagination',  array($this, 'pagination'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('button',      array($this, 'button'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('buttonGroup',      array($this, 'buttonGroup'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('table',       array($this, 'table'), array('is_safe' => array('html'), 'needs_environment' => true)),
        );
    }

    public function labelFilter($val, $class = null)
    {
        $content = $val;

        if (is_bool($val)) {
            $content = $this->translator->trans($val ? 'yes': 'no');

            if (!$class) {
                $class = $val ? 'success': 'danger';
            }
        }

        if (!$class) $class = 'default';

        return '<span class="label label-' . $class .'">' . $content . '</span>';
    }

    public function pagination(PaginatedDataInterface $paginator)
    {
        $query = $this->request->query;

        $page       = $paginator->getCurrentPage();
        $pageCount  = $paginator->getPageCount();

        $startPage = $page - 5;
        if ($startPage < 1) $startPage = 1;
        $endPage = $startPage + 10;
        if ($endPage > $pageCount) $endPage = $pageCount;

        $pages = '';
        $route = $this->request->attributes->get('_route');
        $routeParams = $this->request->attributes->get('_route_params');
        $params = $query->all();

        for ($i = $startPage; $i <= $endPage; $i++) {
            $url = $this->router->generate($route, array_merge(
                $routeParams,
                $params,
                $paginator->getRequestParams($i)
            ));
            $active = ($page == $i) ? 'active' : '';
            $pages .= "<li class=\"paginate_button $active\"><a href=\"$url\">$i</a></li>";
        }

        return "<ul class=\"pagination\">$pages</ul>";
    }

    public function pageTitle($title, $width = 12)
    {
        return <<<HTML
            <div class="row">
                <div class="col-lg-$width">
                    <h1 class="page-header">$title</h1>
                </div>
            </div>
HTML;
    }

    /**
     * @param array $config
     * @return string
     */
    public function button($config)
    {
        $path = $this->router->generate($config['route'], $config['route_params']);
        $class = $config['class'];
        $icon = $config['icon'] ? "<i class=\"fa fa-{$config['icon']} fa-fw\"></i> ": '';
        $label = $icon . $this->translator->trans($config['label']);
        $attributes = implode(' ', array_map(
            function ($key, $val) {
                return sprintf('%s="%s"', $key, $val);
            },
            array_keys($config['attributes']),
            $config['attributes']
        ));

        return <<<HTML
            <a href="$path" class="btn btn-sm btn-$class" $attributes>$label</a>
HTML;
    }

    /**
     * @param array $configs
     * @return string
     */
    public function buttonGroup($configs)
    {
        if (!count($configs)) {
            return '';
        }

        $buttons = '';
        foreach ($configs as $config) {
            $buttons .= $this->button($config);
        }

        return <<<HTML
            <div class="btn-group">$buttons</div>
HTML;
    }

    /**
     * @param \Twig_Environment $env
     * @param Table $table
     * @return string
     */
    public function table(\Twig_Environment $env, Table $table)
    {
        return $env->render($table->getTemplate(), array(
            'table' => $table
        ));
    }
}
