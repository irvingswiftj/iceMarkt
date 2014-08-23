<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 23/08/2014
 * Time: 09:40
 */

namespace IceMarkt\Bundle\MainBundle\Tests\Entity;


use IceMarkt\Bundle\MainBundle\Entity\SpreadSheet;

class SpreadSheetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function gettingEmailColumnIndexExpectedResult()
    {
        $spreadsheet = new SpreadSheet();
        $testRow = array(
            0 => 'blah',
            1 => 'blah',
            2 => 'blah',
            3 => 'Email',
            4 => 'blah',
        );

        $this->assertEquals(3, $spreadsheet->getEmailColumnIndex($testRow));
    }

    /**
     * @test i expect that this will return the first instance of an email column
     */
    public function gettingEmailColumnIndexWithTwoEmailColumns()
    {
        $spreadsheet = new SpreadSheet();
        $testRow = array(
            0 => 'blah',
            1 => 'email address',
            2 => 'blah',
            3 => 'Email',
            4 => 'blah',
        );

        $this->assertEquals(1, $spreadsheet->getEmailColumnIndex($testRow));
    }

    /**
     * @test
     */
    public function gettingEmailColumnIndexByEmailAddress()
    {
        $spreadsheet = new SpreadSheet();
        $testRow = array(
            0 => 'blah',
            1 => 'test@gmail.com',
            2 => 'blah',
            3 => 'Email',
            4 => 'blah',
        );

        $this->assertEquals(1, $spreadsheet->getEmailColumnIndex($testRow));
    }

    /**
     * @test
     */
    public function gettingEmailColumnIndexWithNoResult()
    {
        $spreadsheet = new SpreadSheet();
        $testRow = array(
            0 => 'blah',
            1 => 'blah',
            2 => 'blah',
            3 => 'blah',
            4 => 'blah',
        );

        $this->assertEquals(-1, $spreadsheet->getEmailColumnIndex($testRow));
    }

    /**
     * @test
     */
    public function gettingEmailColumnIndexFromEmptyData()
    {
        $spreadsheet = new SpreadSheet();
        $testRow = array();

        $this->assertEquals(-1, $spreadsheet->getEmailColumnIndex($testRow));
    }


    /**
     * @test
     */
    public function checkingForEmailColumn()
    {
        $method = self::getPrivateMethod('isEmailColumn');
        $entityClass = new SpreadSheet();
        $this->assertTrue($method->invokeArgs($entityClass, array('Email')));
        $this->assertTrue($method->invokeArgs($entityClass, array('email')));
        $this->assertTrue($method->invokeArgs($entityClass, array('Email Address')));
        $this->assertTrue($method->invokeArgs($entityClass, array('email_addreSS')));
        $this->assertFalse($method->invokeArgs($entityClass, array('rubbish')));
        $this->assertFalse($method->invokeArgs($entityClass, array('more rubbish @')));
        $this->assertTrue($method->invokeArgs($entityClass, array('myTestEmail@hotmail.com')));

    }

    /**
     * @test
     */
    public function checkingForFirstNameColumn()
    {
        $method = self::getPrivateMethod('isFirstNameColumn');
        $entityClass = new SpreadSheet();
        $this->assertTrue($method->invokeArgs($entityClass, array('fname')));
        $this->assertTrue($method->invokeArgs($entityClass, array('Fname')));
        $this->assertTrue($method->invokeArgs($entityClass, array('first name')));
        $this->assertTrue($method->invokeArgs($entityClass, array('firstname')));
    }

    /**
     * @test
     */
    public function checkingForLastNameColumn()
    {
        $method = self::getPrivateMethod('isLastNameColumn');
        $entityClass = new SpreadSheet();
        $this->assertTrue($method->invokeArgs($entityClass, array('lname')));
        $this->assertTrue($method->invokeArgs($entityClass, array('Lname')));
        $this->assertTrue($method->invokeArgs($entityClass, array('last name')));
        $this->assertTrue($method->invokeArgs($entityClass, array('surname')));
    }

    /**
     * @test
     */
    public function checkingForNameColumn()
    {
        $method = self::getPrivateMethod('isNameColumn');
        $entityClass = new SpreadSheet();
        $this->assertTrue($method->invokeArgs($entityClass, array('Name')));
        $this->assertTrue($method->invokeArgs($entityClass, array('Full Name')));
    }

    /**
     * @param $methodName
     * @return \ReflectionMethod
     */
    protected static function getPrivateMethod($methodName)
    {
        $class = new \ReflectionClass('IceMarkt\Bundle\MainBundle\Entity\SpreadSheet');
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }
}
