<?php

namespace Ctrl\RadBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder extends ContainerAware
{
    public function build(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureMenuEvent::CONFIGURE,
            new ConfigureMenuEvent($factory, $menu)
        );

        return $menu;
    }
}