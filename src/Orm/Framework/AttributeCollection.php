<?php

/**
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

namespace DSchoenbauer\Orm\Framework;

/**
 * Description of Attributes
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class AttributeCollection {

    const BY_VALUE = "byValue";
    const BY_REF = "byRef";

    private $_attributes = [];

    public function set($key, $value) {
        $this->ensureKey($key)->_attributes[$key]->setValue($value);
        return $this;
    }

    /**
     * Retrieves a parameter if the parameter doesn't exist one is created with the default value
     * @param string $key a one word label for the value
     * @param type $defaultValue a value to use if the parameter is not present
     * @param string $type byRef or byValue : byRef will return the attribute object and byValue will return the attribute value
     * @return mixed type will define what is returned
     */
    public function get($key, $defaultValue = null, $type = self::BY_VALUE) {
        $attr = &$this->ensureKey($key, $defaultValue)->_attributes[$key];
        if ($type == self::BY_REF) {
            return $attr;
        }
        return $attr->getValue();
    }

    /**
     * Validates that the key is present and if it isn't it adds it in
     * @param type $key
     * @param type $value
     * @return $this
     */
    private function ensureKey($key, $value = null) {
        if (!$this->has($key)) {
            $this->_attributes[$key] = new Attribute($value);
        }
        return $this;
    }

    /**
     * Validates the existance of a previously set parameter
     * @param string $key
     * @return bool
     */
    public function has($key) {
        return array_key_exists($key, $this->_attributes);
    }

}
