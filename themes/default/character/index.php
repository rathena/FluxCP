<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Characters</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="char_id">Character ID:</label>
		<input type="text" name="char_id" id="char_id" value="<?php echo htmlspecialchars($params->get('char_id') ?: '') ?>" />
		...
		<label for="account">Account:</label>
		<input type="text" name="account" id="account" value="<?php echo htmlspecialchars($params->get('account') ?: '') ?>" />
		...
		<label for="char_name">Character:</label>
		<input type="text" name="char_name" id="char_name" value="<?php echo htmlspecialchars($params->get('char_name') ?: '') ?>" />
		...
		<label for="char_class">Job Class:</label>
		<input type="text" name="char_class" id="char_class" value="<?php echo htmlspecialchars($params->get('char_class') ?: '') ?>" />
	</p>
	<p>
		<label for="base_level">Base Level:</label>
		<select name="base_level_op">
			<option value="eq"<?php if (($base_level_op=$params->get('base_level_op')) == 'eq') echo ' selected="selected"' ?>>is equal to</option>
			<option value="gt"<?php if ($base_level_op == 'gt') echo ' selected="selected"' ?>>is greater than</option>
			<option value="lt"<?php if ($base_level_op == 'lt') echo ' selected="selected"' ?>>is less than</option>
		</select>
		<input type="text" name="base_level" id="base_level" value="<?php echo htmlspecialchars($params->get('base_level') ?: '') ?>" />
		...
		<label for="job_level">Job Level:</label>
		<select name="job_level_op">
			<option value="eq"<?php if (($job_level_op=$params->get('job_level_op')) == 'eq') echo ' selected="selected"' ?>>is equal to</option>
			<option value="gt"<?php if ($job_level_op == 'gt') echo ' selected="selected"' ?>>is greater than</option>
			<option value="lt"<?php if ($job_level_op == 'lt') echo ' selected="selected"' ?>>is less than</option>
		</select>
		<input type="text" name="job_level" id="job_level" value="<?php echo htmlspecialchars($params->get('job_level') ?: '') ?>" />
		...
		<label for="zeny">Zeny:</label>
		<select name="zeny_op">
			<option value="eq"<?php if (($zeny_op=$params->get('zeny_op')) == 'eq') echo ' selected="selected"' ?>>is equal to</option>
			<option value="gt"<?php if ($zeny_op == 'gt') echo ' selected="selected"' ?>>is greater than</option>
			<option value="lt"<?php if ($zeny_op == 'lt') echo ' selected="selected"' ?>>is less than</option>
		</select>
		<input type="text" name="zeny" id="zeny" value="<?php echo htmlspecialchars($params->get('zeny') ?: '') ?>" />
	</p>
	<p>
		<label for="guild">Guild:</label>
		<input type="text" name="guild" id="guild" value="<?php echo htmlspecialchars($params->get('guild') ?: '') ?>" />
		...
		<label for="partner">Partner:</label>
		<input type="text" name="partner" id="partner" value="<?php echo htmlspecialchars($params->get('partner') ?: '') ?>" />
		...
		<label for="mother">Mother:</label>
		<input type="text" name="mother" id="mother" value="<?php echo htmlspecialchars($params->get('mother') ?: '') ?>" />
		...
		<label for="father">Father:</label>
		<input type="text" name="father" id="father" value="<?php echo htmlspecialchars($params->get('father') ?: '') ?>" />
		...
		<label for="child">Child:</label>
		<input type="text" name="child" id="child" value="<?php echo htmlspecialchars($params->get('child') ?: '') ?>" />
	</p>
	<p>	
		<label for="online">Online Status:</label>
		<select name="online" id="online">
			<option value=""<?php if (!($online=$params->get('online'))) echo ' selected="selected"' ?>>All</option>
			<option value="on"<?php if ($online == 'on') echo ' selected="selected"' ?>>Online</option>
			<option value="off"<?php if ($online == 'off') echo ' selected="selected"' ?>>Offline</option>
		</select>
		...
		<label for="slot">Slot Number:</label>
		<select name="slot_op">
			<option value="eq"<?php if (($slot_op=$params->get('slot_op')) == 'eq') echo ' selected="selected"' ?>>is equal to</option>
			<option value="gt"<?php if ($slot_op == 'gt') echo ' selected="selected"' ?>>is greater than</option>
			<option value="lt"<?php if ($slot_op == 'lt') echo ' selected="selected"' ?>>is less than</option>
		</select>
		<input type="text" name="slot" id="slot" value="<?php echo htmlspecialchars($params->get('slot') ?: '') ?>" />
		
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($characters): ?>
<?php echo $paginator->infoText() ?>
<table class="vertical-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('ch.char_id', 'Character ID') ?></th>
		<th><?php echo $paginator->sortableColumn('userid', 'Account') ?></th>
		<th><?php echo $paginator->sortableColumn('char_name', 'Character') ?></th>
		<th>Job Class</th>
		<th><?php echo $paginator->sortableColumn('ch.base_level', 'Base Level') ?></th>
		<th><?php echo $paginator->sortableColumn('ch.job_level', 'Job Level') ?></th>
		<th><?php echo $paginator->sortableColumn('ch.zeny', 'Zeny') ?></th>
		<th colspan="2"><?php echo $paginator->sortableColumn('guild_name', 'Guild') ?></th>
		<th><?php echo $paginator->sortableColumn('partner_name', 'Partner') ?></th>
		<th><?php echo $paginator->sortableColumn('mother_name', 'Mother') ?></th>
		<th><?php echo $paginator->sortableColumn('father_name', 'Father') ?></th>
		<th><?php echo $paginator->sortableColumn('child_name', 'Child') ?></th>
		<th><?php echo $paginator->sortableColumn('ch.online', 'Online') ?></th>
		<th><?php echo $paginator->sortableColumn('ch.char_num', 'Slot') ?></th>
	</tr>
	<?php foreach ($characters as $char): ?>
	<tr>
		<td align="right">
			<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
				<?php echo $this->linkToCharacter($char->char_id, $char->char_id) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($char->char_id) ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
				<?php echo $this->linkToAccount($char->account_id, $char->userid) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($char->userid) ?>
			<?php endif ?>
		</td>
		<td><?php echo htmlspecialchars($char->char_name) ?></td>
		<td>
			<?php if ($job=$this->jobClassText($char->class)): ?>
				<?php echo htmlspecialchars($job) ?>
			<?php else: ?>
				<span class="not-applicable">Unknown</span>
			<?php endif ?>
		</td>
		<td><?php echo number_format((int)$char->base_level) ?></td>
		<td><?php echo number_format((int)$char->job_level) ?></td>
		<td><?php echo number_format((int)$char->zeny) ?></td>
		<?php if ($char->guild_name): ?>
			<?php if ($char->emblem): ?>
			<td width="24"><img src="<?php echo $this->emblem($char->guild_id) ?>" /></td>
			<?php endif ?>
			<td<?php if (!$char->emblem) echo ' colspan="2"' ?>>
				<?php if ($auth->actionAllowed('guild', 'view') && $auth->allowedToViewGuild): ?>
					<?php echo $this->linkToGuild($char->guild_id, $char->guild_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($char->guild_name) ?>
				<?php endif ?>
			</td>
		<?php else: ?>
			<td colspan="2" align="center"><span class="not-applicable">None</span></td>
		<?php endif ?>
		<td>
			<?php if ($char->partner_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($char->partner_id, $char->partner_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($char->partner_name) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($char->mother_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($char->mother_id, $char->mother_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($char->mother_name) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($char->father_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($char->father_id, $char->father_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($char->father_name) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($char->child_name): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($char->child_id, $char->child_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($char->child_name) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($char->online): ?>
				<span class="online">Online</span>
			<?php else: ?>
				<span class="offline">Offline</span>
			<?php endif ?>
		</td>
		<td><?php echo $char->char_num + 1 ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No characters found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
