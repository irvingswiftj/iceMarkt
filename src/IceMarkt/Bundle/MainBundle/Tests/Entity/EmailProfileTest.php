<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 15/08/2014
 * Time: 09:18
 */

namespace IceMarkt\Bundle\MainBundle\Tests\Entity;

use IceMarkt\Bundle\MainBundle\Entity\EmailProfile;

class EmailProfileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test for setters and getters
     */
    public function settersAndGetters()
    {
        $profile = new EmailProfile();

        $name = 'fred';
        $email = 'test@test.com';

        $this->assertInstanceOf('IceMarkt\Bundle\MainBundle\Entity\EmailProfile', $profile->setName($name));
        $this->assertInstanceOf('IceMarkt\Bundle\MainBundle\Entity\EmailProfile', $profile->setFromName($name));
        $this->assertInstanceOf('IceMarkt\Bundle\MainBundle\Entity\EmailProfile', $profile->setFromEmail($email));
        $this->assertInstanceOf('IceMarkt\Bundle\MainBundle\Entity\EmailProfile', $profile->setReplyToName($name));
        $this->assertInstanceOf('IceMarkt\Bundle\MainBundle\Entity\EmailProfile', $profile->setReplyToEmail($email));

        $this->assertEquals($name, $profile->getName());
        $this->assertEquals($name, $profile->getFromName());
        $this->assertEquals($email, $profile->getFromEmail());
        $this->assertEquals($name, $profile->getReplyToName());
        $this->assertEquals($email, $profile->getReplyToEmail());
    }
}
