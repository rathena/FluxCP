<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Viewing <?php echo htmlspecialchars(Flux::message('StorageGroup.'.$type)); ?> Item</h2>
<?php if ($item): ?>
<?php $icon = $this->iconImage($item->item_id); ?>
<h3>
	<?php if ($icon): ?><img src="<?php echo $icon ?>" /><?php endif ?>
	#<?php echo htmlspecialchars($item->item_id) ?>: <?php echo htmlspecialchars($item->name) ?>
</h3>
<table class="vertical-table">
	<tr>
		<th>Item ID</th>
		<td><?php echo htmlspecialchars($item->item_id) ?></td>
		<?php if ($image=$this->itemImage($item->item_id)): ?>
		<td rowspan="<?php echo ($server->isRenewal)?9:8 ?>" style="width: 150px; text-align: center; vertical-alignment: middle">
			<img src="<?php echo $image ?>" />
		</td>
		<?php endif ?>
		<th>For Sale</th>
		<td>
			<?php if ($item->cost): ?>
				<span class="for-sale yes">
					Yes
				</span>
			<?php else: ?>
				<span class="for-sale no">
					No
				</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Identifier</th>
		<td><?php echo htmlspecialchars($item->identifier) ?></td>
		<th>Credit Price</th>
		<td>
			<?php if ($item->cost): ?>
				<?php echo number_format((int)$item->cost) ?>
			<?php else: ?>
				<span class="not-applicable">Not For Sale</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Name</th>
		<td><?php echo htmlspecialchars($item->name) ?></td>
		<th>Type</th>
		<td><?php echo $this->itemTypeText($item->type) ?><?php if($item->subtype) echo ' - '.$this->itemSubTypeText($item->type, $item->subtype) ?></td>
	</tr>
	<tr>
		<th>NPC Buy</th>
		<td><?php echo number_format((int)$item->price_buy) ?></td>
		<th>Weight</th>
		<td><?php echo round($item->weight, 1) ?></td>
	</tr>
	<tr>
		<th>NPC Sell</th>
		<td>
			<?php if (is_null($item->price_sell) && $item->price_buy): ?>
				<?php echo number_format(floor($item->price_buy / 2)) ?>
			<?php else: ?>
				<?php echo number_format((int)$item->price_sell) ?>
			<?php endif ?>
		</td>
		<th>Weapon Level</th>
		<td><?php echo number_format((int)$item->weapon_level) ?></td>
	</tr>
	<tr>
		<th>Range</th>
		<td><?php echo number_format((int)$item->range) ?></td>
		<th>Defense</th>
		<td><?php echo number_format((int)$item->defense) ?></td>
	</tr>
	<tr>
		<th>Slots</th>
		<td><?php echo number_format((int)$item->slots) ?></td>
		<th>Refineable</th>
		<td>
			<?php if ($item->refineable): ?>
				Yes
			<?php else: ?>
				No
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Attack</th>
		<td><?php echo number_format((int)$item->attack) ?></td>
		<th>Min Equip Level</th>
		<td>
			<?php if ($item->equip_level_min == 0): ?>
				<span class="not-applicable">None</span>
			<?php else: ?>
				<?php echo number_format((int)$item->equip_level_min) ?>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<?php if($server->isRenewal): ?>
			<th>MATK</th>
			<td><?php echo number_format((int)$item->magic_attack) ?></td>
		<?php endif ?>
		<th>Max Equip Level</th>
		<td colspan="<?php echo $image ? 0 : 3 ?>">
			<?php if ($item->equip_level_max == 0): ?>
				<span class="not-applicable">None</span>
			<?php else: ?>
				<?php echo number_format((int)$item->equip_level_max) ?>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Slot 1</th>
		<td colspan="<?php echo $image ? 2 : 1 ?>">
			<?php if($itemData->card0 && ($itemData->type == $type_list['armor'] || $itemData->type == $type_list['weapon']) && $itemData->card0 != 254 && $itemData->card0 != 255 && $itemData->card0 != -256): ?>
				<?php if (!empty($item_cards[$itemData->card0])): ?>
					<?php echo $this->linkToItem($itemData->card0, $item_cards[$itemData->card0]) ?>
				<?php else: ?>
					<?php echo $this->linkToItem($itemData->card0, $itemData->card0) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
		<th>Slot 2</th>
		<td>
			<?php if($itemData->card1 && ($itemData->type == $type_list['armor'] || $itemData->type == $type_list['weapon']) && $itemData->card0 != 255 && $itemData->card0 != -256): ?>
				<?php if (!empty($item_cards[$itemData->card1])): ?>
					<?php echo $this->linkToItem($itemData->card1, $item_cards[$itemData->card1]) ?>
				<?php else: ?>
					<?php echo $this->linkToItem($itemData->card1, $itemData->card1) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Slot 3</th>
		<td colspan="<?php echo $image ? 2 : 1 ?>">
			<?php if($itemData->card2 && ($itemData->type == $type_list['armor'] || $itemData->type == $type_list['weapon']) && $itemData->card0 != 254 && $itemData->card0 != 255 && $itemData->card0 != -256): ?>
				<?php if (!empty($item_cards[$itemData->card2])): ?>
					<?php echo $this->linkToItem($itemData->card2, $item_cards[$itemData->card2]) ?>
				<?php else: ?>
					<?php echo $this->linkToItem($itemData->card2, $itemData->card2) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
		<th>Slot 4</th>
		<td>
			<?php if($itemData->card3 && ($itemData->type == $type_list['armor'] || $itemData->type == $type_list['weapon']) && $itemData->card0 != 254 && $itemData->card0 != 255 && $itemData->card0 != -256): ?>
				<?php if (!empty($item_cards[$itemData->card3])): ?>
					<?php echo $this->linkToItem($itemData->card3, $item_cards[$itemData->card3]) ?>
				<?php else: ?>
					<?php echo $this->linkToItem($itemData->card3, $itemData->card3) ?>
				<?php endif ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Refine</th>
		<td colspan="<?php echo $image ? 2 : 1 ?>">
			<?php echo $itemData->refine ? "+".$itemData->refine : 0; ?>
		</td>
		<th>Broken</th>
		<td>
			<?php if ($itemData->attribute): ?>
				<span class="broken yes">Yes</span>
			<?php else: ?>
				<span class="broken no">No</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Enchant grade</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if($itemData->enchantgrade): ?>
				<?php echo htmlspecialchars(Flux::message('EnchantGradeClass.'.$itemData->enchantgrade)); ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif;?>
		</td>
	</tr>
	<tr>
		<th>Bound</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if($itemData->bound == 1):?>
				Account Bound
			<?php elseif($itemData->bound == 2):?>
				Guild Bound
			<?php elseif($itemData->bound == 3):?>
				Party Bound
			<?php elseif($itemData->bound == 4):?>
				Character Bound
			<?php else:?>
					<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<?php if ($server->isRenewal): ?>
		<tr>
			<th>Random options</th>
			<td colspan="<?php echo $image ? 4 : 3 ?>">
				<?php if($itemData->rndopt): ?>
					<ul>
						<?php foreach($itemData->rndopt as $rndopt) echo "<li>".$this->itemRandOption($rndopt[0], $rndopt[1])."</li>"; ?>
					</ul>
				<?php else: ?>
					<span class="not-applicable">None</span>
				<?php endif ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if ($auth->allowedToSeeItemUniqueID): ?>
		<tr>
			<th>Unique ID</th>
			<td colspan="<?php echo $image ? 4 : 3 ?>">
				<?php if ($itemData->unique_id == 0): ?>
					<span class="not-applicable">None</span>
				<?php else: ?>
					<?php echo $itemData->unique_id ?>
				<?php endif ?>
			</td>
		</tr>
	<?php endif; ?>
	<tr>
		<th>Equip Locations</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($equip_locations=$this->equipLocations($equip_locs)): ?>
				<?php echo $equip_locations ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equip Upper</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($this->equipUpper($upper)): ?>
				<?php echo htmlspecialchars(implode(' / ', $this->equipUpper($upper))) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equippable Jobs</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($this->equippableJobs($jobs)): ?>
				<?php echo htmlspecialchars(implode(' / ', $this->equippableJobs($jobs))) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equip Gender</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($item->gender == 'Female'): ?>
				Female
			<?php elseif ($item->gender == 'Male'): ?>
				Male
			<?php elseif ($item->gender == 'Both' || $item->gender == NULL): ?>
				Both (Male and Female)
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Trade restriction</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($this->tradeRestrictions($restrictions)): ?>
				<?php echo htmlspecialchars(implode(' / ', $this->tradeRestrictions($restrictions))) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<?php if (($isCustom && $auth->allowedToSeeItemDb2Scripts) || (!$isCustom && $auth->allowedToSeeItemDbScripts)): ?>
	<tr>
		<th>Item Use Script</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($script=$this->displayScript($item->script)): ?>
				<?php echo $script ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equip Script</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($script=$this->displayScript($item->equip_script)): ?>
				<?php echo $script ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Unequip Script</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($script=$this->displayScript($item->unequip_script)): ?>
				<?php echo $script ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<?php endif ?>
    <?php if(Flux::config('ShowItemDesc')):?>
	<tr>
		<th>Description</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if($item->itemdesc): ?>
                <?php echo $item->itemdesc ?>
            <?php else: ?>
                <span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
    <?php endif ?>
    
</table>
<?php else: ?>
<p>No such item was found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
