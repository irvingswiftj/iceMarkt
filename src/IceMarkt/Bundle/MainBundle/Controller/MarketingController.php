<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 21/07/2014
 * Time: 17:08
 */

namespace IceMarkt\Bundle\MainBundle\Controller;

use IceMarkt\Bundle\MainBundle\Facade\SendFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MarketingController
 * @package IceMarkt\Bundle\MainBundle\Controller
 */
class MarketingController extends Controller
{
    private $varsForTwig = array();

    /**
     * Method for sending out emails
     *
     * @Route("/marketing/", name="marketing");
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function sendEmailsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $sendFacade = new SendFacade(
            $em,
            $this->get('mailer')
        );

        $form = $this->createFormBuilder()
            ->add('email_template_id', 'choice', array(
                'label' => 'Email Template',
                'choices' => $em->getRepository('IceMarktMainBundle:EmailTemplate')->getChoicesArray(),
                'preferred_choices' => array(),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('Send', 'submit', array(
                'attr'   =>  array(
                    'class'   => 'btn btn-primary',
                    'data-loading-text' => 'Loading...'
                )
            ))
            ->getForm();

        $this->varsForTwig['sendEmailsForm'] = $form->createView();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $template   = $request->request->get('form');
            $recipients = $em->getRepository('IceMarktMainBundle:MailRecipient')->findAll();

            foreach ($recipients as $recipient) {
                $sendFacade->sendTemplateToRecipient($template['email_template_id'], $recipient);
            }
        }

        return $this->varsForTwig;
    }

    /**
     * Method for sending out emails
     *
     * @Route("/marketing/sendBatch/{templateId}/{offset}", name="send_batch");
     *
     * @param Request $request
     * @param Integer $templateId
     * @param Integer $offset
     *
     * @return Response (json)
     */
    public function sendEmailBatchAction(Request $request, $templateId, $offset)
    {
        $json['emailStatus'] = array(
            'sent' => array(),
            'failed' => array()
        );
        $em = $this->getDoctrine()->getManager();

        $sendFacade = new SendFacade(
            $em,
            $this->get('mailer')
        );

        $recipients = $em->getRepository('IceMarktMainBundle:MailRecipient')->findAll(null, 10, $offset);

        $json['recipients'] = count($recipients);

        foreach ($recipients as $recipient) {
            try {
                $sendFacade->sendTemplateToRecipient($templateId, $recipient);
                $json['emailStatus']['sent'][] = $recipient->getEmailAddress();
            } catch (Exception $e) {
                $json['emailStatus']['failed'][] = $recipient->getEmailAddress();
            }
        }

        $response = new Response(json_encode($json));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
