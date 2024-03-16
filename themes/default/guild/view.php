<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Viewing Guild</h2>
<?php if ($guild): ?>
<h3>Guild Information for “<?php echo htmlspecialchars($guild->name) ?>”</h3>
<table class="vertical-table">
	<tr>
		<th>Guild ID</th>
		<td><?php echo htmlspecialchars($guild->guild_id) ?></td>
		<th>Guild Name</th>
		<td><?php echo htmlspecialchars($guild->name) ?></td>
		<th>Emblem ID</th>
		<td><?php echo number_format($guild->emblem) ?></td>
		<td><img src="<?php echo $this->emblem($guild->guild_id) ?>" /></td>
	</tr>
	<tr>
		<th>Leader ID</th>
		<td><?php echo htmlspecialchars($guild->char_id) ?></td>
		<th>Leader Name</th>
		<td>
			<?php if ($auth->allowedToViewCharacter): ?>
				<?php echo $this->linkToCharacter($guild->char_id, $guild->guild_master) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($guild->guild_master) ?>
			<?php endif ?>
		</td>
		<th>Guild Level</th>
		<td colspan="2"><?php echo number_format($guild->guild_lv) ?></td>
	</tr>
	<tr>
		<th>Online Members</th>
		<td><?php echo number_format($guild->connect_member) ?></td>
		<th>Capacity</th>
		<td><?php echo number_format($guild->max_member) ?></td>
		<th>Average Level</th>
		<td colspan="2"><?php echo number_format($guild->average_lv) ?></td>
	</tr>
	<tr>
		<th>Guild EXP</th>
		<td><?php echo number_format($guild->exp) ?></td>
		<th>EXP until Level Up</th>
		<td><?php echo number_format($guild->next_exp) ?></td>
		<th>Skill Point</th>
		<td colspan="2"><?php echo number_format($guild->skill_point) ?></td>
	</tr>
	<tr>
		<th>Guild Notice 1</th>
		<td colspan="6">
			<?php if (trim($guild->mes1)): ?>
				<?php echo htmlspecialchars($guild->mes1) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Guild Notice 2</th>
		<td colspan="6">
			<?php if (trim($guild->mes2)): ?>
				<?php echo htmlspecialchars($guild->mes2) ?></td>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
</table>
<h3>Alliances of “<?php echo htmlspecialchars($guild->name) ?>”</h3>
<?php if ($alliances): ?>
	<p><?php echo htmlspecialchars($guild->name) ?> has <?php echo count($alliances) ?> Alliance(s).</p>
	<table class="vertical-table">
		<tr>
			<th>Guild ID</th>
			<th>Guild Name</th>
		</tr>
		<?php foreach ($alliances AS $alliance): ?>
		<tr>
			<td align="right">
				<?php if ($auth->allowedToViewGuild): ?>
					<?php echo $this->linkToGuild($alliance->alliance_id, $alliance->alliance_id) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($alliance->alliance_id) ?>
				<?php endif ?>
			</td>
			<td><?php echo htmlspecialchars($alliance->name) ?></td>
		</tr>
		<?php endforeach ?>
	</table>
<?php else: ?>
	<p>There are no alliances for this guild.</p>
<?php endif ?>
<h3>Oppositions of “<?php echo htmlspecialchars($guild->name) ?>”</h3>
<?php if ($oppositions): ?>
	<p><?php echo htmlspecialchars($guild->name) ?> has <?php echo count($oppositions) ?> Opposition(s).</p>
	<table class="vertical-table">
		<tr>
			<th>Guild ID</th>
			<th>Guild Name</th>
		</tr>
		<?php foreach ($oppositions AS $opposition): ?>
		<tr>
			<td align="right">
				<?php if ($auth->allowedToViewGuild): ?>
					<?php echo $this->linkToGuild($opposition->alliance_id, $opposition->alliance_id) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($opposition->alliance_id) ?>
				<?php endif ?>
			</td>
			<td><?php echo htmlspecialchars($opposition->name) ?></td>
		</tr>
		<?php endforeach ?>
	</table>
<?php else: ?>
	<p>There are no oppositions for this guild.</p>
