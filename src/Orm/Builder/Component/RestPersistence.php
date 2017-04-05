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
namespace DSchoenbauer\Orm\Builder\Component;

use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Events\Persistence\Http\Create;
use DSchoenbauer\Orm\Events\Persistence\Http\Delete;
use DSchoenbauer\Orm\Events\Persistence\Http\Select;
use DSchoenbauer\Orm\Events\Persistence\Http\SelectAll;
use DSchoenbauer\Orm\Events\Persistence\Http\Update;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;
use Zend\Http\Client;

/**
 * Description of RestPersistence
 *
 * @author David Schoenbauer
 */
class RestPersistence implements VisitorInterface
{
    protected $client;

    public function __construct(Client $client = null)
    {
        $this->setClient($client);
    }
    

    public function visitModel(ModelInterface $model)
    {
        $client = $this->getClient();
        $model->accept(new Create([ModelEvents::CREATE], EventPriorities::ON_TIME, $client));
        $model->accept(new Select([ModelEvents::FETCH], EventPriorities::ON_TIME, $client));
        $model->accept(new SelectAll([ModelEvents::FETCH_ALL], EventPriorities::ON_TIME, $client));
        $model->accept(new Update([ModelEvents::UPDATE], EventPriorities::ON_TIME, $client));
        $model->accept(new Delete([ModelEvents::DELETE], EventPriorities::ON_TIME, $client));
    }
    
    /**
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client instanceof Client) {
            $this->setClient(new Client());
        }
        return $this->client;
    }

    public function setClient(Client $client = null)
    {
        $this->client = $client;
        return $this;
    }
}
