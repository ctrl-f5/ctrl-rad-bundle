<?php

namespace Ctrl\RadBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class SidebarMenuBuilder extends ContainerAware
{
    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('Home', array('route' => 'home'));
        $menu->addChild('Users', array('route' => 'ctrl_rad_user_index'));
    }
}