<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('GenderChangeHeading')) ?></h2>
<?php if ($cost): ?>
<p>
	<?php printf(Flux::message('GenderChangeCost'), '<span class="remaining-balance">'.number_format((int)$cost).'</span>') ?>
	<?php printf(Flux::message('GenderChangeBalance'), '<span class="remaining-balance">'.number_format((int)$session->account->balance).'</span>') ?>
</p>
<?php if (!$hasNecessaryFunds): ?>
<p><?php echo htmlspecialchars(Flux::message('GenderChangeNoFunds')) ?></p>
<?php elseif ($auth->allowedToAvoidSexChangeCost): ?>
<p><?php echo htmlspecialchars(Flux::message('GenderChangeNoCost')) ?></p>
<?php endif ?>
<?php endif ?>

<?php if ($hasNecessaryFunds): ?>
<?php if (empty($errorMessage)): ?>
<p><strong><?php echo htmlspecialchars(Flux::message('NoteLabel')) ?>:</strong> <?php printf(Flux::message('GenderChangeCharInfo'), '<em>'.implode(', ', array_values($badJobs)).'</em>') ?>.</p>
<h3><?php echo htmlspecialchars(Flux::message('GenderChangeSubHeading')) ?></h3>
<?php else: ?>
<p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="changegender" value="1" />
	<table class="generic-form-table">
		<tr>
			<td>
				<p>
					<?php printf(Flux::message('GenderChangeFormText'), '<strong>'.strtolower($this->genderText($session->account->sex == 'M' ? 'F' : 'M')).'</strong>') ?>
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<p>
					<button type="submit"
						onclick="return confirm('<?php echo str_replace("\'", "\\'", Flux::message('GenderChangeConfirm')) ?>')">
							<strong><?php echo htmlspecialchars(Flux::message('GenderChangeButton')) ?></strong>
					</button>
				</p>
			</td>
		</tr>
	</table>
</form>
<?php endif ?>
