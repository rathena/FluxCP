<?php
if (!defined('FLUX_ROOT')) exit; ?>

<h2><?php echo htmlspecialchars(Flux::message('HARTitle')) ?></h2>
<p class="toggler"><a href="javascript:toggleSearchForm()"><?php echo htmlspecialchars(Flux::message('HARSearchLink')) ?></a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module'), $params->get('action')) ?>
	<p>
		<label for="use_log_after"><?php echo htmlspecialchars(Flux::message('HARDateBetween')) ?>:</label>
		<input type="checkbox" name="use_log_after" id="use_log_after"<?php if ($params->get('use_log_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('log_after') ?>
		<label for="use_log_before">&mdash;</label>
		<input type="checkbox" name="use_log_before" id="use_log_before"<?php if ($params->get('use_log_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('log_before') ?>
	</p>
	<p>
		<label for="ip"><?php echo htmlspecialchars(Flux::message('HARIPAddress')) ?>:</label>
		<input type="text" name="ip" id="ip" value="<?php echo htmlspecialchars($params->get('ip')) ?>" />
		...
		<label for="char_name"><?php echo htmlspecialchars(Flux::message('HARCharacter')) ?>:</label>
		<input type="text" name="char_name" id="char_name" value="<?php echo htmlspecialchars($params->get('char_name')) ?>" />
		...
		<label for="account_id"><?php echo htmlspecialchars(Flux::message('HARAccountID')) ?>:</label>
		<input type="text" name="account_id" id="account_id" value="<?php echo htmlspecialchars($params->get('account_id')) ?>" />
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($harmonydata): ?>
<?php echo $paginator->infoText() ?>
<div class="adjust">
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('date', 'Date/Time') ?></th>
		<th><?php echo $paginator->sortableColumn('ip', 'IP Address') ?></th>
		<th><?php echo $paginator->sortableColumn('char_name', 'Character') ?></th>
		<th><?php echo $paginator->sortableColumn('account_id', 'Account ID') ?></th>
		<th><?php echo "Description" ?></th>
	</tr>
	<?php foreach ($harmonydata as $harmonyrow): ?>
	<tr>
		<td align="right"><font><?php echo htmlspecialchars($this->formatDateTime($harmonyrow->date)) ?></td>
		<td><font>
			<?php if ($auth->actionAllowed('account', 'index')): ?>
				<?php echo $this->linkToAccountSearch(array('last_ip' => $harmonyrow->ip), $harmonyrow->ip) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($harmonyrow->ip) ?>
			<?php endif ?>
		</td>
		<td><font>
			<?php if ($auth->actionAllowed('character', 'view') &&  $auth->allowedToViewCharacter && $harmonyrow->char_id): ?>
				<?php echo $this->linkToCharacter($harmonyrow->char_id, $harmonyrow->char_name, $serverName) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($harmonyrow->char_name) ?>
			<?php endif ?>
		</td>
		<td><font>
			<?php if ($harmonyrow->account_id && $auth->actionAllowed('account', 'view') && $auth->allowedToViewAccount): ?>
				<?php echo $this->linkToAccountSearch(array('account_id' => $harmonyrow->account_id), $harmonyrow->account_id) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($harmonyrow->account_id) ?>
			<?php endif ?>
		</td>
		<td><font><?php echo mb_convert_encoding(htmlspecialchars($harmonyrow->data), "UTF-8") ?></td>
	</tr>
	<?php endforeach ?>
</table>
</div>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('HARNoData')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('HARGoBack')) ?></a>.
</p>
<?php endif ?>