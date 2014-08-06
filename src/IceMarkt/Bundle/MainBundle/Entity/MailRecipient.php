<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/07/2014
 * Time: 12:22
 */

namespace IceMarkt\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="recipients")
 */
class MailRecipient
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=100)
     */
    private $emailAddress;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @param mixed $emailAddress
     * @throws \Exception if email address is invalid
     * @return $this
     */
    public function setEmailAddress($emailAddress)
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception('invalid email address');
        }

        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
