<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('MainPageHeading')) ?></h2>
<p><strong><?php echo htmlspecialchars(Flux::message('MainPageInfo')) ?></strong></p>
<p><?php echo htmlspecialchars(Flux::message('MainPageInfo2')) ?></p>
<ol>
	<li><p class="green"><?php echo htmlspecialchars(sprintf(Flux::message('MainPageStep1'), __FILE__)) ?></p></li>
	<li><p class="green"><?php echo htmlspecialchars(Flux::message('MainPageStep2')) ?></p></li>
</ol>
<p style="text-align: right"><strong><em><?php echo htmlspecialchars(Flux::message('MainPageThanks')) ?></em></strong></p>
