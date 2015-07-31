<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>

<style>
    .points{position:absolute;border:3px double #ff00ff;}
    .hide{display:none;}
</style>
<h2>Map Database</h2>
<a style="display:block;" href="<?=$this->url('map')?>">Back to Map Database</a>

<?php if($map){ ?>

<table style="display:inline-block" class="vertical-table" style="margin-top:10px;">
    <tr>
        <td>
            <h3>Map "<b><?=$map->name?></b>"</h3>
            <div style="display:inline-block;position:relative;width:512px;height:512px">
                <img src="<?=$this->mapImage($map->name)?>" style="width:100%;height:100%;">

                <?php $isResp = false; foreach($mobs as $mob){ if(!$mob->x){continue;} $isResp = true; ?>

                    <div class="mob_spawn_<?=$mob->id?> points hide mob_resp" style="
                        width:<?=conv($mob->range_x, $map->x)?>px;
                        height:<?=conv($mob->range_y, $map->y)?>px;
                        left:<?=conv($mob->x, $map->x) - conv($mob->range_x, $map->x) / 2?>px;
                        bottom:<?=conv($mob->y, $map->y) - conv($mob->range_y, $map->y) / 2?>px;
                        "></div>

                <?php } ?>

            </div>
            <?php if($isResp){ ?>
                <div style="padding:20px;text-align:center;">
                    <button onclick="$('.points').removeClass('hide')">Show all monster respawn</button>
                    <button onclick="$('.points').addClass('hide')">Hide all monster respawn</button>
                </div>
            <?php } ?>
        </td>
        <td>
            <?php if(sizeof($mobs)){ ?>
                <table style="display:inline-block" class="vertical-table" style="margin-top:10px;">
                    <tr>
                        <th>Mob Name</th>
                        <th>Spawn</th>
                        <th>Respawn time</th>
                        <?php if($isResp){ ?>
                            <th>Respawn Area</th>
                        <?php } ?>
                    </tr>

                    <?php foreach($mobs as $mob){ ?>

                        <tr>
                            <td><a href="<?=$this->url('monster_new', 'view')?>&id=<?=$mob->mob_id?>"><?=$mob->name?></td>
                            <td><?=$mob->count?></td>
                            <td><b><?=ceil($mob->time_to / 60000)?></b>min<?=
                                ($mob->time_from ?
                                    '-<b>' . (ceil($mob->time_to / 60000) + ceil($mob->time_from / 60000)) . '</b>min' :
                                    '')
                                ?></td>
                            <?php if($isResp){ ?>
                                <td align="center">
                                    <?php if($mob->x){ ?>
                                        <button onclick="$('.mob_spawn_<?=$mob->id?>').toggleClass('hide')" class="mob_spawn_<?=$mob->id?>">Show</button>
                                        <button onclick="$('.mob_spawn_<?=$mob->id?>').toggleClass('hide')" class="mob_spawn_<?=$mob->id?> hide">Hide</button>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>

                    <?php } ?>

                </table>

            <?php }else{ ?>

                No monster on this map.

            <?php } ?>

            <?php }else{ ?>

                No area found

            <?php } ?>
        </td></tr></table>