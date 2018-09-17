<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('BranchLogHeading')) ?></h2>

<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="account_id">Account ID:</label>
		<input type="text" name="account_id" id="account_id" value="<?php echo htmlspecialchars($params->get('account_id')) ?>" />
		...
		<label for="char_id">Char ID:</label>
		<input type="text" name="char_id" id="char_id" value="<?php echo htmlspecialchars($params->get('char_id')) ?>" />
		...
		<label for="char_name">Char ID:</label>
		<input type="text" name="char_name" id="char_name" value="<?php echo htmlspecialchars($params->get('char_name')) ?>" />
		...
		<label for="map">Map:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map')) ?>" />
		...
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

<?php if ($branchs): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('branch_date', Flux::message('BranchLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('account_id', Flux::message('BranchLogAccountIDLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('BranchLogCharIDLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_name', Flux::message('BranchLogCharNameLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('BranchLogMapLabel')) ?></th>
	</tr>
	<?php foreach ($branchs as $branch): ?>
	<tr>
		<td align="center"><?php echo $this->formatDateTime($branch->branch_date) ?></td>
		<td align="center">
			<?php if ($branch->account_id): ?>
				<?php if ($auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
					<?php echo $this->linkToAccount($branch->account_id, $branch->account_id) ?>
				<?php else: ?>
					<?php echo $branch->account_id ?>
				<?php endif ?>
			<?php else: ?>	
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center">
			<?php if ($branch->char_id): ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($branch->char_id, $branch->char_id) ?>
				<?php else: ?>
					<?php echo $branch->char_id ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td align="center"><?php echo htmlspecialchars($branch->char_name) ?></td>
		<td align="center">
			<?php if (strlen(basename($branch->map, '.gat')) > 0): ?>
				<?php echo htmlspecialchars(basename($branch->map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('BranchLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
