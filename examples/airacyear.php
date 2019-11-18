<?php
require('../vendor/autoload.php');

use Carbon\Carbon;
use CobaltGrid\Aviation\AIRACCalculator;

$date = Carbon::now();
if ($_GET['year']) {
    $date = new Carbon($_GET['year'] . '-01-01');
}
?>

<form>
    AIRAC Year (in format 2019):
    <input type='text' name='year'/>
    <input type='submit' value='Submit'/>
</form>

<?php

$cycles = AIRACCalculator::cyclesForYear($date->format('Y'));

?>
<table>
    <thead>
    <tr>
        <th>Cycle</th>
        <th>Effective Date</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($cycles as $cycle) {
        ?>
        <tr>
            <td><?= $cycle[0] ?></td>
            <td><?= $cycle[1] ?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
