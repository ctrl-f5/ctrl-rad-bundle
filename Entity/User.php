<?php

namespace Ctrl\RadBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="\Ctrl\RadBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements EncoderAwareInterface
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
     * @var string
     */
    protected $passwordEncoderName;

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
                $roleNames[] = 'ROLE_LDAP_' . strtoupper(str_replace(' ', '_', trim(end($subparts))));
            }
        }

        array_unique($roleNames);

        return $this->setRoles($roleNames);
    }

    /**
     * @return string
     */
    public function getPasswordEncoderName()
    {
        return $this->passwordEncoderName;
    }

    /**
     * @param string $passwordEncoderName
     * @return $this
     */
    public function setPasswordEncoderName($passwordEncoderName)
    {
        $this->passwordEncoderName = $passwordEncoderName;
        return $this;
    }

    /**
     * Gets the name of the encoder used to encode the password.
     *
     * If the method returns null, the standard way to retrieve the encoder
     * will be used instead.
     *
     * @return string
     */
    public function getEncoderName()
    {
        return $this->passwordEncoderName;
    }
}
