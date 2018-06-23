<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('PickLogHeading')) ?></h2>

<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="nameid">Item ID:</label>
		<input type="text" name="nameid" id="nameid" value="<?php echo htmlspecialchars($params->get('nameid')) ?>" />
		...
		<label for="char_id">Char ID:</label>
		<input type="text" name="char_id" id="char_id" value="<?php echo htmlspecialchars($params->get('char_id')) ?>" />
		...
		<label for="map">Map:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map')) ?>" />
		...
		<label for="card">Card:</label>
		<input type="text" name="card" id="card" value="<?php echo htmlspecialchars($params->get('card')) ?>" />
		<br />
		<label for="datefrom">Date from:</label>
		<input type="date" name="datefrom" id="datefrom" value="<?php echo htmlspecialchars($params->get('datefrom')) ?>" />
		...
		<label for="dateto">Date to:</label>
		<input type="date" name="dateto" id="dateto" value="<?php echo htmlspecialchars($params->get('dateto')) ?>" />
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>

<?php if ($picks): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('time', Flux::message('PickLogDateLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('char_id', Flux::message('PickLogCharacterLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('type', Flux::message('PickLogTypeLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('nameid', Flux::message('PickLogItemLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('amount', Flux::message('PickLogAmountLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('refine', Flux::message('PickLogRefineLabel')) ?></th>
		<th><?php echo $paginator->sortableColumn('card0', Flux::message('PickLogCard0Label')) ?></th>
		<th><?php echo $paginator->sortableColumn('card1', Flux::message('PickLogCard1Label')) ?></th>
		<th><?php echo $paginator->sortableColumn('card2', Flux::message('PickLogCard2Label')) ?></th>
		<th><?php echo $paginator->sortableColumn('card3', Flux::message('PickLogCard3Label')) ?></th>
		<th><?php echo $paginator->sortableColumn('map', Flux::message('PickLogMapLabel')) ?></th>
	</tr>
	<?php foreach ($picks as $idx => $pick): ?>
	<tr>
		<td align="right"><?php echo $this->formatDateTime($pick->time) ?></td>
		<td>
			<?php if ($pick->char_name): ?>
				<?php if ($pick->type == 'M' || $pick->type == 'L'): ?>
					<?php if ($auth->actionAllowed('monster', 'view')): ?>
						<em><?php echo $this->linkToMonster($pick->char_id, $pick->char_name) ?></em>
					<?php else: ?>
						<em><?php echo htmlspecialchars($pick->char_name) ?></em>
					<?php endif ?>
				<?php else: ?>
					<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
						<strong><?php echo $this->linkToCharacter($pick->char_id, $pick->char_name) ?></strong>
					<?php else: ?>
						<strong><?php echo htmlspecialchars($pick->char_name) ?></strong>	
					<?php endif ?>
				<?php endif ?>
			<?php elseif ($pick->char_id): ?>
				<?php if ($pick->type == 'M' || $pick->type == 'L'): ?>
					<?php if ($auth->actionAllowed('monster', 'view')): ?>
						<em><?php echo $this->linkToMonster($pick->char_id, $pick->char_id) ?></em>
					<?php else: ?>
						<em><?php echo htmlspecialchars($pick->char_id) ?></em>
					<?php endif ?>
				<?php else: ?>
					<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
						<strong><?php echo $this->linkToCharacter($pick->char_id, $pick->char_id) ?></strong>
					<?php else: ?>
						<strong><?php echo htmlspecialchars($pick->char_id) ?></strong>	
					<?php endif ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($pick->pick_type): ?>
				<?php echo htmlspecialchars($pick->pick_type) ?>
			<?php elseif ($pick->type): ?>
				<?php echo $pick->type ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($pick->item_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->nameid, $pick->item_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->item_name) ?>
				<?php endif ?>
			<?php elseif ($pick->nameid): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->nameid, $pick->nameid) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->nameid) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
			<?php if ($pick->options): ?>
				<a title="Click to check options" class="item-options-toggle" onclick="toggleOption(<?php echo $idx ?>)"><?php echo "&#60;".$pick->options."&#62;" ?></a>
			<?php endif ?>
			<?php if ($pick->bound): ?>
				<a title="This item is <?php echo Flux::message(Flux::config('BoundLabels')->get($pick->bound)); ?> bound">&#60;B&#62;</a>
			<?php endif ?>
		</td>
		<td><?php echo $pick->amount >= 0 ? '+'.number_format($pick->amount) : number_format($pick->amount) ?></td>
		<td><?php echo $pick->refine ?></td>
		<!-- Non-special item -->
		<?php if (!$pick->special): ?>
		<!-- Card0 -->
		<td>
			<?php if ($pick->card0_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card0, $pick->card0_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card0_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card0): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card0, $pick->card0) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card0) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card1 -->
		<td>
			<?php if ($pick->card1_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card1, $pick->card1_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card1_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card1): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card1, $pick->card1) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card1) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card2 -->
		<td>
			<?php if ($pick->card2_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card2, $pick->card2_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card2_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card2): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card2, $pick->card2) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card2) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card3 -->
		<td>
			<?php if ($pick->card3_name): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card3, $pick->card3_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card3_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card3): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card3, $pick->card3) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card3) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Special item -->
		<?php else: ?>
		<td colspan="4">
			<?php if ($pick->is_forged || $pick->is_creation): ?>

				<?php if ($pick->forged_prefix): ?>
					<?php echo htmlspecialchars($pick->forged_prefix) ?>
				<?php endif ?>

				<?php if (array_key_exists($pick->creator_char_id, $creatorIDs) && $creatorIDs[$pick->creator_char_id]): ?>
					<?php $dispcharname = $creatorIDs[$pick->creator_char_id] ?>
				<?php else: ?>
					<?php $dispcharname = "[CID:".$pick->creator_char_id."]" ?>
				<?php endif ?>
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php $dispcharname = $this->linkToCharacter($pick->char_id, $dispcharname, $session->serverName); ?>
				<?php endif ?>
				<?php echo $dispcharname ?>'s

				<?php if ($pick->element): ?>
					<?php echo htmlspecialchars($pick->element) ?>
				<?php endif ?>

			<?php elseif ($pick->is_egg): ?>

				<?php if ($pick->egg_renamed): ?>
					<?php echo htmlspecialchars(Flux::message('PetRanamedLabel')) ?>
				<?php endif ?>

			<?php endif ?>
		</td>
		<?php endif ?>
		<td>
			<?php if ($pick->map): ?>
				<?php echo htmlspecialchars(basename($pick->map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
	</tr>
	<?php if ($pick->options): ?>
	<tr id="item-options-<?php echo $idx ?>" style="display:none;">
		<td colspan='11'>
		<?php
			$str  = '<ul class="item-options">';
			if ($pick->option_id0) $str .= '<li>'.Flux::getRandomOption($pick->option_id0, $pick->option_val0, $pick->option_parm0).'</li>';
			if ($pick->option_id1) $str .= '<li>'.Flux::getRandomOption($pick->option_id1, $pick->option_val1, $pick->option_parm1).'</li>';
			if ($pick->option_id2) $str .= '<li>'.Flux::getRandomOption($pick->option_id2, $pick->option_val2, $pick->option_parm2).'</li>';
			if ($pick->option_id3) $str .= '<li>'.Flux::getRandomOption($pick->option_id3, $pick->option_val3, $pick->option_parm3).'</li>';
			if ($pick->option_id4) $str .= '<li>'.Flux::getRandomOption($pick->option_id4, $pick->option_val4, $pick->option_parm4).'</li>';
			$str .= '</ul>';
			echo $str;
		?>
		</tr>
	</tr>
	<?php endif ?>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('PickLogNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
