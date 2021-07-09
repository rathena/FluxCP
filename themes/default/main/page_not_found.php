<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('PageNotFoundHeading')) ?></h2>
<p><?php echo htmlspecialchars(Flux::message('PageNotFoundInfo')) ?></p>
<p><span class="request"><?php echo $_SERVER['REQUEST_URI'] ?></span></p>
