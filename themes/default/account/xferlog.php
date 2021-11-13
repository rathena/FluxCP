<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('XferLogHeading')) ?></h2>
<h3><?php echo htmlspecialchars(Flux::message('XferLogReceivedSubHead')) ?></h3>
<?php if ($incomingXfers): ?>
<table class="vertical-table">
	<tr>
		<th><?php echo htmlspecialchars(Flux::message('XferLogCreditsLabel')) ?></th>
		<th><?php echo htmlspecialchars(Flux::message('XferLogFromLabel')) ?></th>
		<th><?php echo htmlspecialchars(Flux::message('XferLogDateLabel')) ?></th>
	</tr>
	<?php foreach ($incomingXfers as $xfer): ?>
	<tr>
		<td align="right"><?php echo number_format($xfer->amount) ?></td>
		<td><?php echo htmlspecialchars($xfer->from_email) ?></td>
		<td><?php echo $this->formatDateTime($xfer->transfer_date) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p><?php echo htmlspecialchars(Flux::message('XferLogNotReceived')) ?></p>
<?php endif ?>

<h3><?php echo htmlspecialchars(Flux::message('XferLogSentSubHead')) ?></h3>
<?php if ($outgoingXfers): ?>
<table class="vertical-table">
	<tr>
		<th><?php echo htmlspecialchars(Flux::message('XferLogCreditsLabel')) ?></th>
		<th><?php echo htmlspecialchars(Flux::message('XferLogCharNameLabel')) ?></th>
		<th><?php echo htmlspecialchars(Flux::message('XferLogDateLabel')) ?></th>
	</tr>
	<?php foreach ($outgoingXfers as $xfer): ?>
	<tr>
		<td align="right"><?php echo number_format($xfer->amount) ?></td>
		<td>
			<?php if ($xfer->target_char_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($xfer->target_char_id, $xfer->target_char_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($xfer->target_char_name) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars($xfer->target_char_id) ?></span>
			<?php endif ?>
		</td>
		<td><?php echo $this->formatDateTime($xfer->transfer_date) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p><?php echo htmlspecialchars(Flux::message('XferLogNotSent')) ?></p>
<?php endif ?>
