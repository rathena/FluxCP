<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Chat Messages</h2>
<?php if ($messages): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', 'Date/Time') ?></th>
		<th><?php echo $paginator->sortableColumn('type', 'Type') ?></th>
		<th><?php echo $paginator->sortableColumn('type_id', 'Type ID') ?></th>
		<th><?php echo $paginator->sortableColumn('src_charid', 'Char ID') ?></th>
		<th><?php echo $paginator->sortableColumn('src_accountid', 'Account ID') ?></th>
		<th><?php echo $paginator->sortableColumn('src_map', 'Map') ?></th>
		<th><?php echo $paginator->sortableColumn('src_map_x', 'X') ?></th>
		<th><?php echo $paginator->sortableColumn('src_map_y', 'Y') ?></th>
		<th><?php echo $paginator->sortableColumn('dst_charname', 'Receiver') ?></th>
		<th><?php echo $paginator->sortableColumn('message', 'Message') ?></th>
	</tr>
	<?php foreach ($messages as $message): ?>
	<tr>
		<td align="right"><?php echo $this->formatDateTime($message->time) ?></td>
		<td><?php echo $message->type ?></td>
		<td>
			<?php if ($message->type_id): ?>
				<?php if ($message->type == 'G' && $auth->actionAllowed('guild', 'view') && $auth->allowedToViewGuild): ?>
					<?php echo $this->linkToGuild($message->type_id, $message->type_id) ?>
				<?php else: ?>
					<?php echo $message->type_id ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($message->src_charid): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($message->src_charid, $message->src_charid) ?>
				<?php else: ?>
					<?php echo $message->src_charid ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($message->src_accountid): ?>
				<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
					<?php echo $this->linkToAccount($message->src_accountid, $message->src_accountid) ?>
				<?php else: ?>
					<?php echo $message->src_accountid ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if (strlen(basename($message->src_map, '.gat')) > 0): ?>
				<?php echo htmlspecialchars(basename($message->src_map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($message->src_map_x): ?>
				<?php echo $message->src_map_x ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($message->src_map_y): ?>
				<?php echo $message->src_map_y ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($message->dst_charname): ?>
				<?php echo htmlspecialchars($message->dst_charname) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo htmlspecialchars($message->message) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	No chat messages found.
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
