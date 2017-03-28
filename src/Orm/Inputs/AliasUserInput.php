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

use DSchoenbauer\Orm\Enum\ModelAttributes;
use DSchoenbauer\Orm\Enum\UserInputFields;
use DSchoenbauer\Orm\Model;
use DSchoenbauer\Orm\VisitorInterface;

/**
 * Responsible for acquiring and validating a users alias of fields
 *
 * @author David Schoenbauer
 */
class AliasUserInput implements VisitorInterface
{

    protected $field = null;
    protected $entity;

    public function __construct($field = UserInputFields::ALIAS)
    {
        $this->setField($field);
    }

    public function visitModel(Model $model)
    {
        $userFields = $this->getUsersInput($this->getField());
        $validatedFields = $this->filterAliases($userFields, $model->getEntity()->getAllFields());
        $model->getAttributes()->set(ModelAttributes::FIELD_ALIASES, $validatedFields);
    }

    public function getUsersInput($field)
    {
        return filter_input_array(INPUT_GET, $this->getValidationDefinition($field), true)[$field];
    }

    public function getValidationDefinition($field)
    {
        return [
            $field => [
                'filter' => FILTER_SANITIZE_STRING,
                'flags' => FILTER_REQUIRE_ARRAY | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_BACKTICK,
                'options' => []
            ]
        ];
    }

    public function filterAliases($userAliases, $validFields)
    {
        $verirfiedFields = array_intersect($userAliases, $validFields);
        $output = [];
        $pattern = '/[^a-z0-9\_]/i';
        foreach ($verirfiedFields as $verirfiedField => $verirfiedAlias) {
            $key = preg_replace($pattern, "", $verirfiedField);
            $value = preg_replace($pattern, "", $verirfiedAlias);
            $output[$key] = $value;
        }
        return $output;
    }

    public function getField()
    {
        return $this->field;
    }

    public
        function setField($field)
    {
        $this->field = $field;
        return $this;
    }
}
