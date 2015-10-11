<?php

namespace Ctrl\RadBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class UserRepository extends EntityRepository
{
    public function getKnownRoles($keyAndLabel = false)
    {
        $rows = $this->createQueryBuilder('u')
            ->select('u.roles')
            ->getQuery()->getResult(Query::HYDRATE_SCALAR);

        $knownRoles = [];
        foreach ($rows as $r) {
            $roles = unserialize($r['roles']);
            array_walk(
                $roles,
                function ($role) use (&$knownRoles) {
                    $knownRoles[$role] = preg_replace('/^ROLE_/', '', $role);
                }
            );
        }

        if (!$keyAndLabel) {
            return array_keys($knownRoles);
        }

        return $knownRoles;
    }
}
