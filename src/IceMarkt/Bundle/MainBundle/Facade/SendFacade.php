<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 15/08/2014
 * Time: 13:02
 */

namespace IceMarkt\Bundle\MainBundle\Facade;


use \Doctrine\ORM\EntityManager;
use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;

class SendFacade
{
    /**
     * @var
     */
    private $doctrineManager;

    /**
     * @var
     */
    private $mailer;

    /**
     *
     */
    public function __construct(EntityManager $doctrineManager, \Swift_Mailer $mailer)
    {
        $this->doctrineManager = $doctrineManager;
        $this->mailer = $mailer;
    }

    /**
     * @param $templateId
     * @param MailRecipient $recipient
     * @return $this
     */
    public function sendTemplateToRecipient($templateId, MailRecipient $recipient)
    {

        $emailTemplate = $this->doctrineManager->getRepository('IceMarktMainBundle:EmailTemplate')->findOneBy(
            array(
                'id' => $templateId
            )
        );

        $twig = new \Twig_Environment(new \Twig_Loader_String());

        $body = $twig->render(
            $emailTemplate->getTemplate(),
            array(
                'first_name'  => $recipient->getFirstName()
            )
        );

        $message = \Swift_Message::newInstance()
            ->setSubject($emailTemplate->getSubject())
            ->setFrom($emailTemplate->getEmailProfile()->getFromEmail())
            ->setTo($recipient->getEmailAddress())
            ->setBody($body)
            ->setContentType("text/html");

        $this->mailer->send($message);

        return $this;
    }
}