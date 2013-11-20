<?php 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired(); 
?>           
<h2><?php echo htmlspecialchars(Flux::message('XCMSNewsHeader')) ?></h2>
<?php if($newstype == '1'):?>
	<?php if($news): ?>
	<div class="newsDiv">
		<?php foreach($news as $nrow):?>
			<h2><?php echo $nrow->title ?></h2>
			<div class="newsCont">
				<span class="newsDate"><small>by <?php echo $nrow->author ?> on <?php echo date(Flux::config('DateFormat'),strtotime($nrow->created))?></small></span>
				<p><?php echo $nrow->body ?></p>
				<?php if($nrow->created != $nrow->modified):?>
					<small><?php echo htmlspecialchars(Flux::message('XCMSModifiedLabel')) ?> : <?php echo date('m-d-y',strtotime($nrow->modified))?></small>
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



<?php elseif($newstype == '2'):?>
	<?php if(isset($xml) && isset($xml->channel)): ?>
	<div class="newsDiv">
		<?php foreach($xml->channel->item as $rssItem): ?>
			<?php $i++; if($i <= $newslimit): ?>
				<h2><?php echo $rssItem->title ?></h2>
				<div class="newsCont">
					<span class="newsDate"><small>Posted on <?php echo date(Flux::config('DateFormat'),strtotime($rssItem->pubDate))?></small></span>
					<p><?php echo $rssItem->description ?></p>
					<a class="news_link" href="<?php echo $rssItem->link ?>"><small><?php echo htmlspecialchars(Flux::message('XCMSNewsLink')) ?></small></a>
					<div class="clear"></div>
				</div>
			<?php endif ?>
		<?php endforeach; ?> 
	</div>
	<?php else: ?>
		<p>
			<?php echo htmlspecialchars(Flux::message('XCMSNewsRSSNotFound')) ?><br/><br/>
		</p>
	<?php endif ?>



<?php elseif($newstype == '3'): ?>
		<p>
			<?php echo htmlspecialchars(Flux::message('XCMSNewsTXTNotFound')) ?><br/><br/>
		</p>



<?php elseif($newstype == '4'): ?>
		<p>
			<?php echo htmlspecialchars(Flux::message('XCMSNewsFBNotFound')) ?><br/><br/>
		</p>



<?php elseif($newstype == '5'): ?>
		<p>
			<?php echo htmlspecialchars(Flux::message('XCMSNewsTwNotFound')) ?><br/><br/>
		</p>


<?php endif ?>