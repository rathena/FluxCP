<?php if (!defined('FLUX_ROOT')) exit; ?>
    
		<div class="page-header">
			<h1><?php echo htmlspecialchars(Flux::message('MainPageHeading')) ?></h1>
		</div>
		
		<div class="alert alert-info">	
			<p><strong><?php echo htmlspecialchars(Flux::message('MainPageInfo')) ?></strong></p>
			<p><?php echo htmlspecialchars(Flux::message('MainPageInfo2')) ?></p>
			<ol>
				<li><?php echo htmlspecialchars(sprintf(Flux::message('MainPageStep1'), __FILE__)) ?></li>
				<li><?php echo htmlspecialchars(Flux::message('MainPageStep2')) ?></li>
			</ol>
			<p style="text-align: right"><strong><em><?php echo htmlspecialchars(Flux::message('MainPageThanks')) ?></em></strong></p>
		</div>
	
	
