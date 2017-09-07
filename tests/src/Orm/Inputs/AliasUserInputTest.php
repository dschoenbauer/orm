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
namespace DSchoenbauer\Orm\Inputs;

use DSchoenbauer\Orm\Entity\EntityInterface;
use DSchoenbauer\Orm\Enum\ModelAttributes;
use DSchoenbauer\Orm\Framework\AttributeCollection;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;
use PHPUnit\Framework\TestCase;

/**
 * This is not cool. I know it. But I need to test the other lines of code.
 */
function filter_input_array($type, $definition, $array)
{
    return array_intersect_key($_GET, $definition);
}

/**
 * Responsible for acquiring and validating a users alias of fields
 *
 * @author David Schoenbauer
 */
class AliasUserInputTest extends TestCase
{

    protected $object;

    protected function setUp()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_REQUEST = $GLOBALS['_GET'] = $_GET = [
            'alias' => [
                'id' => 'idx'
            ]
        ];
        $this->object = new AliasUserInput();
    }

    public function testField()
    {
        $field = 'test';
        $this->assertEquals($field, $this->object->setField($field)->getField());
    }
    
    public function testInterface(){
        $this->assertInstanceOf(VisitorInterface::class, $this->object);
    }

    /**
     * Super important... Test with no rooms for error
     * @dataProvider getValidationDefinitionDataProvider
     */
    public function testGetValidationDefinition($field, $result)
    {
        $this->assertEquals($result, $this->object->getValidationDefinition($field));
    }

    public function getValidationDefinitionDataProvider()
    {
        $mock = [
            'filter' => FILTER_SANITIZE_STRING,
            'flags' => FILTER_REQUIRE_ARRAY | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK,
            'options' => []
        ];

        return [
            ['test1', ['test1' => $mock]],
            ['test2', ['test2' => $mock]],
            ['test3', ['test3' => $mock]],
            ['test4', ['test4' => $mock]],
            ['test5', ['test5' => $mock]],
        ];
    }

    public function testGetUsersInput()
    {
        $this->markAsRisky();
        $this->assertEquals(['id' => 'idx'], $this->object->getUsersInput('alias', ['id']));
    }

    public function getFilterAliasesDataProvider()
    {
        return [
            "All Fields Allowed" => [['id' => 'idx', 'name' => 'namex'], ['idx', 'namex'], ['id' => 'idx', 'name' => 'namex']],
            "One Field Not Allowed" => [['id' => 'idx', 'name' => 'namex'], ['idx'], ['id' => 'idx']],
            "No Field Allowed" => [['id' => 'idx', 'namex' => 'name'], ['id'], []],
            "Bad Fields Characters" => [['`iDx` or 1=1 --' => 'idx'], ['idx'], ['iDxor11'=>'idx']],
        ];
    }

    /**
     * @dataProvider getFilterAliasesDataProvider
     * @param type $userAliases
     * @param type $validFields
     * @param type $results
     */
    public function testFilterAliases($userAliases, $validFields, $results)
    {
        $this->assertEquals($results, $this->object->filterAliases($userAliases, $validFields));
    }

    public function testVisitModel()
    {
        $attributes = $this->getMockBuilder(AttributeCollection::class)->getMock();
        $attributes->expects($this->once())->method('set')->with(ModelAttributes::FIELD_ALIASES, ['id' => 'idx']);
        
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $entity->expects($this->once())->method('getAllFields')->willReturn(['idx']);

        $model = $this->getMockBuilder(ModelInterface::class)->getMock();
        $model->expects($this->once())->method('getAttributes')->willReturn($attributes);
        $model->expects($this->once())->method('getEntity')->willReturn($entity);

        $this->object->visitModel($model);
    }
}
