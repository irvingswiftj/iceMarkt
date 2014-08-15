<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 10/08/2014
 * Time: 19:55
 */

namespace IceMarkt\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="IceMarkt\Bundle\MainBundle\Entity\EmailProfileRepository")
 * @ORM\Table(name="email_profile")
 */
class EmailProfile
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $fromEmail;

    /**
     * @ORM\Column(type="text")
     */
    private $fromName;

    /**
     * @ORM\Column(type="text")
     */
    private $replyToEmail;

    /**
     * @ORM\Column(type="text")
     */
    private $replyToName;

    /**
     * @ORM\OneToMany(targetEntity="IceMarkt\Bundle\MainBundle\Entity\EmailTemplate", mappedBy="emailProfile")
     */
    private $templates;

    public function __construct()
    {
        $this->templates = new EmailTemplateCollection();
    }

    /**
     * @param String $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $fromEmail
     * @return $this
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * @return String
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param String $fromName
     * @return $this
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;

        return $this;
    }

    /**
     * @return String
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param String $replyToEmail
     * @return $this
     */
    public function setReplyToEmail($replyToEmail)
    {
        $this->replyToEmail = $replyToEmail;

        return $this;
    }

    /**
     * @return String
     */
    public function getReplyToEmail()
    {
        return $this->replyToEmail;
    }

    /**
     * @param String $replyToName
     * @return $this
     */
    public function setReplyToName($replyToName)
    {
        $this->replyToName = $replyToName;

        return $this;
    }

    /**
     * @return String
     */
    public function getReplyToName()
    {
        return $this->replyToName;
    }


}