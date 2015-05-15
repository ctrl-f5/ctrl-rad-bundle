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

        $menu->addChild('home', array(
            'route' => 'homepage',
            'label' => '<i class="fa fa-home fa-fw"></i> Home',
            'extras' => array('safe_label' => true),
        ));

        $menu->addChild('user_index', array(
            'route' => 'ctrl_rad_user_index',
            'label' => '<i class="fa fa-users fa-fw"></i> Users',
            'extras' => array('safe_label' => true),
        ));
    }
}