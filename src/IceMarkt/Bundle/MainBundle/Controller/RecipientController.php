<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 21/07/2014
 * Time: 20:51
 */

namespace IceMarkt\Bundle\MainBundle\Controller;


use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;
use IceMarkt\Bundle\MainBundle\Entity\SpreadSheet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class RecipientController
 * @package IceMarkt\Bundle\MainBundle\Controller
 */
class RecipientController extends Controller
{
    private $varsForTwig = array();

    /**
     * controller method to the add recipient page
     *
     * @Route("/recipient/add/", name="add_recipient")
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function addRecipientAction(Request $request)
    {
        $this->varsForTwig['emailAdded'] = false;

        $recipient = $request->request->get('form');

        $form = $this->createFormBuilder()
            ->add('first_name', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'value' => (is_array($recipient) && array_key_exists('first_name', $recipient))
                            ? $recipient['first_name'] : ''
                )
            ))
            ->add('last_name', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'value' => (is_array($recipient) && array_key_exists('last_name', $recipient))
                            ? $recipient['last_name'] : ''
                )
            ))
            ->add('email', 'email', array(
                'attr' => array(
                    'class' => 'form-control',
                    'value' => (is_array($recipient) && array_key_exists('email', $recipient))
                            ? $recipient['email'] : ''
                )
            ))
            ->add('Add', 'submit', array(
                'attr'   =>  array(
                    'class'   => 'btn btn-primary')
            ))
            ->getForm();

        $this->varsForTwig['addRecipientForm'] = $form->createView();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $existingRecipient = $em->getRepository('IceMarktMainBundle:MailRecipient')->find($recipient['email']);

            if ($existingRecipient === null) {

                $newRecipient = new MailRecipient();
                $newRecipient->setFirstName($recipient['first_name']);
                $newRecipient->setLastName($recipient['last_name']);
                $newRecipient->setEmailAddress($recipient['email']);

                $em->persist($newRecipient);
                $em->flush();

                $this->varsForTwig['emailAdded'] = $recipient['email'];

            } else {
                $this->varsForTwig['emailNotAdded'] = $recipient['email'];
                $this->varsForTwig['errorMsg'] = sprintf('Email %s already exists!', $recipient['email']);
            }

        }

        return $this->varsForTwig;
    }

    /**
     * Controller Action to display and process form for uploading spreadsheet of recipients
     *
     * @Route("/recipient/addBulk/", name="bulk_add_recipient")
     * @Template()
     *
     * @param Request $request
     * @return array
     */
    public function bulkAddRecipientAction(Request $request)
    {
        $spreadsheet = new SpreadSheet();

        //start building the form
        $form = $this->createFormBuilder($spreadsheet)
            ->add('name', 'text', array(
                'label' => 'Name',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Name',
                    'class' => 'form-control',
                )
            ))
            ->add('file', null, array(
                'required' => true,
                'attr' => array(
                            'class' => 'btn btn-default btn-file'
                        )
            ))
            ->add('Upload', 'submit', array(
                'attr'   =>  array(
                    'class'   => 'btn btn-primary')
            ))
            ->getForm();
        //well that was a very easy way to built a form!

        $this->varsForTwig['BulkAddRecipientForm'] = $form->createView();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $spreadsheet->upload();

            $em->persist($spreadsheet);
            $em->flush();

            if (($handle = fopen($spreadsheet->getWebPath(), "r")) !== false) {
                while (($data = fgetcsv($handle, 0, ",")) !== false) {
                    if (filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                        $firstName = isset($data[1]) && strlen($data[1] > 0) ? $data[1] : 'na';
                        $lastName = isset($data[2]) && strlen($data[2] > 0) ? $data[2] : 'na';

                        $mailRecipient = new MailRecipient();
                        $mailRecipient->setEmailAddress($data[0]);
                        $mailRecipient->setFirstName($firstName);
                        $mailRecipient->setLastName($lastName);
                        $em->persist($mailRecipient);
                    }
                }
                fclose($handle);
                $em->flush();
            }

        }


        $this->varsForTwig['formErrors'] = $form->getErrors(true, false);

        return $this->varsForTwig;
    }

    /**
     * Controller action for the page of listing recipients
     *
     * @Route("/recipient/list/",
     *          name="view_recipient_list")
     * @Route("/recipient/list/{pageNumber}/",
     *          name="view_recipient_list_page")
     *          defaults={"pageNumber":"1"}
     * @Template()
     *
     * @param Integer $pageNumber - for pagination
     * @return array
     */
    public function viewRecipientListAction($pageNumber = 1)
    {
        $em = $this->getDoctrine()->getManager();

        //TODO add support for pagination
        $this->varsForTwig['recipients'] = $em->getRepository('IceMarktMainBundle:MailRecipient')->findAll();

        return $this->varsForTwig;
    }


    /**
     * Controller Action for removing a recipient
     *      recipient is remove by primary key which is the email address
     *
     * @Route("/recipient/remove/{email}/", name="remove_recipient")
     * @Template()
     *
     * @param String $email - email address of recipient
     *
     * @return array
     */
    public function removeRecipientAction($email)
    {
        $em = $this->getDoctrine()->getManager();
        $recipient = $em->getRepository('IceMarktMainBundle:MailRecipient')->findOneBy(
            array(
                'emailAddress' => $email
            )
        );

        $recipient->disable();
        $em->flush();

        $this->varsForTwig['email'] = $email;

        return $this->varsForTwig;
    }
}
