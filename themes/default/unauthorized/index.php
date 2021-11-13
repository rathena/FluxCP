<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2 class="red"><?php echo htmlspecialchars(Flux::message('UnauthorizedHeading')) ?></h2>
<p><?php printf(Flux::message('UnauthorizedInfo'), $metaRefresh['location']) ?></p>
