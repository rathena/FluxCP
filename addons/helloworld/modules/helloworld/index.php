<?php

use rAthena\FluxCp\Flux;

if (!defined('FLUX_ROOT')) exit;

$fluxVersion  = Flux::VERSION;
$fluxVersion .= Flux::REPOSVERSION ? '.'.Flux::REPOSVERSION : '';
?>
