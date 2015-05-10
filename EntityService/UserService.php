<?php

namespace Ctrl\RadBundle\EntityService;

class UserService extends AbstractService
{
    /**
     * @return string
     */
    function getEntityClass()
    {
        return 'Ctrl\RadBundle\Entity\User';
    }
}