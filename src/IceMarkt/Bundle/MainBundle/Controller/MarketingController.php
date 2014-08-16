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
use Symfony\Component\HttpFoundation\Request;

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
        $sendFacade = new SendFacade(
            $this->getDoctrine()->getManager(),
            $this->get('mailer')
        );

        $em = $this->getDoctrine()->getManager();

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
                    'class'   => 'btn btn-primary')
            ))
            ->getForm();

        $this->varsForTwig['sendEmailsForm'] = $form->createView();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $template   = $request->request->get('form');
            $recipients = $em->getRepository('IceMarktMainBundle:MailRecipient')->findAll();

            //TODO send in batches

            foreach ($recipients as $recipient) {
                $sendFacade->sendTemplateToRecipient($template['email_template_id'], $recipient);
            }
        }

        return $this->varsForTwig;
    }
}
