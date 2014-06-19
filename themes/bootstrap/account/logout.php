<?php if (!defined('FLUX_ROOT')) exit; ?>

		<div class="page-header">
			<h1><?php echo htmlspecialchars(Flux::message('LogoutHeading')) ?></h1>
		</div>
		<p><strong><?php echo htmlspecialchars(Flux::message('LogoutInfo')) ?></strong> <?php printf(Flux::message('LogoutInfo2'), $metaRefresh['location']) ?></p>
