<?php
if (!defined('FLUX_ROOT')) exit;    
$this->loginRequired();
?>
<h2><?php echo htmlspecialchars(Flux::message('CMSNewsHeader')) ?></h2>
<?php if($news): ?>
	<table class="horizontal-table" width="100%">  
		<tr>
			<th><?php echo htmlspecialchars(Flux::message('CMSNewsTitleLabel')) ?></th>    
			<th><?php echo htmlspecialchars(Flux::message('CMSNewsAuthorLabel')) ?></th>    
			<th><?php echo htmlspecialchars(Flux::message('CMSCreatedLabel')) ?></th>
			<th><?php echo htmlspecialchars(Flux::message('CMSModifiedLabel')) ?></th>
			<th><?php echo htmlspecialchars(Flux::message('CMSActionLabel')) ?></th>    
		</tr>
		<?php foreach($news as $nrow):?>
			<tr>
				<td><?php echo $nrow->title?></td>
				<td><?php echo $nrow->author?></td>
				<td><?php echo date('d-m-Y',strtotime($nrow->created))?></td>
				<td><?php echo date('d-m-Y',strtotime($nrow->modified))?></td>
				<td>
					<a href="<?php echo $this->url('news', 'edit', array('id' => $nrow->id)); ?>">Edit</a> |
					<a href="<?php echo $this->url('news', 'delete', array('id' => $nrow->id)); ?>" onclick="return confirm('<?php echo htmlspecialchars(Flux::message('CMSConfirmDeleteLabel')) ?>');">Delete</a>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
<?php else: ?>
	<p>
		<?php echo htmlspecialchars(Flux::message('CMSNewsEmpty')) ?><br/><br/>
		<a href="<?php echo $this->url('news', 'add') ?>"><?php echo htmlspecialchars(Flux::message('CMSCreateLabel')) ?></a>
	</p>
<?php endif ?>
