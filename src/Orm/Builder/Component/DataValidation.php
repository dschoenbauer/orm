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
use DSchoenbauer\Orm\Events\Filter\DefaultValue;
use DSchoenbauer\Orm\Events\Filter\RemoveId;
use DSchoenbauer\Orm\Events\Filter\ValidFields;
use DSchoenbauer\Orm\Events\Validate\DataType\DataTypeBoolean;
use DSchoenbauer\Orm\Events\Validate\DataType\DataTypeDate;
use DSchoenbauer\Orm\Events\Validate\DataType\DataTypeNumber;
use DSchoenbauer\Orm\Events\Validate\DataType\DataTypeString;
use DSchoenbauer\Orm\Events\Validate\Schema\RequiredFields;
use DSchoenbauer\Orm\ModelInterface;
use DSchoenbauer\Orm\VisitorInterface;

/**
 * Description of DataValidation
 *
 * @author David Schoenbauer
 */
class DataValidation implements VisitorInterface
{

    public function visitModel(ModelInterface $model)
    {
        $model
            ->accept(new RemoveId([ModelEvents::CREATE, ModelEvents::UPDATE], EventPriorities::EARLY))
            ->accept(new DefaultValue([ModelEvents::CREATE], EventPriorities::EARLY))
            ->accept(new ValidFields([ModelEvents::CREATE, ModelEvents::UPDATE], EventPriorities::EARLY))
            ->accept(new RequiredFields([ModelEvents::CREATE, ModelEvents::UPDATE], EventPriorities::EARLY))
            
            ->accept(new DataTypeBoolean([ModelEvents::CREATE, ModelEvents::UPDATE], EventPriorities::EARLY))
            ->accept(new DataTypeDate([ModelEvents::CREATE, ModelEvents::UPDATE], EventPriorities::EARLY))
            ->accept(new DataTypeNumber([ModelEvents::CREATE, ModelEvents::UPDATE], EventPriorities::EARLY))
            ->accept(new DataTypeString([ModelEvents::CREATE, ModelEvents::UPDATE], EventPriorities::EARLY));
    }
}
