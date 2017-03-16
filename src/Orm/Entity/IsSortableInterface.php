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
 * provides a way to define whish fields of an entity are sortable
 * @author David Schoenbauer <d.schoenbauer@ctimeetingtech.com>
 */
interface IsSortableInterface
{
    const SORT_ASCENDING = "ASC";
    const SORT_DESCENDING = "DESC";
    
    /**
     * provides a list of fields that can be sorted
     * @return array an array of fields that can be sorted
     * @since v1.0.0
     */
    public function getSortFields();

    /**
     * provides a the default field to sort on
     * @return string if no field is provided by the user this field will define
     * which field should be sorted on
     * @since v1.0.0
     */
    public function getDefaultSortField();

    /**
     * defines a default sort order
     * @return string returns either IsSortableInterface::SORT_ASCENDING or IsSortableInterface::SORT_DESCENDING
     * @since v1.0.0
     */
    public function getDefaultSortDirection();
}
