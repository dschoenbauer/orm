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
namespace DSchoenbauer\Orm\Events\Validate\Schema;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Enum\ModelAttributes;
use DSchoenbauer\Orm\Framework\Attribute;
use DSchoenbauer\Orm\Framework\AttributeCollection;
use DSchoenbauer\Orm\Model;
use PHPUnit\Framework\TestCase;

/**
 * Description of AliasUserCollection'
 *
 * @author David Schoenbauer
 */
class AliasUserCollectionTest extends TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new AliasUserCollection();
    }

    public function testAttribute()
    {
        $attribute = $this->getAttribute('test');
        $this->assertSame($attribute, $this->object->setAttribute($attribute)->getAttribute());
    }

    public function testGetFields()
    {
        $fields = $this->object->setAttribute($this->getAttribute('test'))->getFields(null);
        $this->assertEquals('test', $fields);
    }

    public function testGetTypeInterface()
    {
        $this->assertEquals(EntityInterface::class, $this->object->getTypeInterface());
    }

    public function testVisitModel()
    {
        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $attributeCollection = $this->getMockBuilder(AttributeCollection::class)->getMock();
        $attribute = $this->getAttribute('test');
        $attributeCollection
            ->expects($this->any())
            ->method('get')
            ->with(ModelAttributes::FIELD_ALIASES, [], AttributeCollection::BY_REF)
            ->willReturn($attribute);
        $model->expects($this->once())->method('getAttributes')->willReturn($attributeCollection);
        
        $this->assertNull($this->object->getAttribute());
        $this->object->visitModel($model);
        $this->assertSame($attribute, $this->object->getAttribute());
    }

    public function getAttribute($value)
    {
        $attribute = $this->getMockBuilder(Attribute::class)->getMock();
        $attribute->expects($this->any())->method('getValue')->willReturn($value);
        return $attribute;
    }
}
