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

use DSchoenbauer\Orm\Entity\HasPasswordInterface;
use DSchoenbauer\Orm\Events\AbstractModelEvent;
use DSchoenbauer\Orm\Events\Filter\PasswordMask\PasswordMaskStrategyInterface;
use DSchoenbauer\Orm\ModelInterface;

/**
 * Masks incoming passwords with the appropriate strategy
 */
class PasswordMask extends AbstractModelEvent
{

    public function getInterface()
    {
        return HasPasswordInterface::class;
    }

    public function execute(ModelInterface $model)
    {
        /* @var $entity HasPasswordInterface */
        $entity = $model->getEntity();
        $data = $model->getData();
        $field = $entity->getPasswordField();
        $model->setData($this->obfascateData($data, $field, $entity->getPasswordMaskStrategy()));
        return true;
    }

    public function obfascateData(array $data, $field, PasswordMaskStrategyInterface $passwordMasker)
    {
        if (array_key_exists($field, $data)) {
            $data[$field] = $passwordMasker->hashString($data[$field]);
        }
        return $data;
    }
}
