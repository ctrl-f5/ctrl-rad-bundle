<?php

namespace Ctrl\RadBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @param string $roles
     * @return $this
     */
    public function setLdapRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = array($roles);
        }

        $roleNames = array();

        foreach ($roles as $roleString) {
            $parts = explode(',', $roleString);
            foreach ($parts as $part) {
                $subparts = explode('=', $part);
                $roleNames[] = 'LDAP_' . strtoupper(trim(end($subparts)));
            }
        }

        array_unique($roleNames);

        return $this->setRoles($roleNames);
    }
}