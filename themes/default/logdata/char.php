<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('CharLogHeading')) ?></h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="account_id">Account ID:</label>
		<input type="text" name="account_id" id="account_id" value="<?php echo htmlspecialchars($params->get('account_id')) ?>" />
		...
		<label for="char_name">Char Name:</label>
		<input type="text" name="char_name" id="char_name" value="<?php echo htmlspecialchars($params->get('char_name')) ?>" />
		...
		<br />
		<br />
		<label>Action:</label>
		<label><input type="checkbox" name="char_action[1]" value="1" <?php if (in_array(1,$char_actions)) echo " checked=\"yes\" " ?> /> Char Select ..</label>
		<label><input type="checkbox" name="char_action[2]" value="1" <?php if (in_array(2,$char_actions)) echo " checked=\"yes\" " ?> /> Change Char Name ..</label>
		<label><input type="checkbox" name="char_action[3]" value="1" <?php if (in_array(3,$char_actions)) echo " checked=\"yes\" " ?> /> Make New Char ..</label>
		<label for="other_action">Other Action:</label>
		<input type="text" name="other_action" id="other_action" value="<?php echo htmlspecialchars($params->get('other_action')) ?>" />
		...
		<br />
		<br />
		<label for="from_date">Date from:</label>
		<input type="date" name="from_date" id="from_date" value="<?php echo htmlspecialchars($params->get('from_date')) ?>" />
		...
		<label for="to_date">Date to:</label>
		<input type="date" name="to_date" id="to_date" value="<?php echo htmlspecialchars($params->get('to_date')) ?>" />
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($chars1): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', Flux::message('CharLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_msg', Flux::message('CharLogMsgLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('account_id', Flux::message('CharLogAccountIDLabe')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_num', Flux::message('CharLogCharNumLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('name', Flux::message('CharLogCharNameLabel')) ?></th>
	</tr>
	<?php foreach ($chars1 as $char1): ?>
	<tr>
		<td align="center"><?php echo $this->formatDateTime($char1->time) ?></td>
		<td align="center"><?php echo htmlspecialchars($char1->char_msg) ?></td>
		<td align="center">
			<?php if ($char1->account_id): ?>
				<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
					<?php echo $this->linkToAccount($char1->account_id, $char1->account_id) ?>
				<?php else: ?>
					<?php echo $char1->account_id ?>
				<?php endif ?>
			<?php else: ?>	
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center"><?php echo htmlspecialchars($char1->char_num) ?></td>
		<td align="center"><?php echo htmlspecialchars($char1->name) ?></td>
		
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('CharLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
