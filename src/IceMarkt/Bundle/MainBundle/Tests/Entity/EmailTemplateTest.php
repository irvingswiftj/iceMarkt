<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 14/08/2014
 * Time: 11:52
 */

namespace IceMarkt\Bundle\MainBundle\Tests\Entity;

use IceMarkt\Bundle\MainBundle\Entity\EmailTemplate;

class EmailTemplateTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test setters and getters
     */
    public function settersAndGetters()
    {
        $emailTemplate = new EmailTemplate();

        $template = '<h1>Example HTML Twig Template</h1><p>Hello {{ first_name }}</p>';
        $subject = 'test subject';
        $name = 'testName';
        $id = 12345;

        //test setters
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\EmailTemplate',
            $emailTemplate->setTemplate($template)
        );
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\EmailTemplate',
            $emailTemplate->setSubject($subject)
        );
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\EmailTemplate',
            $emailTemplate->setFormat(EmailTemplate::HTML_HEADER)
        );
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\EmailTemplate',
            $emailTemplate->setId($id)
        );
        $this->assertInstanceOf(
            'IceMarkt\Bundle\MainBundle\Entity\EmailTemplate',
            $emailTemplate->setName($name)
        );

        //test getters
        $this->assertEquals($template, $emailTemplate->getTemplate());
        $this->assertEquals($subject, $emailTemplate->getSubject());
        $this->assertEquals(EmailTemplate::HTML_HEADER, $emailTemplate->getFormat());
        $this->assertEquals($id, $emailTemplate->getId());
        $this->assertEquals($name, $emailTemplate->getName());
    }
}
