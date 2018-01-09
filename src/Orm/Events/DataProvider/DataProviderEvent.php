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
namespace DSchoenbauer\Orm\Events\DataProvider;

use DSchoenbauer\Orm\DataProvider\DataProviderInterface;
use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Enum\EventPriorities as EventPriority;
use DSchoenbauer\Orm\Events\AbstractEvent;
use Zend\EventManager\EventInterface;

/**
 * Description of DataProviderEvent
 *
 * @author David Schoenbauer
 */
class DataProviderEvent extends AbstractEvent
{

    private $dataProvider;
    
    public function __construct(array $events, DataProviderInterface $dataProvider, $priority = EventPriority::ON_TIME)
    {
        parent::__construct($events, $priority);
        $this->setDataProvider($dataProvider);
    }
    
    public function onExecute(EventInterface $event)
    {
        $model = $event->getTarget();
        if (!$this->validateModel($model, EntityInterface::class)) {
            return false;
        }
        $model->setData($this->getDataProvider()->getData());
        return true;
    }
    
    /**
     * @return DataProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    public function setDataProvider(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        return $this;
    }
}
