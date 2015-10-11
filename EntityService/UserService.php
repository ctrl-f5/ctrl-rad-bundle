<?php

namespace Ctrl\RadBundle\EntityService;

use Ctrl\Common\EntityService\AbstractDoctrineService;
use Ctrl\RadBundle\Repository\UserRepository;

/**
 * @package Ctrl\RadBundle\EntityService
 *
 * @method UserRepository getEntityRepository()
 */
class UserService extends AbstractDoctrineService
{
    /**
     * @return string
     */
    public function getEntityClass()
    {
        return 'Ctrl\RadBundle\Entity\User';
    }

    /**
     * @return array
     */
    public function getKnownRoles()
    {
        return $this->getEntityRepository()->getKnownRoles();
    }
}
