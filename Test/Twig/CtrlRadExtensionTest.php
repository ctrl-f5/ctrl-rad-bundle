<?php

namespace CtrlRadBundle\Test\Twig;

use Ctrl\RadBundle\Twig\CtrlRadExtension;

class CtrlRadExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CtrlRadExtension
     */
    protected $extension;

    protected function setUp()
    {
        $this->extension = new CtrlRadExtension();
    }

    protected function tearDown()
    {
        $this->extension = null;
    }

    public function test_get_name()
    {
        $this->assertEquals('ctrl_rad_extension', $this->extension->getName());
    }

    public function test_get_filters()
    {
        /** @var \Twig_SimpleFilter[] $filters */
        $filters = $this->extension->getFilters();
        $this->assertEquals(2, count($filters));

        $this->assertEquals('is_type', $filters[0]->getName());
        $this->assertEquals('call', $filters[1]->getName());
    }

    public function test_get_functions()
    {
        /** @var \Twig_SimpleFunction[] $functions */
        $functions = $this->extension->getFunctions();
        $this->assertEquals(1, count($functions));

        $this->assertEquals('is_type', $functions[0]->getName());
    }

    public function test_filter_callable_with_function()
    {
        $value = 0;
        $func = function ($add = 1) use (&$value) {
            $value += $add;
        };

        $this->extension->callableFilter($func);
        $this->assertEquals(1, $value);

        $this->extension->callableFilter($func, 5);
        $this->assertEquals(6, $value);
    }

    public function test_filter_is_type()
    {
        $this->assertTrue($this->extension->isType(1, 'int'));
        $this->assertTrue($this->extension->isType(1, 'numeric'));
        $this->assertTrue($this->extension->isType('1', 'numeric'));
        $this->assertTrue($this->extension->isType('1', 'string'));
        $this->assertTrue($this->extension->isType(new \stdClass, 'object'));
        $this->assertTrue($this->extension->isType(new \DateTime, 'date'));
        $this->assertTrue($this->extension->isType(new \DateTime, 'datetime'));
        $this->assertTrue($this->extension->isType(null, 'null'));
    }
}
