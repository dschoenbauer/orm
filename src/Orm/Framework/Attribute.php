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
 * An abstract value to be assigned to a given model.
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
class Attribute
{

    private $value;

    /**
     * a value to be stored
     * @param mixed $value a value to be stored
     * @since v1.0.0
     */
    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    /**
     * retrieves a value to be stored
     * @return mixed a value stored
     * @since v1.0.0
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * sets a value to be stored
     * @param mixed $value a value to be stored
     * @return $this
     * @since v1.0.0
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
