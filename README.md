
# AIRAC Calculator for PHP
[![Tests](https://github.com/atoff/airac-calculator/actions/workflows/test.yml/badge.svg)](https://github.com/atoff/airac-calculator/actions/workflows/test.yml)

`airac-calculator` is a zero dependency PHP library for parsing and computing AIRAC (Aeronautical Information Regulation And Control) cycles.

AIRAC cycles are used in the aviation industry to standardise significant revisions to operational information, such as aeronautical charts, frequencies, procedures and more. Each cycle lasts of 28 days, with 13 cycles per year (or exceptionally 14 in some cases). Cycles have two key, unique, properties; the date it becomes effective and a 4 digit cycle code.

This package has been validated against the EUROCONTROL AIRAC dates database.

## Requirements
* PHP 8.1 and above (tested up to PHP 8.3)

## License
airac-calculator is licensed under the MIT license, which can be found in the root of the package in the `LICENSE` file.

## Installation

The easiest way to install is via composer:
```
$ composer require cobaltgrid/airac-calculator
```

## Usage
If you are using Composer:
```php
<?php

// Include the composer autoloader
require('../vendor/autoload.php');

// Import the class
use CobaltGrid\AIRACCalculator\AIRACCycle;
```

All functions are performed on the `AIRACCycle` class. Documentation for available methods is given below.

### Creating a cycle
```php
// Getting the AIRAC cycle effective at a specific date
AIRACCycle::forDate(new DateTime('2023-07-29'));

// Getting the AIRAC cycle from an **exact** effective date
AIRACCycle::fromEffectiveDate(new DateTime('2023-07-13'));

// Getting the AIRAC cycle from a cycle code
AIRACCycle::fromCycleCode('2308');

// Getting from a serial (a serial is the number of cycles since the AIRAC epoch)
AIRACCycle::fromSerial(1619);

// Getting the current effective AIRAC
AIRACCycle::current();

// Getting the next AIRAC to become effective
AIRACCycle::next();

// Getting the previous AIRAC
AIRACCycle::previous();
```

### Getting cycle information
```php
$cycle = AIRACCycle::current();

// Get the 4-digit AIRAC cycle code (string)
$cycle->getCycleCode(); // 2308

// Gets the number of the cycle in it's year, starting at 1 (i.e. the first cycle is ordinal 1, second is 2, etc.) (int)
$cycle->getOrdinal(); // 1

// Returns the date the cycle became/becomes effective (DateTime)
$cycle->getEffectiveDate(); // DateTime Instance

// Returns the serial (number of cycles since AIRAC epoch) (int)
$cycle->getSerial(); // 1619

// Returns the next cycle from this one (AIRACCycle)
$cycle->nextCycle(); // AIRACCycle Instance

// Returns the previous cycle from this one (AIRACCycle)
$cycle->previousCycle(); // AIRACCycle Instance
```

## Testing
The code base is tested with PHP unit:
```
$ ./vendor/bin/phpunit
```
