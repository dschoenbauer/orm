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

use DSchoenbauer\Orm\Entity\HasUriCollection;
use DSchoenbauer\Orm\Entity\HasUriEntity;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Enum\ModelEvents;
use DSchoenbauer\Orm\Events\Persistence\Http\Methods\Delete;
use DSchoenbauer\Orm\Events\Persistence\Http\Methods\Get;
use DSchoenbauer\Orm\Events\Persistence\Http\Methods\Post;
use DSchoenbauer\Orm\Events\Persistence\Http\Methods\Put;
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
        $entity = $model->getEntity();
        if ($entity instanceof HasUriCollection) {
            $post = new Post([ModelEvents::CREATE], $entity->getUriCollectionMask());
            $model->accept($post->setClient($client));
            
            $get = new Get([ModelEvents::FETCH_ALL], $entity->getUriCollectionMask());
            $model->accept($get->setClient($client));
        }

        if ($entity instanceof HasUriEntity) {
            $get = new Get([ModelEvents::FETCH], $entity->getUriEntityMask());
            $model->accept($get->setClient($client));
            
            $put = new Put([ModelEvents::UPDATE], $entity->getUriEntityMask());
            $model->accept($put->setClient($client));
            
            $delete = new Delete([ModelEvents::DELETE], $entity->getUriEntityMask());
            $model->accept($delete->setClient($client));
        }
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
