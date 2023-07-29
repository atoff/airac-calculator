<?php

use CobaltGrid\AIRACCalculator\AIRACCycle;
use PHPUnit\Framework\TestCase;

final class AIRACClassTest extends TestCase
{
    /** @var AIRACCycle */
    private $cycle;

    protected function setUp(): void
    {
        parent::setUp();
        // Effective 20 FEB 2025, serial 1619, code 2502
        $this->cycle = new AIRACCycle(1619);
    }

    public function testGettingCycleCode(): void
    {
        $this->assertEquals('2502', $this->cycle->getCycleCode());
    }

    public function testGettingOrdinal(): void
    {
        $this->assertEquals(2, $this->cycle->getOrdinal());
    }

    public function testGettingEffectiveDate(): void
    {
        $this->assertEquals(new DateTime('2025-02-20', new DateTimeZone('UTC')), $this->cycle->getEffectiveDate());
    }

    public function testGettingSerial(): void
    {
        $this->assertEquals(1619, $this->cycle->getSerial());
    }

    public function testGettingNextCycle(): void
    {
        $this->assertEquals(1620, $this->cycle->nextCycle()->getSerial());
    }

    public function testGettingPreviousCycle(): void
    {
        $this->assertEquals(1618, $this->cycle->previousCycle()->getSerial());
    }
}
