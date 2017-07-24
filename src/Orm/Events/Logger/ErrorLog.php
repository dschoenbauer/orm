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
namespace DSchoenbauer\Orm\Events\Logger;

use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Framework\InterpolateTrait;
use DSchoenbauer\Orm\ModelInterface;
use Exception;
use Zend\EventManager\EventInterface;

/**
 * Description of ErrorLog
 *
 * @author David Schoenbauer
 */
class ErrorLog extends AbstractEvent
{

    const KEY_SUCCESS = 'success';
    const KEY_EVENT = 'event';
    const KEY_MESSAGE = 'message';

    use InterpolateTrait;

    public function onExecute(EventInterface $event)
    {
        $model = $event->getTarget();
        $exception = $event->getParam('exception', null);
        $event = $event->getParam('event', null);
        if (!$model instanceof ModelInterface || !$exception instanceof Exception) {
            return false;
        }
        $model->setData([
            self::KEY_SUCCESS => false,
            self::KEY_EVENT => $event,
            self::KEY_MESSAGE => $exception->getMessage(),
        ]);
        return true;
    }
}