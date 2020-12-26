<?php

use Carbon\Carbon;
use CobaltGrid\Aviation\AIRACCalculator;
use PHPUnit\Framework\TestCase;

final class DateForCycleTest extends TestCase
{
    public function testItReportsDateCorrectly(): void
    {
        // 1906 cycle effective at 23 MAY 19
        // 1907 cycle effective at 20 JUN 19

        $this->assertEquals(AIRACCalculator::dateForCycle('1906'), new Carbon('23 MAY 19'));
        $this->assertEquals(AIRACCalculator::dateForCycle('1907'), new Carbon('20 JUN 19'));
    }
}
