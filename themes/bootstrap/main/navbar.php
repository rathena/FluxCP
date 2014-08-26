<?php if (!defined('FLUX_ROOT')) exit; ?>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="./"><?php echo Flux::config('SiteTitle'); ?></a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<?php $menuItems = $this->getMenuItems(); ?>
				<?php if (!empty($menuItems)): ?>
					<?php foreach ($menuItems as $menuCategory => $menus): ?>
						<?php if (!empty($menus)): ?>
							<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo htmlspecialchars(Flux::message($menuCategory)) ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
							<?php foreach ($menus as $menuItem):  ?>
								<li>
									<a href="<?php echo $menuItem['url'] ?>"><?php echo htmlspecialchars(Flux::message($menuItem['name'])) ?></a>
								</li>
							<?php endforeach ?>
							</ul>
							</li>
						<?php endif ?>
					<?php endforeach ?>
				<?php endif ?>

				<?php $adminMenuItems = $this->getAdminMenuItems(); ?>
				<?php if (!empty($adminMenuItems) && Flux::config('AdminMenuNewStyle')): ?>
							<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Admin Menu <b class="caret"></b></a>
							<ul class="dropdown-menu">
							<?php foreach ($adminMenuItems as $menuItem): ?>
								<li>
									<a href="<?php echo $this->url($menuItem['module'], $menuItem['action']) ?>"><?php echo htmlspecialchars(Flux::message($menuItem['name'])) ?></a>
								</li>
							<?php endforeach ?>
							</ul>
							</li>
				<?php endif ?>

			</ul>
		</div><!--/.nav-collapse -->
	</div>
</div>
