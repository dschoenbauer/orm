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
namespace DSchoenbauer\Orm\Events\Persistence\Http;

use DSchoenbauer\Orm\Entity\IsHttpInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Events\Persistence\Http\DataExtract\DataExtractorFactory;
use DSchoenbauer\Orm\Framework\InterpolateTrait;
use DSchoenbauer\Orm\ModelInterface;
use Zend\EventManager\EventInterface;
use Zend\Http\Client;

/**
 * Description of AbstractHttpEvent
 *
 * @author David Schoenbauer
 */
abstract class AbstractHttpEvent extends AbstractEvent
{

    private $client;
    protected $method;
    protected $dataExtractorFactory;

    use InterpolateTrait;

    public function __construct(
        array $events = array(),
        $priority = EventPriorities::ON_TIME,
        Client $client = null,
        $method = null
    ) {
    
        $this->setClient($client, $priority);
        parent::__construct($events);
        if ($method) {
            $this->setMethod($method);
        }
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->setClient(new Client());
        }
        return $this->client;
    }

    public function setClient(Client $client = null)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Selects data from an API and adds it to our model
     * @param EventInterface $event
     */
    public function onExecute(EventInterface $event)
    {
        /* @var $model ModelInterface */
        $model = $event->getTarget();
        if (!$model instanceof ModelInterface) {
            return;
        }
        /* @var $entity IsHttp */
        $entity = $model->getEntity();
        if (!$entity instanceof IsHttpInterface) {
            return;
        }
        $this->run($model);
    }

    abstract public function run(ModelInterface $model);

    public function buildUri(ModelInterface $model)
    {
        return $this->interpolate($this->getUri($model->getEntity()), $this->getData($model));
    }

    public function getUri(IsHttpInterface $entity)
    {
        return $entity->getEntityUrl();
    }

    public function getData(ModelInterface $model)
    {
        $data = $model->getData();
        $data[$model->getEntity()->getIdField()] = $model->getId();
        return $data;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     *
     * @return DataExtractorFactory
     */
    public function getDataExtractorFactory()
    {
        if ($this->dataExtractorFactory === null) {
            $this->setDataExtractorFactory(new DataExtractorFactory());
        }
        return $this->dataExtractorFactory;
    }

    /**
     * @param type $dataExtractorFactory
     * @return $this
     */
    public function setDataExtractorFactory($dataExtractorFactory)
    {
        $this->dataExtractorFactory = $dataExtractorFactory;
        return $this;
    }
}
