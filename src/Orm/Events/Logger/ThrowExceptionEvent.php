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

use DSchoenbauer\Orm\Enum\EventParameters;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use Exception;
use Zend\EventManager\EventInterface;

/**
 * Description of ThrowExceptionEvent
 *
 * @author David Schoenbauer
 */
class ThrowExceptionEvent extends AbstractEvent
{

    const NO_EXCEPTION_MESSAGE = "Exception expected to be thrown but no exception provided";

    private $alwaysThrowException = false;
    
    public function __construct(array $events = [], $alwaysThrowException = false, $priority = EventPriorities::ON_TIME)
    {
        $this->setAlwaysThrowException($alwaysThrowException);
        parent::__construct($events, $priority);
    }

    public function onExecute(EventInterface $event)
    {
        if (!$event->getParam(EventParameters::EXCEPTION) instanceof Exception) {
            throw new Exception(static::NO_EXCEPTION_MESSAGE);
        }
        throw $event->getParam(EventParameters::EXCEPTION);
    }

    public function getAlwaysThrowException()
    {
        return $this->alwaysThrowException;
    }

    public function setAlwaysThrowException($alwaysThrowException = true)
    {
        $this->alwaysThrowException = $alwaysThrowException;
        return $this;
    }
}
