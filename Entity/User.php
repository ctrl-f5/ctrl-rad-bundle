<?php

namespace Ctrl\RadBundle\Entity;

use Ctrl\Common\Entity\EntityInterface;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="\Ctrl\RadBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser implements EncoderAwareInterface, EntityInterface
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
