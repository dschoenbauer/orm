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
namespace DSchoenbauer\Orm\Events\Filter;

use DSchoenbauer\DotNotation\ArrayDotNotation;
use DSchoenbauer\Orm\Entity\MassMappingInterface;
use DSchoenbauer\Orm\Enum\EventPriorities;
use DSchoenbauer\Orm\Events\AbstractModelEvent;
use DSchoenbauer\Orm\ModelInterface;

/**
 * Description of MassMapper
 *
 * @author David Schoenbauer
 */
class MassMapper extends AbstractModelEvent
{

    const MAPPING_IN = 'in';
    const MAPPING_OUT = 'out';

    private $mappingDirection = self::MAPPING_IN;

    public function __construct(
        array $events = array(),
        $mappingDirection = self::MAPPING_IN,
        $priority = EventPriorities::ON_TIME
    ) {
    
        parent::__construct($events, $priority);
        $this->setMappingDirection($mappingDirection);
    }

    public function getInterface()
    {
        return MassMappingInterface::class;
    }

    public function execute(ModelInterface $model)
    {
        $entity = $model->getEntity();
        $model->setData($this->mapData($entity->getMapping(), $model->getData()));
        return true;
    }

    public function mapData($mapping, $data)
    {
        $arrayDotIn = new ArrayDotNotation($data);
        $arrayDotout = new ArrayDotNotation([]);
        if ($this->getMappingDirection() == self::MAPPING_OUT) {
            $mapping = array_flip($mapping);
        }
        foreach ($mapping as $key => $value) {
            $arrayDotout->set($value, $arrayDotIn->get($key));
        }
        return $arrayDotout->getData();
    }

    public function getMappingDirection()
    {
        return $this->mappingDirection;
    }

    public function setMappingDirection($mappingDirection)
    {
        $this->mappingDirection = $mappingDirection;
        return $this;
    }
}
