<?php 
if (!defined('FLUX_ROOT')) exit;
?>           
<h2><?php echo htmlspecialchars(Flux::message('XCMSNewsHomeTitle')) ?></h2>
<?php if($news): ?>
<div class="newsDiv">
	<?php foreach($news as $nrow):?>
		<h4><?php echo $nrow->title ?></h4>
		<div class="newsCont">
			<span class="newsDate"><small>by <?php echo $nrow->author ?> on <?php echo date(Flux::config('DateFormat'),strtotime($nrow->created))?></small></span>
			<p><?php echo $nrow->body ?></p>
			<?php if($nrow->created != $nrow->modified):?>
				<small><?php echo htmlspecialchars(Flux::message('XCMSModifiedLabel')) ?> : <?php echo date(Flux::config('DateFormat'),strtotime($nrow->modified))?></small>
			<?php endif; ?>
			<?php if($nrow->link): ?>
				<a class="news_link" href="<?php echo $nrow->link ?>"><small><?php echo htmlspecialchars(Flux::message('XCMSNewsLink')) ?></small></a>
				<div class="clear"></div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?> 
</div>
<?php else: ?>
	<p>
		<?php echo htmlspecialchars(Flux::message('XCMSNewsEmpty')) ?><br/><br/>
	</p>
<?php endif ?>
