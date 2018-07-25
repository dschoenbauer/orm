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
use Zend\EventManager\EventInterface;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

/**
 * Description of DebugLog
 *
 * @author David Schoenbauer
 */
class DebugLog extends AbstractEvent
{

    private $file;
    private $callback = null;

    public function __construct(array $events, $logFile, $priority = EventPriorities::ON_TIME, $payloadCallback = null)
    {
        $this->setFile($logFile)->setCallback($payloadCallback);
        parent::__construct($events, $priority);
    }

    public function onExecute(EventInterface $event)
    {
        $this->logAction($this->getPayload($event));
        return true;
    }

    public function logAction($payload)
    {
        $data = [];
        if (file_exists($this->getFile())) {
            $data = json_decode(file_get_contents($this->getFile()), true) ?: [];
        }
        $data[] = $payload;
        file_put_contents($this->getFile(), json_encode($data));
    }

    public function getPayload(EventInterface $event)
    {
        $callBack = $this->getCallback();
        if (is_callable($callBack)) {
            return $callBack($event);
        }
        return ['name' => $event->getName(), 'target' => $event->getTarget(), 'timestamp' => time()];
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     * @return DebugLog
     */
    public function setFile($file)
    {

        if (!is_writable(dirname($file))) {
            throw new NotFoundException("Path: " . dirname($file) . " not found");
        }
        $this->file = $file;
        return $this;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback($callback)
    {
        if ($callback !== null && !is_callable($callback)) {
            throw new ServerErrorException('Code provided to debug log is not callable');
        }
        $this->callback = $callback;
        return $this;
    }
}
