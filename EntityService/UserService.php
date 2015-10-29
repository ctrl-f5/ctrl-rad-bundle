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
     * @param $keyAndLabel
     * @return array
     */
    public function getKnownRoles($keyAndLabel = false)
    {
        return $this->getEntityRepository()->getKnownRoles($keyAndLabel);
    }
}
