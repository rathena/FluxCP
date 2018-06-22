<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<?php if ($vending): ?>
    <h3 style="text-align:right; margin:0; padding:0;font-style: italic"><img style="position:relative;top:7px;" src="<?php echo $this->iconImage(671) ?>?nocache=<?php echo rand() ?>" /> <?php echo $vending->title ?> </h3>
    <h4 style="text-align:right; color:blue; margin:0; margin-bottom:15px; "> <?php echo $vending->map; ?>, <?php echo $vending->x; ?>, <?php echo $vending->y; ?> </h4>

    <?php if ($items): ?>
        <table class="horizontal-table">
            <thead>
                <tr>
					<th width="40">Item ID</th>
                    <th colspan="2">Item Name</th>
                    <th>Card0</th>
                    <th>Card1</th>
                    <th>Card2</th>
                    <th>Card3</th>
                    <th width="100">Price</th>
                    <th width="50">Amount</th>
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
				</tr>
				<?php endforeach ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
    <?php endif ?>
<?php else: ?>
    <p>No Vendor found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
