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
namespace DSchoenbauer\Orm\Events;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\ModelInterface;
use Zend\EventManager\EventInterface;

/**
 * Description of AbstracrModelEvents
 *
 * @author David Schoenbauer
 */
abstract class AbstractModelEvent extends AbstractEvent
{

    private $event;

    public function getInterface()
    {
        return EntityInterface::class;
    }

    public function onExecute(EventInterface $event)
    {
        $model = $this->setEvent($event)->getEvent()->getTarget();
        if (!$this->validateModel($model, $this->getInterface())) {
            return false;
        }
        return $this->execute($model);
    }

    abstract public function execute(ModelInterface $model);

    /**
     * Provides the source event that triggered
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
