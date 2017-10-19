<?php
/*
 * The MIT License
 *
 * Copyright 2017 David Schoenbauer.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace DSchoenbauer\Orm\Events\Filter\PasswordMask;

use PHPUnit\Framework\TestCase;

/**
 * Description of Sha1PasswordStrategyTest
 *
 * @author David Schoenbauer
 */
class Sha1PasswordStrategyTest extends TestCase
{

    private $object;

    protected function setUp()
    {
        $this->object = new Sha1PasswordStrategy();
    }

    public function testHasProperInterface()
    {
        $this->assertInstanceOf(PasswordMaskStrategyInterface::class, $this->object);
    }

    /**
     * 
     * @dataProvider generalDataProvider
     */
    public function testHashString($password)
    {
        $this->assertEquals(sha1($password), $this->object->hashString($password));
    }

    /**
     * 
     * @dataProvider generalDataProvider
     */
    public function testValidate($password)
    {
        $this->assertTrue($this->object->validate($password, sha1($password)));
        $this->assertFalse($this->object->validate($password, sha1($password . 'salt?')));
    }

    public function testSalt()
    {
        $this->assertEquals('test', $this->object->setSalt('test')->getSalt());
    }

    /**
     * @dataProvider generalDataProvider
     * @param type $password
     */
    public function testSaltedHashString($password)
    {
        $salt = 'salt';
        $this->assertEquals(sha1($salt . $password), $this->object->setSalt($salt)->hashString($password));
    }

    /**
     * @dataProvider generalDataProvider
     * @param type $password
     */
    public function testSaltedValidate($password)
    {
        $salt = 'salt';
        $this->assertFalse($this->object->setSalt($salt)->validate($password, sha1($password)));
        $this->assertTrue($this->object->setSalt($salt)->validate($password, sha1($salt . $password)));
    }

    /**
     * Top 25 passwords of 2016
     */
    public function generalDataProvider()
    {
        return [
            ['1234567890'],
            ['123456789'],
            ['123456'],
            ['qwerty'],
            ['12345678'],
            ['111111'],
            ['1234567'],
            ['password'],
            ['123123'],
            ['987654321'],
            ['qwertyuiop'],
            ['mynoob'],
            ['123321'],
            ['666666'],
            ['18atcskd2w'],
            ['7777777'],
            ['1q2w3e4r'],
            ['654321'],
            ['555555'],
            ['3rjs1la7qe'],
            ['google'],
            ['1q2w3e4r5t'],
            ['123qwe'],
            ['zxcvbnm'],
            ['1q2w3e']];
    }
}
