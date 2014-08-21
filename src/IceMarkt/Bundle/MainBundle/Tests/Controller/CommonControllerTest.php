<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 21/08/2014
 * Time: 14:10
 */

namespace IceMarkt\Bundle\MainBundle\Tests\Controller;


use IceMarkt\Bundle\MainBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;

class CommonControllerTest extends \PHPUnit_Framework_TestCase
{
    private $commonController;

    /**
     * Set up method
     */
    public function setUp()
    {
        $this->commonController = new CommonController();
    }

    /**
     * @test for the expected result from the pagination controller
     */
    public function expectedPaginationAction()
    {
        $this->commonController = new CommonController();
        $result = $this->commonController->paginationAction(
            2,
            3,
            4,
            'test',
            new Request()
        );

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('currentPageNumber', $result);
        $this->assertArrayHasKey('totalPages', $result);
        $this->assertArrayHasKey('pageSize', $result);
        $this->assertArrayHasKey('pageRouteName', $result);
        $this->assertEquals(2, $result['currentPageNumber']);
        $this->assertEquals(4, $result['totalPages']);
        $this->assertEquals(3, $result['pageSize']);
        $this->assertEquals('test', $result['pageRouteName']);

    }
}
