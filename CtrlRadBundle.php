<?php

namespace Ctrl\RadBundle;

use Ctrl\RadBundle\DependencyInjection\EntityServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CtrlRadBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new EntityServicePass());
    }

    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
