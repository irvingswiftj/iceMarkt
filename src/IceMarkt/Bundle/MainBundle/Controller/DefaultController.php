<?php

namespace IceMarkt\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * Index action for IceMarkt, tbh its basically hello world, wow it works, load up template + bootstrap navigation!
     *
     * @Route("/", name="home_page")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
}
