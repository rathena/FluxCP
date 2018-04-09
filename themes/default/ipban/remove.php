<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('IpbanRemoveHeading')) ?></h2>
<?php if ($ipban): ?>
	<?php if (!empty($errorMessage)): ?>
		<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
	<?php endif ?>
	<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
		<input type="hidden" name="remipban" value="1" />
		<table class="generic-form-table">
			<tr>
				<th><label for="list"><?php echo htmlspecialchars(Flux::message('IpbanIpAddressLabel')) ?></label></th>
				<td><input type="text" name="list" id="list"
						value="<?php echo htmlspecialchars(empty($list) ? '' : $list) ?>" /></td>
				<td><p><?php echo htmlspecialchars(Flux::message('IpbanIpAddressInfo')) ?></p></td>
			</tr>
			<tr>
				<th><label for="reason"><?php echo htmlspecialchars(Flux::message('IpbanRemoveReasonLabel')) ?></label></th>
				<td>
					<textarea name="reason" id="reason" class="reason"><?php
						echo htmlspecialchars(empty($reason) ? '' : $reason)
					?></textarea>
				</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td colspan="2"><input type="submit" value="<?php echo htmlspecialchars(Flux::message('IpbanRemoveButton')) ?>" /></td>
			</tr>
		</table>
	</form>
<?php else: ?>
<p>No such IP ban. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
