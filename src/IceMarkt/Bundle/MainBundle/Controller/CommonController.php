<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 20/08/2014
 * Time: 12:52
 */

namespace IceMarkt\Bundle\MainBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CommonController extends Controller
{

    /**
     * Method for getting pagination
     *
     * @Route("/pagination/{currentPage}/{pageSize}/{totalPages}/{pageRouteName}/",
     *  name="pagination"
     * )
     * @Template()
     *
     * @param Integer $currentPage
     * @param Integer $pageSize
     * @param Integer $totalPages
     * @param String  $pageRouteName
     * @param Request $request
     *
     * @return array
     */
    public function paginationAction($currentPage, $pageSize, $totalPages, $pageRouteName, Request $request)
    {
        $varsForTwig = array(
            'currentPageNumber' => $currentPage,
            'totalPages'        => $totalPages,
            'pageSize'          => $pageSize,
            'pageRouteName'     => $pageRouteName
        );

        return $varsForTwig;
    }
}
