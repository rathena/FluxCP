<?php
if (!defined('FLUX_ROOT')) exit;
?>
<h2><?php echo $title ?></h2>
<?php echo $body ?>
<?php if(Flux::config('CMSDisplayModifiedBy')):?>
<p><small><?php echo htmlspecialchars(Flux::message('CMSModifiedLabel')) ?> : <?php echo date(Flux::config('DateFormat'),strtotime($modified))?></small></p>
<?php endif ?>
