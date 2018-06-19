<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form class="search-form" method="get">
    <?php echo $this->moduleActionFormInputs($params->get('module'), $params->get('action')); ?>
    <p>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($params->get('name')) ?>" />
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
                <th><?php echo $paginator->sortableColumn('title', 'Shop'); ?></th>
                <th><?php echo $paginator->sortableColumn('merchant', 'Merchant'); ?></th>
                <th>Position</th>
                <th><?php echo $paginator->sortableColumn('item_name', 'Item Name'); ?></th>
                <th>Card0</th>
                <th>Card1</th>
                <th>Card2</th>
                <th>Card3</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <a href="<?php echo $this->url('vending', 'viewshop', array("id" => $item->shop_id)); ?>"><?php echo $item->title; ?></a>
                    </td>
                    <td><?php echo $item->merchant; ?></td>
                    <td>
                        <?php echo sprintf('%s %s, %s', $item->map, $item->x, $item->y); ?>
                    </td>
                    <td>
                        <?php if ($itemImage = $this->iconImage($item->item_id)): ?>
                        <img src="<?php echo "$itemImage?nocache=" . rand() ?>" />
                        <?php endif; ?>
                        <?php if ($item->refine > 0): ?>
                            <strong>+<?php echo $item->refine ?></strong>
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
                                <?php echo htmlspecialchars($item->char_name . "'s") ?>
                            <?php else: ?>
                                <span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>'s
                            <?php endif ?>
                        <?php endif ?>

                        <?php if ($item->card0 == 255 && array_key_exists($item->card1 % 1280, $itemAttributes)): ?>
                            <?php echo htmlspecialchars($itemAttributes[$item->card1 % 1280]) ?>
                        <?php endif ?>
                        <?php if ($auth->actionAllowed('item', 'view')): ?>
                            <a href="<?php echo $this->url('item', 'view', array("id" => $item->item_id)); ?>"><?php echo $item->item_name; ?></a>
                        <?php else: ?>
                            <?php echo $item->item_name ?>
                        <?php endif ?>
                        <?php if ($item->char_name): ?>
                            Of <?php echo $item->char_name ?>
                        <?php endif; ?>
                        <?php if ($item->slots): ?>
                            <?php echo htmlspecialchars(' [' . $item->slots . ']') ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($item->card0 && ($item->type == 4 || $item->type == 5) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
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
                        <?php if ($item->card1 && ($item->type == 4 || $item->type == 5) && $item->card0 != 255 && $item->card0 != -256): ?>
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
                        <?php if ($item->card2 && ($item->type == 4 || $item->type == 5) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
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
                        <?php if ($item->card3 && ($item->type == 4 || $item->type == 5) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
                            <?php if (!empty($cards[$item->card3])): ?>
                                <?php echo $this->linkToItem($item->card3, $cards[$item->card3]) ?>
                            <?php else: ?>
                                <?php echo $this->linkToItem($item->card3, $item->card3) ?>
                            <?php endif ?>
                        <?php else: ?>
                            <span class="not-applicable">None</span>
                        <?php endif ?>
                    </td>

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
    <?php echo $paginator->getHTML() ?>
<?php else: ?>
    <p>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>