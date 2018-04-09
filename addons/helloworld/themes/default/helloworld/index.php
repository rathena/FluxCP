<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo Flux::message('HelloWorld') ?></h2>
<p><?php echo Flux::message('HelloInfoText') ?></p>
<p><?php printf(Flux::message('HelloVersionText'), $fluxVersion) ?></p>
