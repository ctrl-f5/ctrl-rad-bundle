<?php

namespace Ctrl\RadBundle\Test\DependencyInjection;

use Ctrl\RadBundle\DependencyInjection\CtrlRadExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CtrlRadExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function test_config_is_saved_as_parameter()
    {
        $loader = new CtrlRadExtension();
        $config = array();
        $builder = new ContainerBuilder();
        $loader->load(array($config), $builder);

        $this->assertTrue($builder->hasParameter('ctrl_rad.config'));
        $this->assertTrue(is_array($builder->getParameter('ctrl_rad.config')));
    }

    public function test_config_has_default_templates()
    {
        $loader = new CtrlRadExtension();
        $config = array();
        $builder = new ContainerBuilder();
        $loader->load(array($config), $builder);

        $config = $builder->getParameter('ctrl_rad.config');

        $this->assertArrayHasKey('templates', $config);
        $templates = $config['templates'];
        $this->assertArrayHasKey('base', $templates);
        $this->assertArrayHasKey('app', $templates);
        $this->assertArrayHasKey('app_topbar_extra', $templates);
        $this->assertArrayHasKey('index_table', $templates);
        $this->assertArrayHasKey('filter_elements', $templates);
        $this->assertArrayHasKey('form_elements', $templates);
        $this->assertArrayHasKey('form_buttons', $templates);
    }
}
