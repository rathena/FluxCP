<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('IpbanListHeading')) ?></h2>
<?php if ($banlist): ?>
<?php echo $paginator->infoText() ?>
<form action="<?php echo $this->url('ipban', 'unban') ?>" method="post">
	<input type="hidden" name="unban" value="1" />
	<table class="horizontal-table">
		<tr>
			<?php if ($auth->allowedToRemoveIpBan && $auth->actionAllowed('ipban', 'unban')): ?>
			<th><input type="checkbox" onclick="$('.unban-cb').attr('checked', this.checked)" /></th>
			<?php endif ?>
			<th><?php echo $paginator->sortableColumn('list', Flux::message('IpbanBannedIpLabel')) ?></th>
			<th><?php echo $paginator->sortableColumn('btime', Flux::message('IpbanBanDateLabel')) ?></th>
			<th><?php echo $paginator->sortableColumn('reason', Flux::message('IpbanBanReasonLabel')) ?></th>
			<th><?php echo $paginator->sortableColumn('rtime', Flux::message('IpbanBanExpireLabel')) ?></th>
			<?php if ($auth->allowedToModifyIpBan && $auth->actionAllowed('ipban', 'edit')): ?>
			<th><?php echo htmlspecialchars(Flux::message('IpbanModifyLink')) ?></th>
			<?php endif ?>
			<?php if ($auth->allowedToRemoveIpBan && $auth->actionAllowed('ipban', 'remove')): ?>
			<th><?php echo htmlspecialchars(Flux::message('IpbanRemoveLink')) ?></th>
			<?php endif ?>
		</tr>
		<?php foreach ($banlist as $list): ?>
		<tr>
			<?php if ($auth->allowedToRemoveIpBan && $auth->actionAllowed('ipban', 'unban')): ?>
			<td align="center">
				<input type="checkbox" class="unban-cb" name="unban_list[]" value="<?php echo htmlspecialchars($list->list) ?>" />
			</td>
			<?php endif ?>
			<td>
			<?php if ($auth->actionAllowed('account', 'index')): ?>
				<?php echo $this->linkToAccountSearch(array('last_ip' => $list->list), $list->list) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($list->list) ?>
			<?php endif ?>
			</td>
			<td><?php echo $this->formatDateTime($list->btime) ?></td>
			<td>
				<?php if ($list->reason): ?>
					<?php echo htmlspecialchars($list->reason) ?>
				<?php else: ?>
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
				<?php endif ?>
			</td>
			<td>
				<?php if (!$list->rtime || $list->rtime == '1000-01-01 00:00:00'): ?>
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NeverLabel')) ?></span>
				<?php else: ?>
					<?php echo $this->formatDateTime($list->rtime) ?>
				<?php endif ?>
			</td>
			<?php if ($auth->allowedToModifyIpBan && $auth->actionAllowed('ipban', 'edit')): ?>
			<td class="td-action action"><a href="<?php echo $this->url('ipban', 'edit', array('list' => $list->list)) ?>"><?php echo htmlspecialchars(Flux::message('IpbanModifyLink')) ?></a></td>
			<?php endif ?>
			<?php if ($auth->allowedToRemoveIpBan && $auth->actionAllowed('ipban', 'remove')): ?>
			<td class="td-action action"><a href="<?php echo $this->url('ipban', 'remove', array('list' => $list->list)) ?>"><?php echo htmlspecialchars(Flux::message('IpbanRemoveLink')) ?></a></td>
			<?php endif ?>
		</tr>
		<?php endforeach ?>
	</table>
	<?php if ($auth->allowedToRemoveIpBan && $auth->actionAllowed('ipban', 'unban')): ?>
	<table class="generic-form-table">
		<tr>
			<th><label for="reason"><?php echo htmlspecialchars(Flux::message('IpbanRemoveReasonLabel')) ?></label></th>
			<td>
				<textarea name="reason" id="reason" class="reason"><?php
					echo htmlspecialchars(empty($reason) ? '' : $reason)
				?></textarea>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="button-action">
				<input type="submit" value="<?php echo htmlspecialchars(Flux::message('IpbanUnbanButton')) ?>" />
			</td>
		</tr>
	</table>
	<?php endif ?>
</form>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('IpbanListNoBans')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
