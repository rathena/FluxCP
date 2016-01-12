<?php if (!defined('FLUX_ROOT')) exit; ?>

<h2>Viewing NPC</h2>
<?php if($npc){ ?>
<h3><?=$npc->name . ($npc->is_shop ? ' (shop)' : '')?></h3>
<table class="vertical-table">
    <tr>
        <th>Name</th>
        <td><?=$npc->name?></td>
    </tr>
    <tr>
        <th>Map</th>
        <?php if($auth->actionAllowed('map', 'view')){ ?>
            <td><a href="<?=$this->url('map', 'view', array('map' => $npc->map))?>"><?=$npc->map?></a></td>
        <?php } else { ?>
            <td><?=$npc->map?></td>
        <?php } ?>
    </tr>
    <tr>
        <th>Coordinates</th>
        <td><?=$npc->x?>,<?=$npc->y?></td>
        <th>Type</th>
        <td><?=$npc->is_shop ? 'Shop' : 'NPC'?></td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <img src="<?=$this->npcImage($npc->sprite) ? $this->npcImage($npc->sprite) : ''?>" />
        </td>
        <td colspan="2" align="center">
            <div style="position:relative">
                <div style="
                    left: <?=$this->conv($npc->x, $map->x, $map, 300) - 5?>px;
                    bottom: <?=$this->conv($npc->y, $map->y, $map, 300) - 5?>px;
                position:absolute;width:10px;height:10px;background-color: green;border: 3px solid yellow"></div>
                <img height="300" width="300" src="<?php $image = $this->mapImage($npc->map); echo $image ? $image : ''?>" />
            </div>
        </td>
    </tr>
</table>
<?php if(sizeof($items)){ ?>

    <h3>Items for sale</h3>
<table class="vertical-table">
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Sell Price</th>
    </tr>
    <?php foreach($items as $item){ ?>
        <tr>
            <td><?php $img = $this->iconImage($item->item); if($img){
                    echo '<img src="' . $img . '">';
                }?></td>

            <?php if($auth->actionAllowed('item', 'view')){ ?>
                <td><a href="<?=$this->url('item', 'view', array('id' => $item->item))?>"><?=$item->name?></a></td>
            <?php } else { ?>
                <td><?=$item->name?></td>
            <?php } ?>
            <td><?=$item->price?></td>
        </tr>
    <?php } ?>


    </table>


<?php } ?>
<?php } else { ?>
    NPC not found.
<?php } ?>
