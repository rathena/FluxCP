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
		<label for="card">Card:</label>
		<input type="text" name="card" id="card" value="<?php echo htmlspecialchars($params->get('card')) ?>" />
		...
		<label for="map">Map:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map')) ?>" />
		...
		<label for="unique_id">Item Unique ID:</label>
		<input type="text" name="unique_id" id="unique_id" value="<?php echo htmlspecialchars($params->get('unique_id')) ?>" style="width:120px;" />
		...
		<br />
		<br />
		<label>Pick type:</label><!-- shared same values -->
		<?php foreach (Flux::config('PickTypes')->toArray() as $picktype => $pickname): ?>
			<label title="<?php echo $pickname ?>"><input type="checkbox" name="type[<?php echo $picktype ?>]" value="1" <?php if (in_array($picktype,$type)) echo " checked=\"yes\" " ?> /> <?php echo $pickname ?> ..</label>
		<?php endforeach ?>
		<br />
		<br />
		<label>Bound:</label>
		<label><input type="checkbox" name="bound[1]" value="1" <?php if (in_array(1,$bound)) echo " checked=\"yes\" " ?>/> <?php echo Flux::message(Flux::config('BoundLabels')->get('1')); ?> ..</label>
		<label><input type="checkbox" name="bound[2]" value="1" <?php if (in_array(2,$bound)) echo " checked=\"yes\" " ?>/> <?php echo Flux::message(Flux::config('BoundLabels')->get('2')); ?> ..</label>
		<label><input type="checkbox" name="bound[3]" value="1" <?php if (in_array(3,$bound)) echo " checked=\"yes\" " ?>/> <?php echo Flux::message(Flux::config('BoundLabels')->get('3')); ?> ..</label>
		<label><input type="checkbox" name="bound[4]" value="1" <?php if (in_array(4,$bound)) echo " checked=\"yes\" " ?>/> <?php echo Flux::message(Flux::config('BoundLabels')->get('4')); ?> ..</label>
		<label for="option">Has Random Option:</label>
		<input type="checkbox" name="option" id="option" value="1" <?php if ($params->get('option')) echo "checked=\"yes\"" ?> />
		...
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
		<th>Extra</th>
	</tr>
	<?php foreach ($picks as $idx => $pick): ?>
	<tr <?php if (($pick->bound)) echo "class=\"bound-item\""; ?>>
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
		</td>
		<td><?php echo $pick->amount >= 0 ? '+'.number_format($pick->amount) : number_format($pick->amount) ?></td>
		<td><?php echo $pick->refine ?></td>
		<!-- Card0 -->
		<td>
			<?php if ($pick->card0_name && !$pick->special): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card0, $pick->card0_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card0_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card0 && !$pick->special): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card0, $pick->card0) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card0) ?>
				<?php endif ?>
			<?php elseif ($viewCardValue && $pick->card0): ?>
				<?php echo $pick->card0; ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card1 -->
		<td>
			<?php if ($pick->card1_name && !$pick->special): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card1, $pick->card1_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card1_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card1 && !$pick->special): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card1, $pick->card1) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card1) ?>
				<?php endif ?>
			<?php elseif ($viewCardValue && $pick->card1): ?>
				<?php echo $pick->card1; ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card2 -->
		<td>
			<?php if ($pick->card2_name && !$pick->special): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card2, $pick->card2_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card2_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card2 && !$pick->special): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card2, $pick->card2) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card2) ?>
				<?php endif ?>
			<?php elseif ($viewCardValue && $pick->card2): ?>
				<?php echo $pick->card2; ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<!-- Card3 -->
		<td>
			<?php if ($pick->card3_name && !$pick->special): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card3, $pick->card3_name) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card3_name) ?>
				<?php endif ?>
			<?php elseif ($pick->card3 && !$pick->special): ?>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($pick->card3, $pick->card3) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($pick->card3) ?>
				<?php endif ?>
			<?php elseif ($viewCardValue && $pick->card3): ?>
				<?php echo $pick->card3; ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
			<?php endif ?>
		</td>
		<td>
			<?php if ($pick->map): ?>
				<?php echo htmlspecialchars(basename($pick->map, '.gat')) ?>
			<?php else: ?>
				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
			<?php endif ?>
		</td>
		<td><!-- 'Extra' column to show slight info -->
			<?php if ($pick->options): ?>
				<?php $pick->extra = "&#60;".$pick->options."&#62;"; ?>
			<?php endif ?>
			<?php if ($pick->bound): ?>
				<?php $pick->extra .= "&#60;B&#62;"; ?>
			<?php endif ?>
			<?php if ($pick->unique_id): ?>
				<?php $pick->extra .= "&#60;U&#62;"; ?>
			<?php endif ?>

			<?php if ($pick->extra): ?>
				<a title="Click to check extra values" class="item-options-toggle" onclick="toggleOption(<?php echo $idx ?>)"><?php echo $pick->extra ?></a>
			<?php endif ?>
		</td>
	</tr>
	<?php if ($pick->extra): ?>
	<tr id="item-options-<?php echo $idx ?>" style="display:none;" class="picklog-extra-row <?php if ($pick->bound) echo "bound-item"; ?>">
		<td colspan="2">
			Bound: <?php echo ($pick->bound) ? Flux::message(Flux::config('BoundLabels')->get($pick->bound)) : Flux::message('NoneLabel'); ?>
		</td>
		<td colspan="5">
			Options:
			<?php
			if ($pick->options) {
				$str  = '<ol class="item-options">';
				if ($pick->option_id0) $str .= '<li>'.Flux::getRandomOption($pick->option_id0, $pick->option_val0, $pick->option_parm0).'</li>';
				if ($pick->option_id1) $str .= '<li>'.Flux::getRandomOption($pick->option_id1, $pick->option_val1, $pick->option_parm1).'</li>';
				if ($pick->option_id2) $str .= '<li>'.Flux::getRandomOption($pick->option_id2, $pick->option_val2, $pick->option_parm2).'</li>';
				if ($pick->option_id3) $str .= '<li>'.Flux::getRandomOption($pick->option_id3, $pick->option_val3, $pick->option_parm3).'</li>';
				if ($pick->option_id4) $str .= '<li>'.Flux::getRandomOption($pick->option_id4, $pick->option_val4, $pick->option_parm4).'</li>';
				$str .= '</ol>';
				echo $str;
			}
			?>
		</td>
		<td>
			<?php if ($pick->options && $viewRandomOpt): ?>
				OptID:
			<?php
				$str  = '<ol class="item-options-real">';
				if ($pick->option_id0) $str .= '<li title="Random Option ID">'.$pick->option_id0.'</li>';
				if ($pick->option_id1) $str .= '<li title="Random Option ID">'.$pick->option_id1.'</li>';
				if ($pick->option_id2) $str .= '<li title="Random Option ID">'.$pick->option_id2.'</li>';
				if ($pick->option_id3) $str .= '<li title="Random Option ID">'.$pick->option_id3.'</li>';
				if ($pick->option_id4) $str .= '<li title="Random Option ID">'.$pick->option_id4.'</li>';
				$str .= '</ol>';
				echo $str;
			?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($pick->options && $viewRandomOpt): ?>
				OptVal:
			<?php
				$str  = '<ol class="item-options-real">';
				if ($pick->option_id0) $str .= '<li title="Random Option Value">'.$pick->option_val0.'</li>';
				if ($pick->option_id1) $str .= '<li title="Random Option Value">'.$pick->option_val1.'</li>';
				if ($pick->option_id2) $str .= '<li title="Random Option Value">'.$pick->option_val2.'</li>';
				if ($pick->option_id3) $str .= '<li title="Random Option Value">'.$pick->option_val3.'</li>';
				if ($pick->option_id4) $str .= '<li title="Random Option Value">'.$pick->option_val4.'</li>';
				$str .= '</ol>';
				echo $str;
			?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($pick->options && $viewRandomOpt): ?>
				OptParm:
			<?php
				$str  = '<ol class="item-options-real">';
				if ($pick->option_id0) $str .= '<li title="Random Option Param">'.$pick->option_parm0.'</li>';
				if ($pick->option_id1) $str .= '<li title="Random Option Param">'.$pick->option_parm1.'</li>';
				if ($pick->option_id2) $str .= '<li title="Random Option Param">'.$pick->option_parm2.'</li>';
				if ($pick->option_id3) $str .= '<li title="Random Option Param">'.$pick->option_parm3.'</li>';
				if ($pick->option_id4) $str .= '<li title="Random Option Param">'.$pick->option_parm4.'</li>';
				$str .= '</ol>';
				echo $str;
			?>
			<?php endif ?>
		</td>
		<td colspan="2"><a title="Unique ID"><?php echo $pick->unique_id ?></a></td>
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
