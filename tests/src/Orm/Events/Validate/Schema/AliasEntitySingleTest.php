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

use DSchoenbauer\Orm\Entity\HasFieldAliases;
use DSchoenbauer\Orm\Model;
use PHPUnit\Framework\TestCase;

/**
 * Description of AliasEntitySingle
 *
 * @author David Schoenbauer
 */
class AliasEntitySingleTest extends TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = new AliasEntitySingle();
    }

    public function testApplyAlias()
    {
        $this->assertEquals(AliasEntitySingle::REMOVE_ALIAS, $this->object->getApplyAlias());
        $this->assertEquals(AliasEntitySingle::APPLY_ALIAS, $this->object->setApplyAlias(AliasEntitySingle::APPLY_ALIAS)->getApplyAlias());
    }

    public function testTypeInterface()
    {
        $this->assertEquals(HasFieldAliases::class, $this->object->getTypeInterface());
    }

    /**
     * @dataProvider getFieldsDataProvider
     * @param array $mappings
     * @param type $applyAlias
     * @param type $results
     */
    public function testGetFields($mappings, $applyAlias, $results)
    {
        $entity = $this->getMockBuilder(HasFieldAliases::class)->getMock();
        $entity->expects($this->once())->method('getFieldAliases')->willReturn($mappings);

        $this->assertEquals($results, $this->object->setApplyAlias($applyAlias)->getFields($entity));
    }

    public function getFieldsDataProvider()
    {
        return [
            "Remove Alias" => [['id_alias' => 'id_real', 'name_alias' => 'name_real'], AliasEntitySingle::REMOVE_ALIAS, ['id_alias' => 'id_real', 'name_alias' => 'name_real']],
            "Apply Alias" => [['id_alias' => 'id_real', 'name_alias' => 'name_real'], AliasEntitySingle::APPLY_ALIAS, ['id_real' => 'id_alias', 'name_real' => 'name_alias']]
        ];
    }

    /**
     * @dataProvider getAliasRowDataProvider
     * @param array $data
     * @param array $aliases
     * @param array $results
     */
    public function testAliasRow($data, $aliases, $results)
    {
        $this->assertEquals($results, $this->object->aliasRow($data, $aliases));
    }
    
    /**
     * @dataProvider getAliasRowDataProvider
     * @param type $data
     * @param type $aliases
     * @param type $results
     */
    public function testValidate($data, $aliases, $results){
        $model = $this->getMockBuilder(Model::class)->disableOriginalConstructor()->getMock();
        $model->expects($this->once())->method('setData')->willReturnCallback(function($data) {
            $this->_data = $data;
            return $this;
        });
        $model->expects($this->once())->method('getData')->willReturnCallback(function() {
            return $this->_data;
        });
        $this->object->setModel($model)->validate($data, $aliases);
        $this->assertEquals($results, $this->object->getModel()->getData());
    }

    public function getAliasRowDataProvider()
    {
        return [
        "No Alias" => [['id' => 1, 'name' => 'test', 'ack' => true], [], ['id' => 1, 'name' => 'test', 'ack' => true]],
        "Single Alias" => [['id' => 1, 'name' => 'test'], ['id' => 'idx'], ['idx' => 1, 'name' => 'test']],
        "All Alias" => [['id' => 1, 'name' => 'test'], ['id' => 'idx', 'name' => 'fullName'], ['idx' => 1, 'fullName' => 'test']],
        "No Data" => [[], ['id' => 'idx'], []],
        ];
    }
}
