<?php

namespace CobaltGrid\AIRACCalculator;

use CobaltGrid\AIRACCalculator\Exceptions\InvalidAIRACCycleCodeException;
use CobaltGrid\AIRACCalculator\Exceptions\InvalidAIRACEffectiveDateException;
use CobaltGrid\AIRACCalculator\Exceptions\InvalidAIRACSerialException;
use DateInterval;
use DateTime;
use DateTimeZone;

class AIRACCycle
{
    /** Magic date for the 0th serial  */
    protected static string $epochDate = '1901-01-10';

    /** Number of days between each AIRAC */
    protected static int $cycleDurationDays = 28;

    /**
     * Returns the AIRAC cycle that was effective at the given date
     */
    public static function forDate(DateTime $date): static
    {
        return static::fromSerial(floor(static::getSerialForDate($date)));
    }

    /**
     * Returns the AIRAC cycle that became effective at the given date
     *
     * @throws InvalidAIRACEffectiveDateException
     */
    public static function fromEffectiveDate(DateTime $date): static
    {
        $serial = static::getSerialForDate($date);
        if (floor($serial) !== $serial) {
            throw new InvalidAIRACEffectiveDateException("{$date->format(DateTime::ATOM)} is not a valid effective date");
        }

        return static::fromSerial($serial);
    }

    /**
     * Returns the AIRAC cycle identified by the given cycle code.
     *
     * Cycle codes take the format YYOO. For example 2301 identifies the first cycle of 2023.
     * Identifiers starting 64 to 99 are cycles between years 1964 and 1999 inclusive
     * Identifiers starting 00 to 63 are cycles between years 2000 and 2063 inclsuive
     *
     * @throws InvalidAIRACCycleCodeException
     */
    public static function fromCycleCode(string $cycleCode): static
    {
        preg_match("/^(\d\d)(\d\d)$/", $cycleCode, $matches);

        if (count($matches) !== 3) {
            throw new InvalidAIRACCycleCodeException("{$cycleCode} was not of format YYOO");
        }

        $yearPart = (int) $matches[1];
        $ordinalPart = (int) $matches[2];

        $year = $yearPart > 63 ? 1900 + $yearPart : 2000 + $yearPart;
        $previousYear = $year - 1;

        $previousYearFinalAirac = static::forDate(new DateTime("{$previousYear}-12-31", new DateTimeZone('UTC')));
        $airac = static::fromSerial($previousYearFinalAirac->getSerial() + $ordinalPart);

        if ((int) $airac->getEffectiveDate()->format('Y') !== $year) {
            throw new InvalidAIRACCycleCodeException("Year {$year} does not have {$ordinalPart} cycles");
        }

        return $airac;
    }

    /** Creates an AIRAC cycle from an AIRAC serial */
    public static function fromSerial(int $serial): static
    {
        return new AIRACCycle($serial);
    }

    /** Returns the current AIRAC cycle */
    public static function current(): static
    {
        return static::forDate(new DateTime());
    }

    /** Returns the next AIRAC cycle */
    public static function next(): static
    {
        return static::fromSerial(static::current()->getSerial() + 1);
    }

    /** Returns the previous AIRAC cycle */
    public static function previous(): static
    {
        return static::fromSerial(static::current()->getSerial() - 1);
    }

    /** Returns the serial of the AIRAC cycle at the given date. Whilst a float is always returned, note that this is an integer if exactly at an effective date, or a float otherwise*/
    public static function getSerialForDate(DateTime $dateTime): float
    {
        $diffSeconds = $dateTime->getTimestamp() - static::getEpochDate()->getTimestamp();

        return $diffSeconds / (static::$cycleDurationDays * 24 * 60 * 60);
    }

    /**
     * Create an AIRAC Cycle instance
     */
    public function __construct(protected int $serial)
    {
        if ($serial < 0) {
            throw new InvalidAIRACSerialException("{$serial} is not a valid AIRAC serial");
        }
    }

    /**
     * Returns the AIRAC Cycle's identifier code
     *
     * Identifiers starting 64 to 99 are cycles between years 1964 and 1999 inclusive
     * Identifiers starting 00 to 63 are cycles between years 2000 and 2063 inclsuive
     *
     * @return string Cycle identifier code, of format YYMM (e.g. 2308 is the 8th cycle for the year 2023)
     */
    public function getCycleCode(): string
    {
        $year = $this->getEffectiveDate()->format('Y');
        $yearPart = (string) ($year < 2000 ? 64 + (int) ($this->getEffectiveDate()->format('y') - 1) : $this->getEffectiveDate()->format('y'));
        $ordinalPart = str_pad($this->getOrdinal(), 2, '0', STR_PAD_LEFT);

        return $yearPart.$ordinalPart;
    }

    /** Returns the ordinal (cycle number of the year) for this cycle. Between 1 and 14 */
    public function getOrdinal(): int
    {
        return floor(($this->getEffectiveDate()->format('z') - 1) / 28) + 1;
    }

    /**
     * Returns the date the AIRAC Cycle becomes effective
     */
    public function getEffectiveDate(): DateTime
    {
        $days = $this->serial * static::$cycleDurationDays;

        return $this->getEpochDate()->add(new DateInterval("P{$days}D"));
    }

    /** Returns the cycle's serial */
    public function getSerial(): int
    {
        return $this->serial;
    }

    /** Returns the next cycle from the cycle */
    public function nextCycle(): static
    {
        return static::fromSerial($this->serial + 1);
    }

    /** Returns the previous cycle from the cycle */
    public function previousCycle(): static
    {
        return static::fromSerial($this->serial - 1);
    }

    /** Returns a DateTime instance that represents the AIRAC epoch */
    protected static function getEpochDate(): DateTime
    {
        return new DateTime(static::$epochDate, new DateTimeZone('UTC'));
    }
}
