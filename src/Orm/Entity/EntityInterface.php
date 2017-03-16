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
namespace DSchoenbauer\Orm\Entity;

/**
 * An entity is an object that contains specific information for a given model type
 *
 * @author David Schoenbauer <dschoenbauer@gmail.com>
 */
interface EntityInterface
{

    /**
     * provides a name that can be used to reference the entity
     * @return string
     * @since v1.0.0
     */
    public function getName();

    /**
     * provides field with primary key
     * @return string
     * @since v1.0.0
     */
    public function getIdField();

    /**
     * provides which table the entities data is stored in
     * @return string
     * @since v1.0.0
     */
    public function getTable();

    /**
     * provides an array of all fields the entity has
     * @return array
     * @since v1.0.0
     */
    public function getAllFields();
}
