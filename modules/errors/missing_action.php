<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('MissingActionTitle');
$realActionPath = sprintf('%s/%s/%s/%s.php', FLUX_ROOT, $this->modulePath, $this->params->get('module'), $this->params->get('action'));
?>
