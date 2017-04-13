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
namespace DSchoenbauer\Orm\Events\Framework;

use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\ModelInterface;
use Zend\EventManager\EventInterface;

/**
 * Description of CrossTrigger
 *
 * @author David Schoenbauer
 */
class CrossTrigger extends AbstractEvent
{

    private $targetEvents = [];

    public function __construct(array $events = [], array $targetEvents = [], $priority = EventPriorities::LATEST)
    {
        parent::__construct($events, $priority);
        $this->setTargetEvents($targetEvents);
    }

    public function onExecute(EventInterface $event)
    {
        $model = $event->getTarget();
        if (!$model instanceof ModelInterface) {
            return false;
        }
        $targetEvents = $this->getTargetEvents();
        foreach ($targetEvents as $targetEvent) {
            $model->getEventManager()->trigger($targetEvent, $model);
        }
        return true;
    }

    public function getTargetEvents()
    {
        return $this->targetEvents;
    }

    public function setTargetEvents(array $targetEvents)
    {
        $this->targetEvents = $targetEvents;
        return $this;
    }
}
