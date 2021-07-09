<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('IpbanEditHeading')) ?></h2>
<?php if ($ipban): ?>
	<?php if (!empty($errorMessage)): ?>
		<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
	<?php endif ?>
	<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
		<input type="hidden" name="modipban" value="1" />
		<table class="generic-form-table">
			<tr>
				<th><label for="list"><?php echo htmlspecialchars(Flux::message('IpbanIpAddressLabel')) ?></label></th>
				<td><input type="text" name="newlist" id="list"
						value="<?php echo htmlspecialchars(($list=$params->get('newlist')) ? $list : $ipban->list) ?>" /></td>
				<td><p><?php echo htmlspecialchars(Flux::message('IpbanIpAddressInfo')) ?></p></td>
			</tr>
			<tr>
				<th><label for="reason"><?php echo htmlspecialchars(Flux::message('IpbanReasonLabel')) ?></label></th>
				<td>
					<textarea name="reason" id="reason" class="reason"><?php
						echo htmlspecialchars(($reason=$params->get('reason')) ? $reason : $ipban->reason)
					?></textarea>
				</td>
				<td></td>
			</tr>
			<tr>
				<th><label><?php echo htmlspecialchars(Flux::message('IpbanUnbanDateLabel')) ?></label></th>
				<td><?php echo $this->dateTimeField('rtime', ($rtime=$params->get('rtime')) ? $rtime : $ipban->rtime) ?></td>
				<td></td>
			</tr>
			<tr>
				<th><label for="edit_reason"><?php echo htmlspecialchars(Flux::message('IpbanEditReasonLabel')) ?></label></th>
				<td>
					<textarea name="edit_reason" id="edit_reason" class="edit_reason"><?php
						echo htmlspecialchars(($editReason=$params->get('edit_reason')) ? $editReason : '')
					?></textarea>
				</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="2"><input type="submit" value="<?php echo htmlspecialchars(Flux::message('IpbanEditButton')) ?>" /></td>
			</tr>
		</table>
	</form>
<?php else: ?>
<p>No such IP ban. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
