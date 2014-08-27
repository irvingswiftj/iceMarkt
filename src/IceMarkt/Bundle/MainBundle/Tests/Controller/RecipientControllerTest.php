<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 26/08/2014
 * Time: 11:41
 */

namespace IceMarkt\Bundle\MainBundle\Tests\Controller;


use IceMarkt\Bundle\MainBundle\Controller\RecipientController;
use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class RecipientControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test check that we can load the first page of view recipient
     */
    public function viewRecipientListExpectedResult()
    {
        $mockRecipient = new MailRecipient();
        $mockRecipient->setEmailAddress('test@test.com');
        $mockRecipient->setLastName('test');
        $mockRecipient->setFirstName('test');
        $mockRecipient->enable();

        $mockRecipientRepository = $this->getMockBuilder('IceMarkt\Bundle\MainBundle\Entity\MailRecipientRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $mockRecipientRepository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(array($mockRecipient)));

        $mockRecipientRepository->expects($this->once())
            ->method('fetchCount')
            ->will($this->returnValue(1));

        $mockEntityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mockEntityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->will($this->returnValue($mockRecipientRepository));

        $controller = new RecipientController();

        $result = $controller->setEntityManager($mockEntityManager)->viewRecipientListAction();

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('pageSize', $result);
        $this->assertArrayHasKey('recipients', $result);
        $this->assertArrayHasKey('totalPages', $result);
        $this->assertArrayHasKey('currentPageNumber', $result);
        $this->assertInternalType('array', $result['recipients']);
        $this->assertInstanceOf('IceMarkt\Bundle\MainBundle\Entity\MailRecipient', array_pop($result['recipients']));

    }
}
