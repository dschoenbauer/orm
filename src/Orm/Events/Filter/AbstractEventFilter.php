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
namespace DSchoenbauer\Orm\Events\Filter;

use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\ModelInterface;
use Zend\EventManager\EventInterface;

/**
 * Description of AbstractEventFilter
 *
 * @author David Schoenbauer
 */
abstract class AbstractEventFilter extends AbstractEvent
{

    protected $model;
    protected $event;

    /**
     * The event called when the event is triggered
     * @param EventInterface $event
     * @return boolean
     */
    public function onExecute(EventInterface $event)
    {
        $this->setEvent($event);
        if (!$event->getTarget() instanceof ModelInterface) {
            return false;
        }
        $this->setModel($event->getTarget());
        $this->getModel()->setData($this->filter($this->getModel()->getData()));
        return true;
    }

    /**
     * Takes the data from the model and allows for modification of the data
     * @return array Modified data
     */
    abstract public function filter(array $data);

    /**
     * Model that houses the data the filter modifies
     * @return ModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * sets the model the filter will work with
     * @param ModelInterface $model
     * @return $this
     */
    public function setModel(ModelInterface $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Provides event that triggered the filter to fire
     * @return EventInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Sets event that triggered the filter to fire
     * @param EventInterface $event
     * @return $this
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
        return $this;
    }
}
