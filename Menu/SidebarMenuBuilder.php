<?php

namespace Ctrl\RadBundle\Menu;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class SidebarMenuBuilder
{
    protected $authChecker;

    public function __construct(AuthorizationChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

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

        if ($this->authChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild('user_index', array(
                'route' => 'ctrl_rad_user_index',
                'label' => '<i class="fa fa-users fa-fw"></i> Users',
                'extras' => array('safe_label' => true),
            ));
        }

    }
}