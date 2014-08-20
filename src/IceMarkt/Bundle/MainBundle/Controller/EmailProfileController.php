<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/08/2014
 * Time: 18:41
 */

namespace IceMarkt\Bundle\MainBundle\Controller;

use IceMarkt\Bundle\MainBundle\Entity\EmailProfile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmailProfileController
 * @package IceMarkt\Bundle\MainBundle\Controller
 */
class EmailProfileController extends Controller
{
    /**
     * amount of results per page
     */
    const PAGE_SIZE = 10;

    /**
     * @var array
     */
    private $varsForTwig = array(
        'pageSize' => self::PAGE_SIZE
    );


    /**
     * controller method to the add email profile page
     *
     * @Route("/emailProfile/add/", name="add_email_profile")
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function addEmailProfileAction(Request $request)
    {
        $this->varsForTwig['emailProfileAdded'] = false;

        $profile = $request->request->get('form');

        $form = $this->createFormBuilder()
            ->add('name', 'text', array(
                'label' =>'Name for profile',
                'attr' => array(
                    'placeholder' => 'Name',
                    'required' => 'required',
                    'class' => 'form-control',
                    'value' => (is_array($profile) && array_key_exists('name', $profile))
                            ? $profile['name'] : ''
                )
            ))
            ->add('fromName', 'text', array(
                'label' =>'From Name',
                'attr' => array(
                    'placeholder' => 'From Name',
                    'class' => 'form-control',
                    'value' => (is_array($profile) && array_key_exists('fromName', $profile))
                            ? $profile['fromName'] : ''
                )
            ))
            ->add('fromEmail', 'text', array(
                'label' =>'From Email',
                'attr' => array(
                    'placeholder' => 'From Email Address',
                    'class' => 'form-control',
                    'value' => (is_array($profile) && array_key_exists('fromEmail', $profile))
                            ? $profile['fromEmail'] : ''
                )
            ))
            ->add('replyToName', 'text', array(
                'label' =>'ReplyTo Name',
                'attr' => array(
                    'placeholder' => 'ReplyTo Name',
                    'class' => 'form-control',
                    'value' => (is_array($profile) && array_key_exists('replyToName', $profile))
                            ? $profile['replyToName'] : ''
                )
            ))
            ->add('replyToEmail', 'text', array(
                'label' =>'ReplyTo Email',
                'attr' => array(
                    'placeholder' => 'ReplyTo Email',
                    'class' => 'form-control',
                    'value' => (is_array($profile) && array_key_exists('replyToEmail', $profile))
                            ? $profile['replyToEmail'] : ''
                )
            ))
            ->add('Add', 'submit', array(
                'attr'   =>  array(
                    'class'   => 'btn btn-primary')
            ))
            ->getForm();

        $this->varsForTwig['addProfileForm'] = $form->createView();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $newProfile = new EmailProfile();
            $newProfile->setName($profile['name']);
            $newProfile->setFromName($profile['fromName']);
            $newProfile->setFromEmail($profile['fromEmail']);
            $newProfile->setReplyToName(
                empty($profile['replyToName']) ? $profile['fromName'] : $profile['replyToName']
            );
            $newProfile->setReplyToEmail(
                empty($profile['replyToEmail']) ? $profile['fromEmail'] : $profile['replyToEmail']
            );

            $em->persist($newProfile);
            $em->flush();

            $this->varsForTwig['emailProfileAdded'] = $newProfile->getFromName();

        }

        return $this->varsForTwig;
    }

    /**
     * controller method for the edit email profile page
     *
     * @Route(
     *      "/emailProfile/edit/{id}",
     *      name="edit_email_profile",
     * )
     * @Template()
     *
     * @param Request $request
     * @param $id
     *
     * @return array
     */
    public function editEmailProfileAction(Request $request, $id)
    {
        $this->varsForTwig['emailProfileEdited'] = false;

        $et = $this->getDoctrine()->getManager();

        $emailProfile = $et->getRepository('IceMarktMainBundle:EmailProfile')->findOneBy(
            array(
                'id' => $id
            )
        );

        $profile = $request->request->get('form');

        $form = $this->createFormBuilder()
            ->add('name', 'text', array(
                'label' =>'Name for profile',
                'attr' => array(
                    'placeholder' => 'Name',
                    'required' => 'required',
                    'class' => 'form-control',
                    'value' => empty($profile) ? $emailProfile->getName() : $profile['name']
                )
            ))
            ->add('fromName', 'text', array(
                'label' =>'From Name',
                'required' => 'required',
                'attr' => array(
                    'placeholder' => 'From Name',
                    'class' => 'form-control',
                    'value' => empty($profile) ? $emailProfile->getFromName() : $profile['fromName']
                )
            ))
            ->add('fromEmail', 'text', array(
                'label' =>'From Email',
                'attr' => array(
                    'placeholder' => 'From Email Address',
                    'required' => 'required',
                    'class' => 'form-control',
                    'value' => empty($profile) ? $emailProfile->getFromEmail() : $profile['fromEmail']
                )
            ))
            ->add('replyToName', 'text', array(
                'label' =>'ReplyTo Email',
                'attr' => array(
                    'placeholder' => 'ReplyTo Name',
                    'class' => 'form-control',
                    'value' => empty($profile) ? $emailProfile->getReplyToName() : $profile['replyToName']
                )
            ))
            ->add('replyToEmail', 'text', array(
                'label' =>'ReplyTo Email',
                'attr' => array(
                    'class' => 'form-control',
                    'value' => empty($profile) ? $emailProfile->getReplyToEmail() : $profile['replyToEmail']
                )
            ))
            ->add('Add', 'submit', array(
                'attr'   =>  array(
                    'class'   => 'btn btn-primary')
            ))
            ->getForm();

        $this->varsForTwig['editProfileForm'] = $form->createView();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $emailProfile->setName($profile['name']);
            $emailProfile->setFromName($profile['fromName']);
            $emailProfile->setFromEmail($profile['fromEmail']);
            $emailProfile->setReplyToName($profile['replyToName']);
            $emailProfile->setReplyToEmail($profile['replyToEmail']);
            $em->persist($emailProfile);
            $em->flush();

            $this->varsForTwig['emailProfileEdited'] = $emailProfile->getFromName();

        }

