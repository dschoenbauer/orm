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

use PHPUnit\Framework\TestCase;

/**
 * Description of InterpolateTraitTest
 *
 * @author David Schoenbauer
 */
class InterpolateTraitTest extends TestCase
{

    protected $object;

    protected function setUp()
    {
        $this->object = $this->getMockForTrait(InterpolateTrait::class);
    }

    /**
     * @dataProvider dataProvider
     * @param type $message
     * @param type $context
     * @param type $result
     */
    public function testInterpolate($message, $context, $result)
    {
        $this->assertEquals($result, $this->object->interpolate($message, $context));
    }

    public function dataProvider()
    {
        return [
            "Golden Path" => ["This {a} {is} test", ['is' => 'a', 'a' => 'is'], "This is a test"],
            "No Match" => ["This is {id}", ['is' => 'a', 'a' => 'is'], "This is {id}"],
            "Nested Right" => ["{message}", ['message' => 'embedded {id}', 'id' => 1], "embedded {id}"],
            "Nested Left" => ["{message}", ['id' => 1, 'message' => 'embedded {id}'], "embedded {id}"],
        ];
    }
}
