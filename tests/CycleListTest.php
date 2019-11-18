<?php

use Carbon\Carbon;
use CobaltGrid\Aviation\AIRACCalculator;
use PHPUnit\Framework\TestCase;

final class CycleListTest extends TestCase
{
    public function testItReturnsCyclesForYear(): void
    {
        // 2019 has 13 cycles
        // 2020 has 14 cycles

        $this->assertEquals(13, count(AIRACCalculator::cyclesForYear('2019')));
        $this->assertEquals(14, count(AIRACCalculator::cyclesForYear('2020')));

        // Assert 2D array
        $this->assertEquals(2, count(AIRACCalculator::cyclesForYear('2020')[0]));
    }

    public function testItReturnsCorrectArray(): void
    {
        // 2019 has 13 cycles, first cycle 1901 effective 2019-01-03

        $cycles = AIRACCalculator::cyclesForYear('2019');

        $this->assertEquals("1901", $cycles[0][0]);
        $this->assertEquals(new Carbon('2019-01-03'), $cycles[0][1]);


        // 2020 has 14 cycles, cycle 2014 effective 2020-12-31

        $cycles = AIRACCalculator::cyclesForYear('2020');

        $this->assertEquals("2014", $cycles[13][0]);
        $this->assertEquals(new Carbon('2020-12-31'), $cycles[13][1]);
    }
}