        return $this->varsForTwig;
    }

    /**
     * Controller action for the page of listing email profiles
     *
     * @Route("/emailProfile/list/",
     *          name="view_email_profile_list")
     * @Route("/emailProfile/list/{pageNumber}/",
     *          name="view_email_profile_list_page")
     *          defaults={"pageNumber":"1"}
     * @Template()
     *
     * @param Integer $pageNumber - for pagination
     * @return array
     */
    public function viewEmailProfileListAction($pageNumber = 1)
    {
        $em = $this->getDoctrine()->getManager();

        //deducting 1 from the page number here as numbering starts from 1 and not 0
        $offset = self::PAGE_SIZE * ($pageNumber-1);

        $this->varsForTwig['emailProfiles'] = $em->getRepository('IceMarktMainBundle:EmailProfile')->findAll(
            null,
            self::PAGE_SIZE,
            $offset
        );

        $this->varsForTwig['totalPages'] = (
            (int) ceil($em->getRepository('IceMarktMainBundle:EmailProfile')->fetchCount() / self::PAGE_SIZE)
        );

        $this->varsForTwig['currentPageNumber'] = (int) $pageNumber;

        return $this->varsForTwig;
    }

    /**
     * controller method for deleting an email profiles
     *
     * @Route(
     *      "/emailProfile/delete/{id}",
     *      name="delete_email_profile",
     * )
     *
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteEmailProfileAction($id)
    {
        $et = $this->getDoctrine()->getManager();

        $profile = $et->getRepository('IceMarktMainBundle:EmailProfile')->findOneBy(
            array(
                'id' => $id
            )
        );

        $et->remove($profile);
        $et->flush();

        return $this->redirect($this->generateUrl('view_email_profile_list'));
    }
}
