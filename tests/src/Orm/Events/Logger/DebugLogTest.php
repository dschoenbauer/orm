<?php
/*
 * The MIT License
 *
 * Copyright 2018 David Schoenbauer.
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

namespace DSchoenbauer\Orm\Events\Logger;

use DSchoenbauer\Exception\Http\ClientError\NotFoundException;
use DSchoenbauer\Exception\Http\ServerError\ServerErrorException;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use PHPUnit\Framework\TestCase;
use Zend\EventManager\Event;

/**
 * Description of DebugLogTest
 *
 * @author David Schoenbauer
 */
class DebugLogTest extends TestCase
{
    /**
     * @var DebugLog
     */
    private $object;
    private $filePath;

    public function testIsEvent()
    {
        $this->assertInstanceOf(AbstractEvent::class, $this->object);
    }

    public function testFileExists()
    {
        $this->assertEquals($this->filePath, $this->object->setFile($this->filePath)->getFile());
    }
    public function testFilePathMissing(){
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Path: /dog not found");
        $this->object->setFile('/dog/notAFile.something');
    }

    public function testConstructor(){
        $event = new DebugLog(['events'],$this->filePath, EventPriorities::LATE);
        $this->assertEquals(['events'],$event->getEvents());
        $this->assertEquals($this->filePath,$event->getFile());
        $this->assertEquals(EventPriorities::LATE, $event->getPriority());
    }
    
    public function testOnExecute(){
        $event = new Event('test');
        $this->object->onExecute($event);
    }


    public function testLogAction(){
        $payload = ['test'=>true];
        $this->object->setFile($this->filePath);
        $this->assertFileNotExists($this->filePath);
        $this->object->logAction($payload);
        $this->assertEquals('[{"test":true}]', file_get_contents($this->filePath));
        $this->object->logAction($payload);
        $this->assertEquals('[{"test":true},{"test":true}]', file_get_contents($this->filePath));
        
    }
    
    public function testGetPayloadPlain(){
        $target = new \stdClass();
        $event = new Event('Janet', $target);
        $result = $this->object->getPayload($event);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('target', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertEquals($result['name'], 'Janet');
        $this->assertEquals($result['target'], $target);
        
    }
    
    public function testGetPayloadCallback(){
        $event = new Event('Bob');
        $callback = function($event){
            return $event;
        };
        $this->assertEquals($event, $this->object->setCallback($callback)->getPayload($event));
    }
        
    function testCallBack(){
        $lambda = function($event){};
        $this->assertEquals($lambda, $this->object->setCallback($lambda)->getCallback());
    }
    
    function testCallBackNotCallable(){
        $this->expectExceptionMessage("Code provided to debug log is not callable");
        $this->expectException(ServerErrorException::class);
        $lambda = [];
        $this->object->setCallback($lambda);        
    }

    protected function setUp()
    {
        $pathBits = ['..', '..', '..', '..', 'files', 'debugLog.js'];
        $this->filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathBits);
        $this->object = new DebugLog([],$this->filePath);
    }
    
    protected function tearDown()
    {
        file_put_contents($this->filePath, "[]");
        unlink($this->filePath);
    }
}
