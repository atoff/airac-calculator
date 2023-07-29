<?php

use CobaltGrid\AIRACCalculator\AIRACCycle;
use CobaltGrid\AIRACCalculator\Exceptions\InvalidAIRACCycleCodeException;
use CobaltGrid\AIRACCalculator\Exceptions\InvalidAIRACEffectiveDateException;
use CobaltGrid\AIRACCalculator\Exceptions\InvalidAIRACSerialException;
use PHPUnit\Framework\TestCase;

final class AIRACCreationTest extends TestCase
{
    public function testItCanGetCycleForDate(): void
    {
        // This cycle should be serial number 1619

        // On the effective date
        $cycle = AIRACCycle::forDate(new DateTime("2025-02-20", new DateTimeZone("UTC")));
        $this->assertEquals(1619, $cycle->getSerial());

        // Within the cycle
        $cycle = AIRACCycle::forDate(new DateTime("2025-03-19 17:00:00", new DateTimeZone("UTC")));
        $this->assertEquals(1619, $cycle->getSerial());

        // Different timezone
        $cycle = AIRACCycle::forDate(new DateTime("2025-03-19 15:59:00", new DateTimeZone("PST")));
        $this->assertEquals(1619, $cycle->getSerial());
    }

    
    public function testItCanGetCycleFromValidEffectiveDate(): void
    {
        $cycle = AIRACCycle::fromEffectiveDate(new DateTime("2025-02-20", new DateTimeZone("UTC")));
        $this->assertEquals(1619, $cycle->getSerial());

        $cycle = AIRACCycle::fromEffectiveDate(new DateTime("2025-02-19 16:00:00", new DateTimeZone("PST")));
        $this->assertEquals(1619, $cycle->getSerial());
    }

    public function testItThrowsIfNotValidEffectiveDate(): void
    {
        $this->expectException(InvalidAIRACEffectiveDateException::class);
        $this->expectExceptionMessage("2025-02-20T00:00:01+00:00 is not a valid effective date");
        AIRACCycle::fromEffectiveDate(new DateTime("2025-02-20 00:00:01", new DateTimeZone("UTC")));
    }

    public function testItCanGetForValidCycleCode(): void
    {
        $cycle = AIRACCycle::fromCycleCode('2502');
        $this->assertEquals(1619, $cycle->getSerial());

        $cycle = AIRACCycle::fromCycleCode('2513');
        $this->assertEquals(1630, $cycle->getSerial());
    }

    public function testItThrowsForInvalidCycleCodeFormat(): void
    {
        $this->expectException(InvalidAIRACCycleCodeException::class);
        $this->expectExceptionMessage("25021 was not of format YYOO");
        AIRACCycle::fromCycleCode('25021');
    }

    public function testItThrowsForInvalidCycleCodeWithTooManyCycles(): void
    {
        $this->expectException(InvalidAIRACCycleCodeException::class);
        $this->expectExceptionMessage("Year 2025 does not have 14 cycles");
        AIRACCycle::fromCycleCode('2514');
    }

    public function testItCanCreateFromValidSerial(): void
    {
        $cycle = AIRACCycle::fromSerial('1619');
        $this->assertEquals(1619, $cycle->getSerial());
    }

    public function testItThrowsForInvalidSerial(): void
    {
        $this->expectException(InvalidAIRACSerialException::class);
        $this->expectExceptionMessage("-1 is not a valid AIRAC serial");
        AIRACCycle::fromSerial(-1);
    }

    public function testItCanGetCurrentAirac(): void
    {
        $this->assertEquals(AIRACCycle::forDate(new DateTime())->getSerial(), AIRACCycle::current()->getSerial());
    }

    public function testItCanGetNextAirac(): void
    {
        $this->assertEquals(AIRACCycle::forDate(new DateTime())->getSerial() + 1, AIRACCycle::next()->getSerial());
    }

    public function testItCanGetPreviousAirac(): void
    {
        $this->assertEquals(AIRACCycle::forDate(new DateTime())->getSerial() - 1, AIRACCycle::previous()->getSerial());
    }
}