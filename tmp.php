<?php

use Archon\DataFrame;
require_once 'vendor/autoload.php';

$df = DataFrame::fromCSV('scores/P35269.txt', ['sep'=>"\t"]);
print_r($df->columns());

// print_r($df['Position']->toArray());

print_r(implode(array_column($df['Position']->toArray(), 'Position')));