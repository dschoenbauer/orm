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
namespace DSchoenbauer\Orm\Entity;

/**
 *
 * @author David Schoenbauer
 */
interface MassMappingInterface
{

    /**
     * a dot notation key that indicates where to get the data from
     */
    const MAPPING_KEY_FROM = 'from';
    
    /**
     * a dot notation key that indicates where to place data
     */
    const MAPPING_KEY_TO = 'to';
    
    /**
     * When data is being mapped to -> from then this will be applied as the filter
     */
    const MAPPING_TO_FILTER = 'toFilter';
    
    /**
     * When data is being mapped from -> to then this will be applied as the filter
     */
    const MAPPING_FROM_FILTER = 'fromFilter';
    
    /**
     * When data is being mapped to -> from then a custom function will be applied to the data
     */
    const MAPPING_TO_MAPPER = 'toMapper';
    
    /**
     * When data is being mapped from -> to then a custom function will be applied to the data
     */
    const MAPPING_FROM_MAPPER = 'fromMapper';

    /**
     * An array of mappings. Uses an array of arrays with an array that has specific keys will specific purposes
     * @return array
     */
    public function getMapping();
}
