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

use DSchoenbauer\Orm\Model;
use DSchoenbauer\Orm\VisitorInterface;
use Zend\EventManager\Event;

/**
 * Allows for easier event attachment
 *
 * @author David Schoenbauer
 */
abstract class AbstractEvent implements VisitorInterface
{

    private $events = [];

    /**
     * provides an opportunity to extend Model's functionality
     * @param Model $model model with which to be listened to
     * @since v1.0.0
     */
    public function visitModel(Model $model)
    {
        foreach ($this->getEvents() as $event) {
            $model->getEventManager()->attach($event, [$this, 'onExecute']);
        }
    }

    abstract public function onExecute(Event $event);

    /**
     * returns a list of event names that this object should be executed
     * @return array
     * @since v1.0.0
     **/
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
}
