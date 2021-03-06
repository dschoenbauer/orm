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
 * A collection of attributes that define a given object
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class AttributeCollection
{

    /**
     * returns the value that is housed inside an attribute
     */
    const BY_VALUE = "byValue";
    
    /**
     * gets the attribute object itself allowing for late binding to a value
     */
    const BY_REF = "byRef";

    private $attributes = [];

    /**
     * sets a parameter into the collection
     * @param string $key a key used to retrieve or identify a given value
     * @param mixed $value a value to be stored
     * @return $this
     */
    public function set($key, $value)
    {
        $this->ensureKey($key)->attributes[$key]->setValue($value);
        return $this;
    }

    /**
     * Retrieves a parameter if the parameter doesn't exist one is created with the default value
     * @param string $key a one word label for the value
     * @param mixed $defaultValue a value to use if the parameter is not present
     * @param string $type byRef or byValue : byRef will return the attribute
     * object and byValue will return the attribute value
     * @return mixed type will define what is returned
     * @since v1.0.0
     */
    public function get($key, $defaultValue = null, $type = self::BY_VALUE)
    {
        $attr = &$this->ensureKey($key, $defaultValue)->attributes[$key];
        if ($type == self::BY_REF) {
            return $attr;
        }
        return $attr->getValue();
    }

    /**
     * Validates that the key is present and if it isn't it adds it in
     * @param string $key a field that will allow to be retrieved later
     * @param mixed $value a value to be associated with the key
     * @return $this
     * @since v1.0.0
     */
    private function ensureKey($key, $value = null)
    {
        if (!$this->has($key)) {
            $this->attributes[$key] = new Attribute($value);
        }
        return $this;
    }

    /**
     * Validates the existence of a previously set parameter
     * @param string $key
     * @return bool
     * @since v1.0.0
     */
    public function has($key)
    {
        return array_key_exists($key, $this->attributes);
    }
}
