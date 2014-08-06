<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 21/07/2014
 * Time: 17:08
 */

namespace IceMarkt\Bundle\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MarketingController extends Controller
{

    /**
     * @Route("/marketing/", name="marketing_setup");
     * @Template()
     */
    public function setupMarketingAction()
    {

        //TODO create action to send emails to recipients

        $varsForTwig = array(
            'test'  => 'test'
        );

        return $varsForTwig;
    }
}
