<?php
/* CMS Addon
 * Created and maintained by Akkarin
 * Current Version: 1.0.1
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();   
?>
<h2><?php echo htmlspecialchars(Flux::message('XCMSPageHeader')) ?></h2>
<p><?php echo htmlspecialchars(Flux::message('XCMSPageText')) ?></p>
<?php if($pages): ?>
	<table class="horizontal-table" width="100%">  
		<tr>
			<th><?php echo htmlspecialchars(Flux::message('XCMSPageTitleLabel')) ?></th>
			<th><?php echo htmlspecialchars(Flux::message('XCMSActionLabel')) ?></th>    
		</tr>
		<?php foreach($pages as $prow):?>
			<tr >
				<td><a href="<?php echo $this->url('pages', 'content', array('path' => $prow->path))?>" title="View the <?php echo $prow->title?> Page"><?php echo $prow->title?></a></td>
				<td align="center">
					<a href="<?php echo $this->url('pages', 'edit', array('id' => $prow->id)); ?>"><?php echo htmlspecialchars(Flux::message('XCMSEdit')) ?></a> |
					<a href="<?php echo $this->url('pages', 'delete', array('id' => $prow->id)); ?>" onclick="return confirm('<?php echo htmlspecialchars(Flux::message('XCMSConfirmDelete')) ?>');"><?php echo htmlspecialchars(Flux::message('XCMSDelete')) ?></a>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
<?php else: ?>
	<p>
		<?php echo htmlspecialchars(Flux::message('XCMSPageEmpty')) ?><br/><br/>
		<a href="<?php echo $this->url('pages', 'add') ?>"><?php echo htmlspecialchars(Flux::message('XCMSPageCreate')) ?></a>
	</p>
<?php endif ?>