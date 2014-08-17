<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/07/2014
 * Time: 12:22
 */

namespace IceMarkt\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use IceMarkt\Bundle\MainBundle\Entity\MailRecipientRepository;

/**
 * @ORM\Entity(repositoryClass="IceMarkt\Bundle\MainBundle\Entity\MailRecipientRepository")
 * @ORM\Table(name="recipients")
 * @ORM\HasLifecycleCallbacks()
 */
class MailRecipient
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true, nullable=false)
     */
    private $emailAddress;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $last_name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDt;

    /**
     * @param String $emailAddress
     * @throws \Exception if email address is invalid
     * @return $this
     */
    public function setEmailAddress($emailAddress)
    {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception(sprintf('invalid email address : %s', $emailAddress));
        }

        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @return String
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param String $first_name
     * @return $this
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * @return String
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param String $last_name
     * @return $this
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return String
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Whether or not this recipient is enabled
     *
     * @return Boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Disable the recipient
     *
     * @return $this
     */
    public function disable()
    {
        $this->enabled = false;

        return $this;
    }

    /**
     * Enable the recipient
     *
     * @return $this
     */
    public function enable()
    {
        $this->enabled = true;

        return $this;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return MailRecipient
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedDt()
    {
        $this->createdDt = new \DateTime('UTC');
    }

    /**
     * Get createdDt
     *
     * @return \DateTime 
     */
    public function getCreatedDt()
    {
        return $this->createdDt;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the full name for this recipient
     *
     * @return string
     */
    public function getName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }
}
