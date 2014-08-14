<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 14/08/2014
 * Time: 07:30
 */

namespace IceMarkt\Bundle\MainBundle\Tests\Entity;

use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;

class MailRecipientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function settersAndGetters()
    {
        $mailRecipient = new MailRecipient();

        //test setters
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\MailRecipient',
            $mailRecipient->setEmailAddress('test@test.com')
        );
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\MailRecipient',
            $mailRecipient->setEnabled(true)
        );
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\MailRecipient',
            $mailRecipient->setFirstName('fred')
        );
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\MailRecipient',
            $mailRecipient->setLastName('flintstone')
        );

        //test getters
        $this->assertEquals('test@test.com', $mailRecipient->getEmailAddress());
        $this->assertTrue($mailRecipient->getEnabled());
        $this->assertEquals('fred', $mailRecipient->getFirstName());
        $this->assertEquals('flintstone', $mailRecipient->getLastName());
    }

    /**
     * @test
     */
    public function enablingAndDisabling()
    {
        $mailRecipient = new MailRecipient();

        $mailRecipient->disable();
        $this->assertFalse($mailRecipient->getEnabled());

        $mailRecipient->enable();
        $this->assertTrue($mailRecipient->getEnabled());
    }
}