<?php

namespace AppBundle\Twig;

use AppBundle\Tools\Paginator;
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
            new \Twig_SimpleFunction('pagination', array($this, 'pagination'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('is_type', array($this, 'isType')),
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

    public function pagination(Paginator $paginator)
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

    public function isType($var, $type)
    {
        switch ($type) {
            case 'bool':
            case 'boolean':
                return is_bool($var);
            break;
            case 'int':
            case 'integer':
                return is_int($var);
            break;
            case 'str':
            case 'string':
                return is_string($var);
            break;
            case 'array':
                return is_array($var);
            break;
            case 'object':
                return is_object($var);
            break;
            case 'scalar':
                return is_scalar($var);
            break;
            case 'numeric':
                return is_numeric($var);
            break;
            case 'date':
            case 'datetime':
                return is_object($var) && $var instanceof \DateTime;
            break;
            case 'null':
                return is_null($var);
            break;
            default:
                throw new \InvalidArgumentException(
                    sprintf("unknown type given: %s", $type)
                );
        }
    }
}