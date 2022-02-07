<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<?php if ($vending): ?>
    <h3 style="text-align:right; margin:0; padding:0;font-style: italic"><img style="position:relative;top:7px;" src="<?php echo $this->iconImage(671) ?>?nocache=<?php echo rand() ?>" /> <?php echo $vending->title ?> </h3>
    <h4 style="text-align:right; color:blue; margin:0; margin-bottom:15px; "> <?php echo $vending->map; ?>, <?php echo $vending->x; ?>, <?php echo $vending->y; ?> </h4>

    <?php if ($vending_items): ?>
        <table class="horizontal-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Refine</th>
                    <th>Slots</th>
                    <th>Slot 1</th>
                    <th>Slot 2</th>
                    <th>Slot 3</th>
                    <th>Slot 4</th>
                    <?php if($server->isRenewal): ?>
						<th><?php echo htmlspecialchars(Flux::message('ItemRandOptionsLabel')) ?></th>
                    <?php endif ?>
                    <th>Price</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vending_items as $item): ?>
                    <tr>
                        <td width="50" align="right"  style="">

                            <?php if ($auth->actionAllowed('item', 'view')): ?>
                                <a href="<?php echo $this->url('item', 'view', array("id" => $item->nameid)); ?>"><?php echo $item->nameid; ?></a>
                            <?php else: ?>
                                <?php echo $item->nameid ?>
                            <?php endif ?>


                        </td>
                        <td>

                            <img src="<?php echo $this->iconImage($item->nameid) ?>?nocache=<?php echo rand() ?>" />
                            <?php if ($auth->actionAllowed('item', 'view')): ?>
                                <a href="<?php echo $this->url('item', 'view', array("id" => $item->nameid)); ?>"><?php echo $item->item_name; ?></a>
                            <?php else: ?>
                                <?php echo $item->item_name ?>
                            <?php endif ?>
                            <?php if ($item->char_name): ?>
                                Of <?php echo $item->char_name ?>
                            <?php endif; ?>


                        </td>

                        <td>
                            <?php if ($item->refine > 0): ?>
                                <img src="<?php echo $this->iconImage(613) ?>?nocache=<?php echo rand() ?>" />
                                <strong><?php echo $item->refine ?></strong>
                            <?php endif; ?>

                            <?php if ($item->card0 == 255 && intval($item->card1 / 1280) > 0): ?>
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

                            <?php if ($item->card0 == 255 && array_key_exists($item->card1 % 1280, $itemAttributes)): ?>
                                <?php echo htmlspecialchars($itemAttributes[$item->card1 % 1280]) ?>
                            <?php endif ?>
                        </td>

                        <td>
                        <?php if ($item->slots): ?>
                            <?php echo htmlspecialchars(' [' . $item->slots . ']') ?>
                        <?php endif ?>
                        </td>

                        <td>
                            <?php if ($item->card0 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
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
                            <?php if ($item->card1 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 255 && $item->card0 != -256): ?>
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
                            <?php if ($item->card2 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
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
                            <?php if ($item->card3 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
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
                        <td style="color:goldenrod; text-shadow:1px 1px 0px brown;">
                            <?php echo number_format($item->price, 0, ',', ' '); ?> z
                        </td>

                        <td>
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
