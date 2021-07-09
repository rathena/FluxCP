<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('MissingActionHeading')) ?></h2>
<p><?php echo htmlspecialchars(Flux::message('MissingActionModLabel')) ?> <span class="module-name"><?php echo $this->params->get('module') ?></span>, <?php echo htmlspecialchars(Flux::message('MissingActionActLabel')) ?> <span class="module-name"><?php echo $this->params->get('action') ?></span></p>
<p><?php echo htmlspecialchars(Flux::message('MissingActionReqLabel')) ?> <span class="request"><?php echo $_SERVER['REQUEST_URI'] ?></span></p>
<p><?php echo htmlspecialchars(Flux::message('MissingActionLocLabel')) ?> <span class="fs-path"><?php echo $realActionPath ?></span></p>
