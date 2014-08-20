<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/08/2014
 * Time: 18:41
 */

namespace IceMarkt\Bundle\MainBundle\Controller;

use IceMarkt\Bundle\MainBundle\Entity\EmailProfile;
use IceMarkt\Bundle\MainBundle\Entity\EmailTemplate;
use IceMarkt\Bundle\MainBundle\Entity\EmailProfileRepository;
use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;
use IceMarkt\Bundle\MainBundle\Facade\SendFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmailTemplateController
 * @package IceMarkt\Bundle\MainBundle\Controller
 */
class EmailTemplateController extends Controller
{
    /**
     * amount of results per page
     */
    const PAGE_SIZE = 10;

    /**
     * @var array
     */
    private $varsForTwig = array(
        'pageSize'  => self::PAGE_SIZE
    );


    /**
     * controller method to the add email template page
     *
     * @Route("/emailTemplate/add/", name="add_email_template")
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function addTemplateAction(Request $request)
    {
        $this->varsForTwig['emailTemplateAdded'] = false;


        $em         = $this->getDoctrine()->getManager();
        $template   = $request->request->get('form');

        $form = $this->createFormBuilder()
            ->add('name', 'text', array(
                'attr' => array(
                    'placeholder' => 'Name',
                    'class' => 'form-control',
                    'value' => (is_array($template) && array_key_exists('name', $template))
                            ? $template['name'] : ''
                )
            ))
            ->add('email_profile_id', 'choice', array(
                'choices' => $em->getRepository('IceMarktMainBundle:EmailProfile')->getChoicesArray(),
                'preferred_choices' => array(),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('subject', 'text', array(
                'attr' => array(
                    'placeholder' => 'Subject',
                    'class' => 'form-control',
                    'value' => (is_array($template) && array_key_exists('subject', $template))
                            ? $template['subject'] : ''
                )
            ))
            ->add('template', 'textarea', array(
                'data' => (is_array($template) && array_key_exists('template', $template))
                    ? $template['template'] : '',
                'attr' => array(
                    'placeholder' => 'Template',
                    'class' => 'form-control'
                )
            ))
            ->add('format', 'choice', array(
                'choices' => array(
                    EmailTemplate::PLAIN_HEADER => 'Plain',
                    EmailTemplate::HTML_HEADER => 'HTML'
                ),
                'preferred_choices' => (is_array($template) && array_key_exists('format', $template))
                        ? array($template['format']) : array(),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('Add', 'submit', array(
                'attr'   =>  array(
                    'class'   => 'btn btn-primary')
            ))
            ->getForm();

        $this->varsForTwig['addTemplateForm'] = $form->createView();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $newTemplate = new EmailTemplate();

            $emailProfile = $em->getRepository('IceMarktMainBundle:EmailProfile')->findOneBy(
                array(
                    'id' => $template['email_profile_id']
                )
            );

            if ($emailProfile instanceof EmailProfile) {
                $newTemplate->setEmailProfile($emailProfile);
            }

            $newTemplate->setName($template['name']);
            $newTemplate->setSubject($template['subject']);
            $newTemplate->setFormat($template['format']);
            $newTemplate->setTemplate($template['template']);

            $em->persist($newTemplate);
            $em->flush();

            $this->varsForTwig['emailTemplateAdded'] = $template['name'];

        }

        return $this->varsForTwig;
    }

    /**
     * controller method for the edit email template page
     *
     * @Route(
     *      "/emailTemplate/edit/{id}",
     *      name="edit_template",
     * )
     * @Template()
     *
     * @param Request $request
     * @param $id
     *
     * @return array
     */
    public function editTemplateAction(Request $request, $id)
    {
        $this->varsForTwig['emailTemplateEdited'] = false;

        $et = $this->getDoctrine()->getManager();

        $template = $et->getRepository('IceMarktMainBundle:EmailTemplate')->findOneBy(
            array(
                'id' => $id
            )
        );

        $templateChanges = $request->request->get('form');

        $form = $this->createFormBuilder()
            ->add('name', 'text', array(
                'attr' => array(
                    'placeholder' => 'Name',
                    'class' => 'form-control',
                    'value' => is_array($templateChanges) ? $templateChanges['name'] : $template->getName()
                )
            ))
            ->add('email_profile_id', 'choice', array(
                'label' => 'Email Profile',
                'choices' => $et->getRepository('IceMarktMainBundle:EmailProfile')->getChoicesArray(),
                'preferred_choices' => is_array($templateChanges)
                        ? array($templateChanges['email_profile_id']) : array($template->getEmailProfile()->getId()),
                'attr' => array(
                    'class' => 'form-control',
                    'value' => ''
                )
            ))
            ->add('subject', 'text', array(
                'attr' => array(
                    'placeholder' => 'Subject',
                    'class' => 'form-control',
                    'value' => is_array($templateChanges) ? $templateChanges['subject'] : $template->getSubject()
                )
            ))
            ->add('template', 'textarea', array(
                'data' => is_array($templateChanges) ? $templateChanges['template'] : $template->getTemplate(),
                'attr' => array(
                    'placeholder' => 'Template',
                    'class' => 'form-control',
                )
            ))
            ->add('format', 'choice', array(
                'choices' => array(
                    EmailTemplate::PLAIN_HEADER => 'Plain',
                    EmailTemplate::HTML_HEADER => 'HTML'
                ),
                'preferred_choices' => is_array($templateChanges)
                        ? array($templateChanges['format']) : array($template->getFormat()),
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('Save', 'submit', array(
                'attr'   =>  array(
                    'class'   => 'btn btn-primary')
            ))
            ->getForm();

        $this->varsForTwig['addTemplateForm'] = $form->createView();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $emailProfile = $et->getRepository('IceMarktMainBundle:EmailProfile')->findOneBy(
                array(
                    'id' => $templateChanges['email_profile_id']
                )
            );

            if ($emailProfile instanceof EmailProfile) {
                $template->setEmailProfile($emailProfile);
            }

            $template->setName($templateChanges['name']);
            $template->setSubject($templateChanges['subject']);
            $template->setFormat($templateChanges['format']);
            $template->setTemplate($templateChanges['template']);

            $et->persist($template);
            $et->flush();

            $this->varsForTwig['emailTemplateEdited'] = $template->getName();

        }

        return $this->varsForTwig;
    }

    /**
     * Controller action for the page of listing email templates
     *
     * @Route("/emailTemplate/list/",
     *          name="view_email_template_list")
     * @Route("/emailTemplate/list/{pageNumber}/",
     *          name="view_email_template_list_page")
     *          defaults={"pageNumber":"1"}
     * @Template()
     *
     * @param Integer $pageNumber - for pagination
     * @return array
     */
    public function viewEmailTemplateListAction($pageNumber = 1)
    {
        $em = $this->getDoctrine()->getManager();

        //deducting 1 from the page number here as numbering starts from 1 and not 0
        $offset = self::PAGE_SIZE * ($pageNumber-1);

        $this->varsForTwig['emailTemplates'] = $em->getRepository('IceMarktMainBundle:EmailTemplate')->findAll(
            null,
            self::PAGE_SIZE,
            $offset
        );

        $this->varsForTwig['totalPages'] = (
        (int) ceil($em->getRepository('IceMarktMainBundle:EmailTemplate')->fetchCount() / self::PAGE_SIZE)
        );

        $this->varsForTwig['currentPageNumber'] = (int) $pageNumber;

        return $this->varsForTwig;
    }

    /**
     * Controller action for the content of a the test send modal box
     *
     * @Route("/emailTemplate/testSendForm/{id}",
     *          name="view_email_template_test_send_form")
     * @Template()
     *
     * @param Integer $id
     *
     * @return array
     */
    public function testSendFormAction($id)
    {
        //create for for test sends
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('view_email_template_test_send', array('id' => $id)))
            ->setMethod('POST')
            ->add('first_name', 'text', array(
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('last_name', 'text', array(
                'attr' => array(
                    'class' => 'form-control'
                )
            ))
            ->add('email', 'email', array(
                'attr' => array(
                    'class' => 'form-control',
                )
            ))
            ->add('send', 'submit', array(
                'attr'   =>  array(
                    'class'   => 'btn btn-primary')
            ))
            ->getForm();

        $this->varsForTwig['addMockRecipientForm'] = $form->createView();

        return $this->varsForTwig;
    }

    /**
     * controller method for deleting an email template
     *
     * @Route(
     *      "/emailTemplate/delete/{id}",
     *      name="delete_template",
     * )
     *
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteEmailTemplateAction($id)
    {
        $et = $this->getDoctrine()->getManager();

        $template = $et->getRepository('IceMarktMainBundle:EmailTemplate')->findOneBy(
            array(
                'id' => $id
            )
        );

        $et->remove($template);
        $et->flush();

        return $this->redirect($this->generateUrl('view_email_template_list'));
    }

    /**
     * Controller Action for previewing a template
     * TODO handle invalid id
     *
     * @Route("/emailTemplate/preview/{id}/",
     *          name="view_email_template_preview")
     *
     * @param Integer $id - id of the template
     * @return Response
     */
    public function previewAction($id)
    {
        $et = $this->getDoctrine()->getManager();

        $template = $et->getRepository('IceMarktMainBundle:EmailTemplate')->findOneBy(
            array(
                'id' => $id
            )
        );

        $twig = new \Twig_Environment(new \Twig_Loader_String());

        $response = $twig->render(
            $template->getTemplate(),
            array(
                'first_name'    => 'FIRSTNAME',
                'last_name'     => 'LASTNAME'
            )
        );

        return new Response($response);
    }

    /**
     * Controller Action for sending a test email for a template
     * TODO handle invalid id
     *
     * @Route("/emailTemplate/testSend/{id}/",
     *          name="view_email_template_test_send")
     *
     * @param Integer $id - id of the template
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function testSendAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $template = $em->getRepository('IceMarktMainBundle:EmailTemplate')->findOneBy(
            array(
                'id' => $id
            )
        );

        $formPostData = $request->request->get('form');

        $mockRecipient = new MailRecipient();
        $mockRecipient->setFirstName($formPostData['first_name']);
        $mockRecipient->setLastName($formPostData['last_name']);
        $mockRecipient->setEmailAddress($formPostData['email']);

        $sendFacade = new SendFacade(
            $em,
            $this->get('mailer')
        );

        $sendFacade->sendTemplateToRecipient($id, $mockRecipient);

        return $this->redirect($this->generateUrl('view_email_template_list'));
    }
}