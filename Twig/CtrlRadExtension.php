<?php

namespace Ctrl\RadBundle\Twig;

class CtrlRadExtension extends \Twig_Extension
{
    protected $twig;

    public function __construct(\Twig_Environment $twig, array $config)
    {
        $this->twig = $twig;

        $this->twig->addGlobal('ctrl_rad_templates', $config['templates']);
    }

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
            new \Twig_SimpleFilter('is_type', array($this, 'isType')),
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