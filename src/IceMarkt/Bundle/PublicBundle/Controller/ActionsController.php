<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 11/08/2014
 * Time: 19:48
 */

namespace IceMarkt\Bundle\PublicBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class ActionsController
 * @package IceMarkt\Bundle\Communal\Controller
 */
class ActionsController extends Controller
{
    /**
     * @var array
     */
    private $varsForTwig = array();

    /**
     * Controller endpoint for an email recipient to unsubscribe
     *
     * @Route("/unsubscribe/{email}/", name="unsubscribe_action")
     * @Template()
     *
     * @param $email
     * @return array
     */
    public function unsubscribeAction($email)
    {
        $this->varsForTwig['success']   = false;
        $this->varsForTwig['email']     = $email;

        $em = $this->getDoctrine()->getManager();
        $recipient = $em->getRepository('IceMarktMainBundle:MailRecipient')->findOneBy(
            array(
                'emailAddress' => $email
            )
        );

        if ($recipient) {

            $recipient->disable();
            $em->flush();

            $this->varsForTwig['success'] = true;
        }


        return $this->varsForTwig;
    }
}