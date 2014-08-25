<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 25/08/2014
 * Time: 12:15
 */

namespace IceMarkt\Bundle\MainBundle\Tests\Facade;


use IceMarkt\Bundle\MainBundle\Entity\EmailProfile;
use IceMarkt\Bundle\MainBundle\Entity\EmailTemplate;
use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;
use IceMarkt\Bundle\MainBundle\Facade\SendFacade;

class SendFacadeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test for expected results for the sendTemplateToRecipient method
     * this test allows use to assert that each method call only happens once, thus ensuring that
     *  we are not doing unnecessary database calls nor accidentally sending duplicates
     *  Finally, this also asserts that the method is returning $this
     */
    public function sendExpectedResult()
    {
        $doctrineMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $swiftMailerMock = $this->getMockBuilder('\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();

        $templateMock = $this->getMockBuilder('IceMarkt\Bundle\MainBundle\Entity\EmailTemplate')
            ->disableOriginalConstructor()
            ->getMock();

        $emailProfileMock = new EmailProfile();
        $emailProfileMock->setName('test');
        $emailProfileMock->setFromEmail('test@test.com');
        $emailProfileMock->setFromName('test');
        $emailProfileMock->setReplyToEmail('test@test.com');
        $emailProfileMock->setReplyToName('test');

        $templateMock->expects($this->once())
            ->method('getEmailProfile')
            ->will($this->returnValue($emailProfileMock));

        $templateMock->setName('testTemplate');
        $templateMock->setId(1);
        $templateMock->setTemplate('<p>template</p>');

        $templateMock->expects($this->once())
            ->method('getFormat')
            ->will($this->returnValue(EmailTemplate::HTML_HEADER));

        $repositoryMock = $this->getMockBuilder('IceMarkt\Bundle\MainBundle\Entity\EmailTemplateRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($templateMock));

        $doctrineMock->expects($this->once())
            ->method('getRepository')
            ->with('IceMarktMainBundle:EmailTemplate')
            ->will($this->returnValue($repositoryMock));


        $sendFacade = new SendFacade($doctrineMock, $swiftMailerMock);

        $recipient = new MailRecipient();
        $recipient->setLastName('Test');
        $recipient->setFirstName('Test');
        $recipient->setEmailAddress('test@test.com');
        $recipient->enable();

        $result = $sendFacade->sendTemplateToRecipient(1, $recipient);

        $this->assertInstanceOf('IceMarkt\Bundle\MainBundle\Facade\SendFacade', $result);
    }
}
