<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('MissingViewTitle');
$realViewPath = sprintf('%s/%s/%s/%s.php', FLUX_ROOT, $addon ? $addon->themeDir : $this->themePath, $this->params->get('module'), $this->params->get('action'));
?>
