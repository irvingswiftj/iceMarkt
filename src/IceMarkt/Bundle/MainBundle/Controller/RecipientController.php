<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 21/07/2014
 * Time: 20:51
 */

namespace IceMarkt\Bundle\MainBundle\Controller;


use JMS\DiExtraBundle\Annotation as DI;
use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;
use IceMarkt\Bundle\MainBundle\Entity\SpreadSheet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class RecipientController
 * @package IceMarkt\Bundle\MainBundle\Controller
 */
class RecipientController extends Controller
{

    const PAGE_SIZE = 10;

    /**
     * @DI\Inject("doctrine.orm.entity_manager")
     */
    private $entityManager;

    /**
     * @var array
     */
    private $varsForTwig = array(
        'pageSize' => self::PAGE_SIZE
    );

    /**
     * setter for entity manager
     *
     * @param  $entityManager
     *
     * @return $this
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        return $this;
    }

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

            $existingRecipient = $this
                ->entityManager
                ->getRepository('IceMarktMainBundle:MailRecipient')
                ->find($recipient['email']);

            if ($existingRecipient === null) {

                $newRecipient = new MailRecipient();
                $newRecipient->setFirstName($recipient['first_name']);
                $newRecipient->setLastName($recipient['last_name']);
                $newRecipient->setEmailAddress($recipient['email']);

                $this->entityManager->persist($newRecipient);
                $this->entityManager->flush();

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
     * @throws \Exception if email address column can not be found
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

            //this resolves line ending issues that I have incurred
            ini_set('auto_detect_line_endings', '1');

            $spreadsheet->upload();

            $this->entityManager->persist($spreadsheet);
            $this->entityManager->flush();

            if (($handle = fopen($spreadsheet->getWebPath(), "r")) !== false) {
                $firstIteration     = true;
                // the fields calculation i don't think should be in the controller, however for now
                // this works as a proof of concept
                $emailKey           = -1;
                $firstNameKey       = -1;
                $lastNameKey        = -1;
                $nameKey            = -1;
                while (($data = fgetcsv($handle, 0, ",")) !== false) {
                    //on first iteration, we should find out which column is what
                    if ($firstIteration) {
                        $emailKey       = $spreadsheet->getEmailColumnIndex($data);
                        $firstNameKey   = $spreadsheet->getFirstNameColumnIndex($data);
                        $lastNameKey    = $spreadsheet->getLastNameColumnIndex($data);
                        $nameKey        = $spreadsheet->getNameColumnIndex($data);

                        if ($firstNameKey === -1
                            && $lastNameKey === -1
                            && $nameKey === -1
                        ) {
                            //at this point, lets just assume the the csv is surname
                            // and then first name after the email address
                            $lastNameKey    = $emailKey + 1;
                            $firstNameKey   = $emailKey + 2;
                        }

                        $firstIteration = false;
                    }

                    if ($emailKey === -1) {
                        // oh dear, we can't find the email address column!
                        throw new \Exception('No Valid email address column could be found');
                    }

                    //check that this row has a valid email address
                    if (filter_var($data[$emailKey], FILTER_VALIDATE_EMAIL)) {
                        if ($nameKey !== -1) {
                            //name logic, it seems that we need to work out what is first name and what is last name
                            $nameArray  = explode(" ", $data[$nameKey]);
                            $lastName   = array_pop($nameArray);
                            $firstName  = implode(" ", $nameArray);
                        } else {
                            $firstName = isset($data[$firstNameKey])
                                ? $data[$firstNameKey] : 'na';
                            $lastName = isset($data[$lastNameKey])
                                ? $data[$lastNameKey] : 'na';
                        }

                        try {
                            $mailRecipient = new MailRecipient();
                            $mailRecipient->setEmailAddress($data[$emailKey]);
                            $mailRecipient->setFirstName($firstName);
                            $mailRecipient->setLastName($lastName);
                            $this->entityManager->persist($mailRecipient);
                            $this->entityManager->flush();
                        } catch (\Exception $e) {
                            //TODO handle when mailrecipient cant be added: probably already exists!
                        }
                    }
                }
                fclose($handle);
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

        //deducting 1 from the page number here as numbering starts from 1 and not 0
        $offset = self::PAGE_SIZE * ($pageNumber-1);

        $this->varsForTwig['recipients'] = $this
            ->entityManager
            ->getRepository('IceMarktMainBundle:MailRecipient')
            ->findAll(
                null,
                self::PAGE_SIZE,
                $offset
            );

        $this->varsForTwig['totalPages'] = (
            (int) ceil(
                $this->entityManager->getRepository('IceMarktMainBundle:MailRecipient')->fetchCount() / self::PAGE_SIZE
            )
        );

        $this->varsForTwig['currentPageNumber'] = (int) $pageNumber;

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
        $recipient = $this->entityManager->getRepository('IceMarktMainBundle:MailRecipient')->findOneBy(
            array(
                'emailAddress' => $email
            )
        );

        $recipient->disable();
        $this->entityManager->flush();

        $this->varsForTwig['email'] = $email;

        return $this->varsForTwig;
    }
}
