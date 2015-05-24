<?php

namespace Ctrl\RadBundle\EntityService;

use Ctrl\Common\EntityService\AbstractDoctrineService;

class UserService extends AbstractDoctrineService
{
    /**
     * @return string
     */
    function getEntityClass()
    {
        return 'Ctrl\RadBundle\Entity\User';
    }
}