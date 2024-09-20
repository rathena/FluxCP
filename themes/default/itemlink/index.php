<?php if (!defined('FLUX_ROOT')) exit; ?>

<?php if ($item): ?>
<?php $icon = $this->iconImage($nameid); ?>
<?php $image = $this->itemImage($nameid) ?>

<table class="vertical-table">
    <tr>
        <th colspan="<?php echo $image && $icon ? 1 : (($image || $icon) ? 2 : 3) ?>"> Item </th>
        <?php if ($icon): ?>
            <th><img src="<?php echo $icon ?>" /></td>
        <?php endif ?>
        <?php if ($image): ?>
            <td rowspan="<?php echo $isequip ? 4 : 3 ?>" style="width: 150px; text-align: center; vertical-alignment: middle">
                <img src="<?php echo $image ?>" />
            </td>
        <?php endif ?>
    </tr>
    <tr>
        <th> Name </th>
        <td>
            <?php $itemname = $item->name . ($isequip ? " [$item->slots]" : "") ?>
            <?php echo $this->linkToItem($nameid, $itemname) ?>
        </td>
    </tr>
    <tr>
        <th> Item ID </th>
        <td> <?php echo htmlspecialchars($nameid) ?> </td>
    </tr>
    <?php if ($isequip): ?>
    <tr>
        <th> Refine </th>
        <td> +<?php echo htmlspecialchars($refine) ?> </td>
    </tr>
    <?php endif ?>
    <?php if (Flux::config('ShowItemDesc')):?>
    <tr>
        <th> Description </th>
        <td> <?php echo htmlspecialchars($item->itemdesc) ?> </td>
    </tr>
    <?php endif ?>
    <?php if ($isequip): ?>
        <tr>
            <th colspan="<?php echo $image ? 4 : 3 ?>"> Slots </th>
        </tr>
        <?php foreach(range(1, 4) as $i): ?>
        <tr>
            <th> Slot <?php echo $i ?> </th>
            <?php if (count($cards) >= $i && $cards[$i - 1]): ?>
                <td class="display:flex, align-items: center; justify-content: center"><img src="<?php echo $this->iconImage($cards[$i - 1]->item_id) ?>" /></td>
                <td colspan="<?php echo $image ? 4 : 3 ?>">
                    <?php echo $this->linkToItem($cards[$i - 1]->item_id, $cards[$i - 1]->name) ?>
                </td>
            <?php elseif ($item->slots >= $i): ?>
                <td class="display:flex, align-items: center; justify-content: center"><img src="<?php echo $this->themePath('itemlink/emptysocket.png') ?>" /></td>
                <td><span class="not-applicable">Empty</span></td>
            <?php else: ?>
                <td class="display:flex, align-items: center; justify-content: center"><img src="<?php echo $this->themePath('itemlink/nosocket.png') ?>" /></td>
                <td><span class="not-applicable">None</span></td>
            <?php endif ?>
        <?php endforeach ?>
        <?php if (count($options) > 0): ?>
        <tr>
            <th colspan="<?php echo $image ? 4 : 3 ?>"> Options </th>
        </tr>
        <?php foreach(range(1, 5) as $i): ?>
            <?php if (count($options) >= $i && $options[$i - 1]): ?><tr>
                <td colspan="<?php echo $image ? 4 : 3 ?>">
                    <?php echo htmlspecialchars($this->itemRandOption($options[$i - 1]['opt'], $options[$i - 1]['val'])) ?>
                </td>
            </tr>
            <?php endif ?>
        <?php endforeach ?>
        <?php endif ?>
    <?php endif ?>
</table>

<?php else: ?>
<p>Item not found</p>
<?php endif ?>
