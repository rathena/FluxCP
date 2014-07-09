<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php $menus = array() ?>
<?php if (!empty($pageMenuItems)): ?>
	<div class="panel panel-default">
  <div class="panel-body"><?php echo empty($title) ? 'Actions for this page' : htmlspecialchars($title) ?>:
	<?php foreach ($pageMenuItems as $menuItemName => $menuItemLink): ?>
		<?php $menus[] = sprintf('<a href="%s" class="page-menu-item">%s</a>', $menuItemLink, htmlspecialchars($menuItemName)) ?>
	<?php endforeach ?>
	<?php echo implode(', ', $menus) ?>
	</div>
	</div>
<?php endif ?>