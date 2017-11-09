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
namespace DSchoenbauer\Orm\Events\Filter\PasswordMask;

use PHPUnit\Framework\TestCase;

/**
 * Description of BlowFishPasswordStrategy
 *
 * @author David Schoenbauer
 */
class BlowFishPasswordStrategyTest extends TestCase
{

    private $object;

    protected function setUp()
    {
        $this->object = new BlowFishPasswordStrategy();
    }

    public function testHasProperInterface()
    {
        $this->assertInstanceOf(PasswordMaskStrategyInterface::class, $this->object);
    }

    public function testCost()
    {
        $this->assertEquals(10, $this->object->getCost());
        $this->assertEquals(12, $this->object->setCost(12)->getCost());
    }

    /**
     * @dataProvider generalDataProvider
     */
    public function testHashString($result, $value, $cost)
    {
        $result = $this->object->setCost($cost)->hashString($value);
        $this->assertEquals(60, strlen($result));
        $this->assertEquals('$2y$' . str_pad($cost, 2, "0", STR_PAD_LEFT) . '$', substr($result, 0, 7));
    }

    /**
     * @dataProvider generalDataProvider
     */
    public function testValidate($hash, $string)
    {
        $this->assertFalse($this->object->validate('WrongPassword', $hash));
        $this->assertTrue($this->object->validate($string, $hash));
    }

    /**
     * Top 25 passwords of 2016
     */
    public function generalDataProvider()
    {
        return [
            ['$2y$04$1T2J6oRR62HxBM8W3uh85O/ILd2raamXlFGu2PIRJVqfPGPggWe8G', '1234567890', 4],
            ['$2y$05$YY.MVXxRtUMVo3kTVc5GkewkSS068S5wlYoQ06I6Er6N.XbWsWYMi', '123456789', 5],
            ['$2y$06$rB4pocdInpDdaxHoXgeFtegMtWO0rajreJMISY45dfx38fMpuD9Cm', '123456', 6],
            ['$2y$04$duFRRG1eTbTg006AFL8ZlOlrjCGwA0FtTQrOtFYRhb5tuneU8xN5G', 'qwerty', 4],
            ['$2y$05$Ua2WRbT7J0a8UCeh.OGTLuCinM/vYRzWKcwujI7GR401PUGPDWlS2', '12345678', 5],
            ['$2y$06$1pOMwmT51VkiLfdr1rttDePVFp.9w8SbvY.h.JK1iDXhGVZnYBWjO', '111111', 6],
            ['$2y$07$E8HBhIo3izEM7imBQWK7R.tR5kFv.2khCYIoQaH.EyuSGADTP1Dle', '1234567', 7],
            ['$2y$08$zTvjRWjjEQrjKteau1MJ4OU1cMvKPjTwzM5fkEhJr19QDiAqXuRPG', 'password', 8],
            ['$2y$09$ry/xyCe9ue7wkiR0y7VvCeV1jznchuLOFxhKnOMDuccRzAPBEob7u', '123123', 9],
            ['$2y$09$4Dz06zP8.j2t06BhyTShUOmWGEXOSkzzjasR/eLWXmSkxNDo5uML6', '987654321', 9],
            ['$2y$04$f5njDmszvGSBjov4XXxfOewLXeylaG7e5GS8.0QRZufbaGB6OEs7G', 'qwertyuiop', 4],
            ['$2y$05$n15czAj.cjjHLc5pVZZCRe.iRI0WtEdeJo8oTDSmLRt4OLOFW3lPO', 'mynoob', 5],
            ['$2y$06$8cs3jTLOA9Fx3gaU3k32ZuhRW6ywOnM8spBkUtZ3rsmfVXvSOnsoy', '123321', 6],
            ['$2y$04$nirUeLULWWjSXv3.l5.U/ebzGhDpvc2Wy2codQjBpm7nRZ9QEg/Su', '666666', 4],
            ['$2y$05$dh62fEVbrq.SfMMY9TEiNeAzLCosDz3/a5caABiM8PScB2EJV/S.y', '18atcskd2w', 5],
            ['$2y$06$WYMe3yR/iouhUR7GQ0wZtevyjsLjs1reT/O.W8kIA1IO3RdoOxW6S', '7777777', 6],
            ['$2y$07$0YGmgH8GF91r.J7/WtInPeoGetjGJXgh9U72a8emA2fiU29QpQDrK', '1q2w3e4r', 7],
            ['$2y$08$Xu.HGAoWdTpiO5JfsaFEGuH/HNy3vgM2gRIHSLzQdyNU0uOYxT1ty', '654321', 8],
            ['$2y$09$v.RP127GKdzbAqJe3b.mWOnWiBmnEa5BS08.kxTv7QTW89NS1aCZ.', '555555', 9],
            ['$2y$07$tBizGPDLORj8Ba/r9QoQYOuvlBERP0valLwkmV5uOtK3BNRY70xH.', '3rjs1la7qe', 7],
            ['$2y$04$iicwoNuqmAda5PhXmX6pHeLIITgglQ/FBEk9wqb8BpAPJaQTwhVEe', 'google', 4],
            ['$2y$05$Cm.ezmdDHhu9kRoJyZRl0ulJTRkapBRnUMP5lcb1BJSwjjKGHgJre', '1q2w3e4r5t', 5],
            ['$2y$05$CMbtd4Ht7/nRVgdAlteYeOkaEe3S5W57H3xp5dKcFpWa8GjhYOVjm', '123qwe', 5],
            ['$2y$04$lQ6aA1McV/WaHQtCB0ZPu.CbzFvR.hhiDZl/53DJmuJCcuarbd0AC', 'zxcvbnm', 4]
        ];
    }
}
