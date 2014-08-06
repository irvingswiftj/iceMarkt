<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 21/07/2014
 * Time: 20:51
 */

namespace IceMarkt\Bundle\MainBundle\Controller;


use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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
            ->add('name', 'text', array(
                'attr' => array(
                    'class' => 'form-control',
                    'value' => (is_array($recipient) && array_key_exists('name', $recipient))
                            ? $recipient['name'] : ''
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
                $newRecipient->setName($recipient['name']);
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
        $em = $this->getDoctrine()->getManager();

        //start building the form
        $form = $this->createFormBuilder()
            ->add('spreadsheet', 'file', array(
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
            //get the file
            $formFiles = $request->files->get('form');

            $spreadsheet = $formFiles['spreadsheet'];

            $timeUploaded = new \DateTime('UTC');

            // compute a random name and try to guess the extension (more secure)
            $extension = $spreadsheet->guessExtension();
            if (!$extension) {
                // extension cannot be guessed
                $extension = 'csv';
            }

            $someNewFilename = 'csv_' . $timeUploaded->format('Ymd.H.i.s') . '.' . $extension;

            //move the file into our uploads dir with a new name
            $spreadsheet->move('uploads', $someNewFilename);

            // TODO find out which column is what in the spreadsheet
            // TODO insert/update on duplicate each row to the db
        }

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
     *      //TODO consider instead of delete, marking the row disabled
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

        $em->remove($recipient);
        $em->flush();

        $this->varsForTwig['email'] = $email;

        return $this->varsForTwig;
    }
}
