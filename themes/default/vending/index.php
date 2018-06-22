<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>

<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="item">Item ID/Name:</label>
		<input type="text" name="item" id="item" value="<?php echo htmlspecialchars($params->get('item')) ?>" />
		...
		<label for="refine">Refine:</label>
		<input type="text" name="refine" id="refine" value="<?php echo htmlspecialchars($params->get('refine')) ?>" />
		...
		<label for="card">Cards/Enchant:</label>
		<input type="text" name="card" id="card" value="<?php echo htmlspecialchars($params->get('card')) ?>" />
		...
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>

<?php if ($items): ?>
	<?php echo $paginator->infoText() ?>
	<table class="horizontal-table">
		<thead>
			<tr>
				<th width="40"><?php echo $paginator->sortableColumn('nameid', 'ID') ?></th>
				<th colspan="2"><?php echo $paginator->sortableColumn('name_japanese', 'Name') ?></th>
				<th>Card0</th>
				<th>Card1</th>
				<th>Card2</th>
				<th>Card3</th>
				<th width="100"><?php echo $paginator->sortableColumn('price', 'Price') ?></th>
				<th width="50">Amount</th>
				<th width="100">Shop Name</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($items as $idx => $item): ?>
			<?php $icon = $this->iconImage($item->nameid) ?>
			<tr<?php if ($item->bound) echo ' class="bound-item"' ?>>
				<td align="right"><?php echo $this->linkToItem($item->nameid, $item->nameid) ?></td>
				<?php if ($icon): ?>
					<td><img src="<?php echo htmlspecialchars($icon) ?>" /></td>
				<?php endif ?>
				<td<?php if (!$icon) echo ' colspan="2"' ?><?php if ($item->cardsOver) echo ' class="overslotted' . $item->cardsOver . '"'; else echo ' class="normalslotted"' ?>>
					<?php if ($item->refine > 0): ?>
						+<?php echo htmlspecialchars($item->refine) ?>
					<?php endif ?>
					<?php if ($item->forged_prefix): ?>
						<?php echo $item->forged_prefix ?>
					<?php endif ?>
					<?php if ($item->is_forged || $item->is_creation): ?>
						<?php if ($item->char_name): ?>
							<?php $isMine = ($item->account_id == $session->account->account_id); ?>
							<?php if ($auth->actionAllowed('character', 'view') && ($isMine || (!$isMine && $auth->allowedToViewCharacter))): ?>
								<?php echo $this->linkToCharacter($item->char_id, $item->char_name, $session->serverName) . "'s" ?>
							<?php else: ?>
								<?php if ($item->is_forged): ?>
									<a href="<?php echo $this->url('ranking', 'blacksmith'); ?>" title="Click here to see Blacksmith rank"><?php echo $item->char_name; ?></a>'s
								<?php elseif ($item->is_creation): ?>
									<a href="<?php echo $this->url('ranking', 'alchemist'); ?>" title="Click here to see Alchemist rank"><?php echo $item->char_name; ?></a>'s
								<?php else: ?>
									<?php echo $item->char_name; ?>'s
								<?php endif ?>
							<?php endif ?>
						<?php else: ?>
							<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>'s
						<?php endif ?>
					<?php endif ?>
					<?php if ($item->is_forged && $item->element): ?>
						<?php echo $item->element ?>
					<?php endif ?>
					<?php if ($item->name_japanese): ?>
						<span class="item_name"><?php echo htmlspecialchars($item->name_japanese) ?></span>
					<?php else: ?>
						<span class="not-applicable">Unknown Item</span>
					<?php endif ?>
					<?php if ($item->slots): ?>
						<?php echo htmlspecialchars(' [' . $item->slots . ']') ?>
					<?php endif ?>
					<?php if ($item->options): ?>
						<a title="Click to check options" class="item-options-toggle" onclick="toggleOption(<?php echo $idx ?>)"><?php echo "[".$item->options." Options]" ?></a>
						<?php echo $this->showItemRandomOption($item, $idx) ?>
					<?php endif ?>
				</td>
				<td>
					<?php if ($item->card0): ?>
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
					<?php if ($item->card1): ?>
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
					<?php if ($item->card2): ?>
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
					<?php if ($item->card3): ?>
						<?php if (!empty($cards[$item->card3])): ?>
							<?php echo $this->linkToItem($item->card3, $cards[$item->card3]) ?>
						<?php else: ?>
							<?php echo $this->linkToItem($item->card3, $item->card3) ?>
						<?php endif ?>
					<?php else: ?>
						<span class="not-applicable">None</span>
					<?php endif ?>
				</td>
				<td align="right" class="price <?php echo $this->getPriceStyle($item->price) ?>">
					<?php echo number_format($item->price, 0, '.', ','); ?> z
				</td>
				<td align="right" width="50">
					<?php echo $item->amount ?>
				</td>
				<td>
					<?php if ($auth->actionAllowed('vending', 'viewshop')): ?>
						<span title="Click for items vended by this vendor and location"><a href="<?php echo $this->url('vending', 'viewshop', array("id" => $item->vending_id)); ?>"><?php echo $item->title; ?></a></span>
					<?php else: ?>
						<span title="Click for items vended by this vendor and location"><?php echo $item->title ?></span>
					<?php endif ?>
				</td>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
	<p>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
