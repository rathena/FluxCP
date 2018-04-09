<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('MissingViewHeading')) ?></h2>
<p><?php echo htmlspecialchars(Flux::message('MissingViewModLabel')) ?> <span class="module-name"><?php echo $this->params->get('module') ?></span>, <?php echo htmlspecialchars(Flux::message('MissingViewActLabel')) ?> <span class="module-name"><?php echo $this->params->get('action') ?></span></p>
<p><?php echo htmlspecialchars(Flux::message('MissingViewReqLabel')) ?> <span class="request"><?php echo $_SERVER['REQUEST_URI'] ?></span></p>
<p><?php echo htmlspecialchars(Flux::message('MissingViewLocLabel')) ?> <span class="fs-path"><?php echo $realViewPath ?></span></p>
