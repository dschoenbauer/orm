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
namespace DSchoenbauer\Orm\Events\Persistence\Http\Methods;

use DSchoenbauer\Orm\Entity\IsHttpInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractModelEvent;
use DSchoenbauer\Orm\Events\Persistence\Http\Client\ClientVisiteeInterface;
use DSchoenbauer\Orm\Events\Persistence\Http\Client\ClientVisitorInterface;
use DSchoenbauer\Orm\Events\Persistence\Http\DataExtract\DataExtractorFactory;
use DSchoenbauer\Orm\Exception\HttpErrorException;
use DSchoenbauer\Orm\Framework\InterpolateTrait;
use DSchoenbauer\Orm\ModelInterface;
use Zend\Http\Client;
use Zend\Http\Response;

/**
 * Description of AbstractHttpMethodEvent
 *
 * @author David Schoenbauer
 */
abstract class AbstractHttpMethodEvent extends AbstractModelEvent implements ClientVisiteeInterface
{

    use InterpolateTrait;

    private $dataExtractorFactory;
    private $client;
    private $uriMask;

    public function __construct(array $events, $uriMask, $priority = EventPriorities::ON_TIME)
    {
        $this->setUriMask($uriMask);
        parent::__construct($events, $priority);
    }

    public function getInterface()
    {
        return IsHttpInterface::class;
    }

    public function execute(ModelInterface $model)
    {
        if ($model->getEntity() instanceof ClientVisitorInterface) {
            $this->accept($model->getEntity());
        }
        $this->setUp($model);
        return $this->send($model);
        ;
    }
    
    public function accept(ClientVisitorInterface $visitor)
    {
        $visitor->visitClient($this->getClient());
    }

    protected function setUp(ModelInterface $model)
    {
        $this->getClient()
            ->setUri($this->getUri($model->getData()))
            ->setMethod($this->getMethod());
    }

    abstract public function send(ModelInterface $model);
    
    abstract public function getMethod();

    public function getDataExtractorFactory()
    {
        if (!$this->dataExtractorFactory instanceof DataExtractorFactory) {
            $this->setDataExtractorFactory(new DataExtractorFactory());
        }
        return $this->dataExtractorFactory;
    }

    public function setDataExtractorFactory(DataExtractorFactory $dataExtractorFactory)
    {
        $this->dataExtractorFactory = $dataExtractorFactory;
        return $this;
    }

    public function getClient()
    {
        if (!$this->client instanceof Client) {
            $this->setClient(new Client());
        }
        return $this->client;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    public function getUriMask()
    {
        return $this->uriMask;
    }

    public function setUriMask($uriMask)
    {
        $this->uriMask = $uriMask;
        return $this;
    }

    public function getUri($data = [])
    {
        return $this->interpolate($this->getUriMask(), $data);
    }

    /**
     * Checks for errors returned in the response.
     * @param Response $response
     * @return Response
     * @throws HttpErrorException
     */
    public function checkForError(Response $response)
    {
        if ($response->isSuccess()) {
            return $response;
        }
        throw new HttpErrorException($response->getBody(), $response->getStatusCode());
    }
}
