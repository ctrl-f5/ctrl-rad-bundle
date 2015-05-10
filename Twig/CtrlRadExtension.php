<?php

namespace Ctrl\RadBundle\Twig;

use Ctrl\RadBundle\Tools\Paginator;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CtrlRadExtension extends \Twig_Extension
{
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ctrl_rad_extension';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('call', array($this, 'callableFilter'), array('is_safe' => array('html'))),
        );
    }

    /**
     * @param callable $callable
     * @param mixed ...$argument
     * @return mixed
     */
    public function callableFilter($callable)
    {
        $arguments = func_get_args();
        $callable = array_shift($arguments);

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException();
        }

        return call_user_func_array($callable, $arguments);
    }
}