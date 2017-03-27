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
use DSchoenbauer\Orm\Events\Validate\AbstractValidate;

/**
 * Description of AliasEntity
 *
 * @author David Schoenbauer
 */
class AliasEntitySingle extends AbstractValidate
{

    const APPLY_ALIAS = true;
    const REMOVE_ALIAS = false;

    protected $applyAlias = self::REMOVE_ALIAS;

    public function __construct(array $events = array(), $applyAlias = self::REMOVE_ALIAS)
    {
        $this->setApplyAlias($applyAlias);
        parent::__construct($events);
    }

    public function getFields($entity)
    {
        /* @var $entity HasFieldAliases */
        if ($this->getApplyAlias() === self::REMOVE_ALIAS) {
            return $entity->getFieldAliases();
        }
        return array_flip($entity->getFieldAliases());
    }

    public function getTypeInterface()
    {
        return HasFieldAliases::class;
    }

    public function validate(array $data, array $fields)
    {
        $this->getModel()->setData($this->aliasRow($data, $fields));
    }

    public function aliasRow(array $data, array $aliases)
    {
        $output = [];
        foreach ($data as $key => $value) {
            $fieldKey = array_key_exists($key, $aliases) ? $aliases[$key] : $key;
            $output[$fieldKey] = $value;
        }
        return $output;
    }

    public function getApplyAlias()
    {
        return $this->applyAlias;
    }

    public function setApplyAlias($applyAlias)
    {
        $this->applyAlias = $applyAlias;
        return $this;
    }
}
