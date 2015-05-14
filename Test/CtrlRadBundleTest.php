<?php

namespace Ctrl\RadBundle\Test;

use Ctrl\RadBundle\CtrlRadBundle;

class CtrlRadBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(array('addCompilerPass'))
            ->getMock();
        $bundle = new CtrlRadBundle();
        $bundle->build($container);
    }
}