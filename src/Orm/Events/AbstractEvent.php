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
namespace DSchoenbauer\Orm\Events;

use DSchoenbauer\Exception\Platform\InvalidArgumentException;
use DSchoenbauer\Exception\Platform\LogicException;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;
use Zend\EventManager\EventInterface;

/**
 * Allows for easier event attachment
 *
 * @author David Schoenbauer
 */
abstract class AbstractEvent implements VisitorInterface
{

    private $events = [];
    private $priority = EventPriorities::ON_TIME;

    public function __construct(array $events = [], $priority = EventPriorities::ON_TIME)
    {
        $this
            ->setEvents($events)
            ->setPriority($priority);
    }

    /**
     * provides an opportunity to extend Model's functionality
     * @param ModelInterface $model model with which to be listened to
     * @since v1.0.0
     */
    public function visitModel(ModelInterface $model)
    {
        foreach ($this->getEvents() as $event) {
            $model->getEventManager()->attach($event, [$this, 'onExecute']);
        }
    }

    abstract public function onExecute(EventInterface $event);

    /**
     * returns a list of event names that this object should be executed
     * @return array
     * @since v1.0.0
     * */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * defines a list of event names this object will listen for to execute
     * @param array $events array of event to be executed with
     * @return $this
     * @since v1.0.0
     */
    public function setEvents(array $events)
    {
        $this->events = $events;
        return $this;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    public function validateModel($model, $expectedEntity, $returnException = false)
    {
        if (!$model instanceof ModelInterface) {
            if ($returnException) {
                throw new InvalidArgumentException('ModelInterface is expected');
            } else {
                return false;
            }
        }
        if (!is_a($model->getEntity(), $expectedEntity)) {
            if ($returnException) {
                throw new LogicException("Entity must implement or extend $expectedEntity");
            } else {
                return false;
            }
        }
        return true;
    }
}
