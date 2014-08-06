<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 06/07/2014
 * Time: 13:27
 */

namespace IceMarkt\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class EmailController extends Controller
{

    /**
     * @Route("/email")
     * @Template()
     */
    public function emailAction()
    {
        $varsForTwig = array();

        return $varsForTwig;
    }
}
