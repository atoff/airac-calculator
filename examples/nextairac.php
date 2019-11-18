<?php
require('../vendor/autoload.php');

use CobaltGrid\Aviation\AIRACCalculator;

echo "The next effective AIRAC is " . AIRACCalculator::nextAiracCycle() . ", which becomes effective at " . AIRACCalculator::nextAiracDate()->format('d/m/Y');