<?php

use Carbon\Carbon;
use CobaltGrid\Aviation\AIRACCalculator;
use PHPUnit\Framework\TestCase;

final class CurrentAiracTest extends TestCase
{
    public function testItReportsCorrectCurrentAirac(): void
    {
        // 1906 cycle effective at 23 MAY 19
        // 1907 cycle effective at 20 JUN 19

        Carbon::setTestNow(new Carbon('19 JUN 19 10:10:10'));
        $this->assertEquals('1906', AIRACCalculator::currentAiracCycle());

        Carbon::setTestNow(new Carbon('19 JUN 19 23:59:59'));
        $this->assertEquals('1906', AIRACCalculator::currentAiracCycle());


        Carbon::setTestNow(new Carbon('20 JUN 19'));
        $this->assertEquals('1907', AIRACCalculator::currentAiracCycle());

        Carbon::setTestNow();
    }

    public function testItReportsCorrectlyOverAYear(): void
    {
        // 2014 cycle effective at 31 DEC 20
        // 2101 cycle effective at 28 JAN 21

        Carbon::setTestNow(new Carbon('10 JAN 21'));
        $this->assertEquals('2014', AIRACCalculator::currentAiracCycle());

        Carbon::setTestNow();
    }

    public function testItCorrectlyIndicatesIfTodayIsNewAirac(): void
    {
        // 1906 cycle effective at 23 MAY 19
        // 1907 cycle effective at 20 JUN 19

        Carbon::setTestNow(new Carbon('20 JUN 19'));
        $this->assertTrue(AIRACCalculator::isNewAiracDay());

        Carbon::setTestNow(new Carbon('20 JUN 19 14:35:59'));
        $this->assertTrue(AIRACCalculator::isNewAiracDay());

        Carbon::setTestNow(new Carbon('19 JUN 19 23:59:59'));
        $this->assertFalse(AIRACCalculator::isNewAiracDay());

        Carbon::setTestNow();
    }

    public function testItCorrectlyIndicatesIfADateIsNewAirac(): void
    {
        // 1906 cycle effective at 23 MAY 19
        // 1907 cycle effective at 20 JUN 19

        $this->assertTrue(AIRACCalculator::isNewAiracDay(new Carbon('20 JUN 19')));

        $this->assertTrue(AIRACCalculator::isNewAiracDay(new Carbon('20 JUN 19 14:35:59')));

        $this->assertFalse(AIRACCalculator::isNewAiracDay(new Carbon('19 JUN 19 23:59:59')));
    }
}
