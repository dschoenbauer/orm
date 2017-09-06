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

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Entity\HasUriCollection;
use DSchoenbauer\Orm\Entity\HasUriEntity;
use DSchoenbauer\Orm\Entity\IsHttpInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractEvent;
use DSchoenbauer\Orm\Events\Persistence\Http\DataExtract\DataExtractorFactory;
use DSchoenbauer\Orm\Exception\HttpErrorException;
use DSchoenbauer\Orm\Framework\InterpolateTrait;
use DSchoenbauer\Orm\ModelInterface;
use Zend\EventManager\EventInterface;
use Zend\Http\Client;
use Zend\Http\Response;

/**
 * Description of AbstractHttpEvent
 * @deprecated since version 1.0.0
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
    
        $this->setClient($client);
        parent::__construct($events, $priority);
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
        if (!$this->validateModel($model, IsHttpInterface::class)) {
            return;
        }
        $this->run($model);
    }

    abstract public function run(ModelInterface $model);

    public function buildUri(ModelInterface $model)
    {
        $entity = $model->getEntity();
        if (!$entity instanceof EntityInterface || !$entity instanceof IsHttpInterface) {
            return;
        }
        $uri = $this->getUri($entity);
        return $this->interpolate($uri, $this->getData($model));
    }

    /**
     * Code is depreciated wired just to get it to work.
     * @param IsHttpInterface $entity
     * @return type
     */
    public function getUri(IsHttpInterface $entity)
    {
        if ($entity instanceof HasUriCollection) {
            return $entity->getUriCollectionMask();
        }
        return $entity->getUriEntityMask();
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
     * @param DataExtractorFactory $dataExtractorFactory
     * @return $this
     */
    public function setDataExtractorFactory($dataExtractorFactory)
    {
        $this->dataExtractorFactory = $dataExtractorFactory;
        return $this;
    }

    /**
     * Checks for errors returned in the response.
     * @param Response $response
     * @return Response
     * @throws HttpErrorException
     */
    public function checkForError(Response $response, array $successStatusCodes = [200, 202, 204])
    {
        if (in_array($response->getStatusCode(), $successStatusCodes)) {
            return $response;
        }
        throw new HttpErrorException($response->getBody(), $response->getStatusCode());
    }
}
