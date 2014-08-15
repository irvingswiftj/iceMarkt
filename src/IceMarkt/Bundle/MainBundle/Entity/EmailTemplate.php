<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 10/08/2014
 * Time: 19:47
 */

namespace IceMarkt\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="email_templates")
 */
class EmailTemplate
{
    const PLAIN_HEADER = 0;

    const HTML_HEADER = 1;

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
    private $subject;

    /**
     * @ORM\Column(type="text")
     */
    private $template;

    /**
     * @ORM\Column(type="text")
     */
    private $format;

    /**
     * @ORM\ManyToOne(targetEntity="IceMarkt\Bundle\MainBundle\Entity\EmailProfile", inversedBy="templates")
     */
    private $emailProfile;

    /**
     * @param mixed $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     * @return $this;
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

    /**
     * @param mixed $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param EmailProfile $emailProfile
     */
    public function setEmailProfile(EmailProfile $emailProfile)
    {
        $this->emailProfile = $emailProfile;
    }

    /**
     * @return EmailProfile
     */
    public function getEmailProfile()
    {
        return $this->emailProfile;
    }
}