<?php endif ?>
<h3>Guild Members of “<?php echo htmlspecialchars($guild->name) ?>”</h3>
<?php if ($members): ?>
	<p><?php echo htmlspecialchars($guild->name) ?> has <?php echo count($members) ?> guild member(s).</p>
	<table class="vertical-table">
		<tr>
			<th>Name</th>
			<th>Job Class</th>
			<th>Base Level</th>
			<th>Job Level</th>
			<th>EXP Devotion</th>
			<th>Position ID</th>
			<th>Position Name</th>
			<th>Guild Rights</th>
			<th>Tax</th>
			<th>Last Login</th>
		</tr>
		<?php foreach ($members AS $member): ?>
		<tr>
			<td align="right">
				<?php if ($auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($member->char_id, $member->name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($member->name) ?>
				<?php endif ?>
			</td>
			<td>
				<?php if ($job=$this->jobClassText($member->class)): ?>
					<?php echo htmlspecialchars($job) ?>
				<?php else: ?>
					<span class="not-applicable">Unknown</span>
				<?php endif ?>
			</td>
			<td><?php echo htmlspecialchars($member->base_level) ?></td>
			<td><?php echo htmlspecialchars($member->job_level) ?></td>
			<td><?php echo number_format($member->devotion) ?></td>
			<td><?php echo htmlspecialchars($member->position) ?></td>
			<td><?php echo htmlspecialchars($member->position_name) ?></td>
			<td>
				<?php if ($member->mode == 17): ?>
					<?php echo htmlspecialchars("Invite/Expel") ?>
				<?php elseif ($member->mode == 16): ?>
					<?php echo htmlspecialchars("Expel") ?>
				<?php elseif ($member->mode == 1): ?>
					<?php echo htmlspecialchars("Invite") ?>
				<?php elseif ($member->mode == 0): ?>
					<span class="not-applicable">None</span>
				<?php else: ?>
					<span class="not-applicable">Unknown</span>
				<?php endif ?>
			</td>
			<td><?php echo number_format($member->guild_tax) ?>%</td>
			<td><?php echo htmlspecialchars($member->lastlogin) ?></td>
		</tr>
		<?php endforeach ?>
	</table>
<?php else: ?>
	<p>There are no members in this guild.</p>
<?php endif ?>
<h3>Member Expulsions of “<?php echo htmlspecialchars($guild->name) ?>”</h3>
<?php if ($expulsions): ?>
	<p><?php echo htmlspecialchars($guild->name) ?> has <?php echo count($expulsions) ?> member expulsion(s).</p>
	<table class="vertical-table">
		<tr>
			<th>Account ID</th>
			<th>Character Name</th>
			<th>Expulsion Reason</th>
		</tr>
		<?php foreach ($expulsions AS $expulsion): ?>
		<tr>
			<td align="right">
				<?php if ($auth->allowedToViewAccount): ?>
					<?php echo $this->linkToAccount($expulsion->account_id, $expulsion->account_id) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($expulsion->account_id) ?>
				<?php endif ?>
			</td>
			<td><?php echo htmlspecialchars($expulsion->name) ?></td>
			<td>
			<?php if($expulsion->mes): ?>
				<?php echo htmlspecialchars($expulsion->mes) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
<?php else: ?>
	<p>There are no member expulsions for this guild.</p>
<?php endif ?>
<?php if (!Flux::config('GStorageLeaderOnly') || $amOwner || $auth->allowedToViewGuild): ?>
	<h3>Guild Storage Items of “<?php echo htmlspecialchars($guild->name) ?>”</h3>
	<?php if (Flux::config('GStorageLeaderOnly')): ?>
		<p>Note: Guild Storage Items are only visible to you, the guild leader.</p>
	<?php endif ?>
	<?php if ($items): ?>
		<p><?php echo htmlspecialchars($guild->name) ?> has <?php echo count($items) ?> guild storage item(s).</p>
		<table class="vertical-table">
			<tr>
				<th>Item ID</th>
				<th colspan="2">Name</th>
				<th>Amount</th>
				<th>Identified</th>
				<th>Broken</th>
				<th>Slot 1</th>
				<th>Slot 2</th>
				<th>Slot 3</th>
				<th>Slot 4</th>
				<?php if($server->isRenewal): ?>
					<th><?php echo htmlspecialchars(Flux::message('ItemRandOptionsLabel')) ?></th>
				<?php endif ?>
				<th>Extra</th>
				</th>
			</tr>
			<?php foreach ($items AS $item): ?>
			<?php $icon = $this->iconImage($item->nameid) ?>
			<tr>
				<td align="right"><?php echo $this->linkToItem($item->nameid, $item->nameid) ?></td>
				<?php if ($icon): ?>
				<td><img src="<?php echo htmlspecialchars($icon) ?>" /></td>
				<?php endif ?>
				<td<?php if (!$icon) echo ' colspan="2"' ?><?php if ($item->cardsOver) echo ' class="overslotted' . $item->cardsOver . '"'; else echo ' class="normalslotted"' ?>>
					<?php if ($item->refine > 0): ?>
						+<?php echo htmlspecialchars($item->refine) ?>
					<?php endif ?>
					<?php if ($item->card0 == 255 && intval($item->card1/1280) > 0): ?>
                        <?php $itemcard1 = intval($item->card1/1280); ?>
                        <?php for ($i = 0; $i < $itemcard1; $i++): ?>
							Very
						<?php endfor ?>
						Strong
					<?php endif ?>
					<?php if ($item->card0 == 254 || $item->card0 == 255): ?>
						<?php if ($item->char_name): ?>
							<?php if ($auth->actionAllowed('character', 'view') && ($isMine || (!$isMine && $auth->allowedToViewCharacter))): ?>
								<?php echo $this->linkToCharacter($item->char_id, $item->char_name, $session->serverName) . "'s" ?>
							<?php else: ?>
								<?php echo htmlspecialchars($item->char_name . "'s") ?>
							<?php endif ?>
						<?php else: ?>
							<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>'s
						<?php endif ?>
					<?php endif ?>
					<?php if ($item->card0 == 255 && array_key_exists($item->card1%1280, $itemAttributes)): ?>
						<?php echo htmlspecialchars($itemAttributes[$item->card1%1280]) ?>
					<?php endif ?>
					<?php if ($item->name_english): ?>
						<span class="item_name"><?php echo htmlspecialchars($item->name_english) ?></span>
					<?php else: ?>
						<span class="not-applicable">Unknown Item</span>
					<?php endif ?>
					<?php if ($item->slots): ?>
						<?php echo htmlspecialchars(' [' . $item->slots . ']') ?>
					<?php endif ?>
				</td>
				<td><?php echo number_format($item->amount) ?></td>
				<td>
					<?php if ($item->identify): ?>
						<span class="identified yes">Yes</span>
					<?php else: ?>
						<span class="identified no">No</span>
					<?php endif ?>
				</td>
				<td>
					<?php if ($item->attribute): ?>
						<span class="broken yes">Yes</span>
					<?php else: ?>
						<span class="broken no">No</span>
					<?php endif ?>
				</td>
				<td>
					<?php if($item->card0 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
						<?php if (!empty($cards[$item->card0])): ?>
							<?php echo $this->linkToItem($item->card0, $cards[$item->card0]) ?>
						<?php else: ?>
							<?php echo $this->linkToItem($item->card0, $item->card0) ?>
						<?php endif ?>
					<?php else: ?>
						<span class="not-applicable">None</span>
					<?php endif ?>
				</td>
				<td>
					<?php if($item->card1 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 255 && $item->card0 != -256): ?>
						<?php if (!empty($cards[$item->card1])): ?>
							<?php echo $this->linkToItem($item->card1, $cards[$item->card1]) ?>
						<?php else: ?>
							<?php echo $this->linkToItem($item->card1, $item->card1) ?>
						<?php endif ?>
					<?php else: ?>
						<span class="not-applicable">None</span>
					<?php endif ?>
				</td>
				<td>
					<?php if($item->card2 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
						<?php if (!empty($cards[$item->card2])): ?>
							<?php echo $this->linkToItem($item->card2, $cards[$item->card2]) ?>
						<?php else: ?>
							<?php echo $this->linkToItem($item->card2, $item->card2) ?>
						<?php endif ?>
					<?php else: ?>
						<span class="not-applicable">None</span>
					<?php endif ?>
				</td>
				<td>
					<?php if($item->card3 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
						<?php if (!empty($cards[$item->card3])): ?>
							<?php echo $this->linkToItem($item->card3, $cards[$item->card3]) ?>
						<?php else: ?>
							<?php echo $this->linkToItem($item->card3, $item->card3) ?>
						<?php endif ?>
					<?php else: ?>
						<span class="not-applicable">None</span>
					<?php endif ?>
				</td>
				<?php if($server->isRenewal): ?>
					<td>
						<?php if($item->rndopt): ?>
							<ul>
								<?php foreach($item->rndopt as $rndopt) echo "<li>".$this->itemRandOption($rndopt[0], $rndopt[1])."</li>"; ?>
							</ul>
						<?php else: ?>
							<span class="not-applicable">None</span>
						<?php endif ?>
					</td>
				<?php endif ?>
				<td>
					<?php if($item->bound == 1):?>
						Account Bound
					<?php elseif($item->bound == 2):?>
						Guild Bound
					<?php elseif($item->bound == 3):?>
						Party Bound
					<?php elseif($item->bound == 4):?>
						Character Bound
					<?php else:?>
							<span class="not-applicable">None</span>
					<?php endif ?>
				</td>
			</tr>
			<?php endforeach ?>
		</table>
	<?php else: ?>
		<p>There are no guild storage items for this guild.</p>
	<?php endif ?>
<?php endif ?>
<?php else: ?>
<p>No such guild was found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
