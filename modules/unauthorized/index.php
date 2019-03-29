<?php

use rAthena\FluxCp\Flux;

if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('UnauthorizedTitle');

$metaRefresh = array('seconds' => 2, 'location' => $this->basePath);
?>
