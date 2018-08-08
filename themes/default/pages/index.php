<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();   
?>
<h2><?php echo htmlspecialchars(Flux::message('CMSPageHeader')) ?></h2>
<p><?php echo htmlspecialchars(Flux::message('CMSPageText')) ?></p>
<?php if($pages): ?>
	<table class="horizontal-table" width="100%">  
		<tr>
			<th><?php echo htmlspecialchars(Flux::message('CMSPageTitleLabel')) ?></th>
			<th><?php echo htmlspecialchars(Flux::message('CMSActionLabel')) ?></th>    
		</tr>
		<?php foreach($pages as $prow):?>
			<tr >
				<td><a href="<?php echo $this->url('pages', 'content', array('path' => $prow->path))?>" title="View the <?php echo $prow->title?> Page"><?php echo $prow->title?></a></td>
				<td align="center">
					<a href="<?php echo $this->url('pages', 'edit', array('id' => $prow->id)); ?>"><?php echo htmlspecialchars(Flux::message('CMSEdit')) ?></a> |
					<a href="<?php echo $this->url('pages', 'delete', array('id' => $prow->id)); ?>" onclick="return confirm('<?php echo htmlspecialchars(Flux::message('CMSConfirmDelete')) ?>');"><?php echo htmlspecialchars(Flux::message('CMSDelete')) ?></a>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
<?php else: ?>
	<p>
		<?php echo htmlspecialchars(Flux::message('CMSPageEmpty')) ?><br/><br/>
		<a href="<?php echo $this->url('pages', 'add') ?>"><?php echo htmlspecialchars(Flux::message('CMSPageCreate')) ?></a>
	</p>
<?php endif ?>
