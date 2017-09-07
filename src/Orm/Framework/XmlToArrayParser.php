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
namespace DSchoenbauer\Orm\Framework;

use DSchoenbauer\Orm\Exception\InvalidXmlException;

/**
 * Description of XmlToArrayParser
 *
 * @author David Schoenbauer
 */
class XmlToArrayParser
{

    public $array = [];
    public $parse_error = false;
    private $parser;
    private $pointer;

    /**
     * @param string $xml
     * @return $this
     */
    public function convert($xml)
    {
        $this->pointer = & $this->array;
        $this->parser = xml_parser_create("UTF-8");
        xml_set_object($this->parser, $this);
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($this->parser, "tagOpen", "tagClose");
        xml_set_character_data_handler($this->parser, "cdata");
        if (xml_parse($this->parser, ltrim($xml)) !== 1) {
            $message = xml_error_string(xml_get_error_code($this->parser));
            $column = xml_get_current_column_number($this->parser);
            $line = xml_get_current_line_number($this->parser);
            throw new InvalidXmlException($message, $column, $line);
        }
        xml_parser_free($this->parser);
        return $this->array;
    }

    private function tagOpen($parser, $tag, $attributes)
    {

        $this->convertToArray($tag, 'attrib');
        $idx = $this->convertToArray($tag, 'cdata');

        if (isset($idx)) {
            $this->pointer[$tag][$idx] = array('@idx' => $idx, '@parent' => &$this->pointer);
            $this->pointer = & $this->pointer[$tag][$idx];
        } else {
            $this->pointer[$tag] = array('@parent' => &$this->pointer);
            $this->pointer = & $this->pointer[$tag];
        }
        if (!empty($attributes)) {
            $this->pointer['attrib'] = $attributes;
        }
    }

    /**
     * Adds the current elements content to the current pointer[cdata] array.
     */
    private function cdata($parser, $cdata)
    {
        $this->pointer['cdata'] = trim($cdata);
    }

    private function tagClose($parser, $tag)
    {
        $current = & $this->pointer;
        if (isset($this->pointer['@idx'])) {
            unset($current['@idx']);
        }
        $this->pointer = & $this->pointer['@parent'];
        unset($current['@parent']);

        if (isset($current['cdata']) && count($current) == 1) {
            $current = $current['cdata'];
        } elseif (empty($current['cdata'])) {
            unset($current['cdata']);
        }
    }

    /**
     * Converts a single element item into array(element[0]) if a second element of the same name is encountered.
     */
    private function convertToArray($tag, $item)
    {
        /*if (isset($this->pointer[$tag][$item])) {
            $content = $this->pointer[$tag];
            $this->pointer[$tag] = array((0) => $content);
            $idx = 1;
        } else */
        if (isset($this->pointer[$tag])) {
            $idx = count($this->pointer[$tag]);
            if (!isset($this->pointer[$tag][0])) {
                foreach ($this->pointer[$tag] as $key => $value) {
                    unset($this->pointer[$tag][$key]);
                    $this->pointer[$tag][0][$key] = $value;
                }
            }
        } else {
            $idx = null;
        }
        return $idx;
    }
}
