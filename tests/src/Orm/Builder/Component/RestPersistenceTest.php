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

use DSchoenbauer\Orm\Builder\Component\RestPersistence;
use DSchoenbauer\Orm\Entity\HasUriCollection;
use DSchoenbauer\Orm\Entity\HasUriEntity;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Tests\Orm\Builder\Component\AbstractComponentTestCase;
use Zend\Http\Client;

/**
 * Description of RestPersistenceTest
 *
 * @author David Schoenbauer
 */
class RestPersistenceTest extends AbstractComponentTestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new RestPersistence();
    }

    public function testClientLazyLoad()
    {
        $this->assertInstanceOf(Client::class, $this->object->getClient());
    }

    public function testClient()
    {
        $client = $this->getMockBuilder(Client::class)->getMock();
        $this->assertSame($client, $this->object->setClient($client)->getClient());
    }

    public function testVisitModelHasUriCollection()
    {
        $this->evaluateVisitModel(HasUriCollection::class, 2, 'getUriCollectionMask');
    }

    public function testVisitModelHasUriEntity()
    {
        $this->evaluateVisitModel(HasUriEntity::class, 3, 'getUriEntityMask');
    }

    public function evaluateVisitModel($entityClass, $calls, $maskMethod)
    {
        $entity = $this->getMockBuilder($entityClass)->getMock();
        $entity->expects($this->exactly($calls))->method($maskMethod);

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->exactly($calls))->method('accept')->willReturnSelf();
        $model->expects($this->any())->method('getEntity')->willReturn($entity);
        $this->object->visitModel($model);
    }
}
