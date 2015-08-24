<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>

<style>
    .points{position:absolute;border:3px double #ff00ff;}
    .points_npcs{z-index:5999;position:absolute;width:10px;height:10px;background-color: green;border:3px solid #ffff00}
    .hide{display:none;}
    .you_here{z-index:9999;position:absolute;width:10px;height:10px;background-color: green;border:2px solid yellow;border-radius:20px;}
    .warps:hover{border-color:black;cursor:pointer;background-color: #ff443f  }
    .warps{position:absolute;width:20px;height:20px;background-color: red;border:2px solid yellow;border-radius:20px;}
    .tab{
        padding:10px;
        border:1px solid #E1EAF3;
        display: inline-block;
        border-radius: 10px;
        cursor:pointer;
    }
    .tabs_un{
        height:500px;
    }
    .tab.active{
        background-color: #8EBCEB;
    }
</style>
<script>
    $(document).ready(function(){
        $('.hide_shop').on('click', function(){
            id = $(this).attr('data');
            $('.shop_' + id).toggleClass('hide');
        });
        $('.show_shop').on('click', function(){
            id = $(this).attr('data');
            $('#load_shop_' + id).show();
            $.ajax({
                type: 'GET',
                dataType: 'JSON',
                url: '/?module=map&action=view&npc_id=' + id,
                success: function(data){
                    $('.shop_' + id).toggleClass('hide');
                    $('#load_shop_' + id).hide();
                    if(!data.length){
                        $('#shop_' + id).text('Not found items in this shop.');
                    } else{
                        var table = '<table class="vertical-table"><tr><th>Image</th><th>Name</th><th>Price</th></tr>';
                        for(var i in data){
                            var item = data[i];
                            table += '<tr>';
                            if(item.img) {
                                table += '<td><img src="' + item.img + '" /></td>';
                            } else {
                                table += '<td></td>';
                            }
                            if(item.link){
                                table += '<td><a href="' + item.link + '">' + item.name + '</a></td>';
                            } else {
                                table += '<td>' + item.name + '</td>';
                            }
                            table += '<td>' + item.price + '</td>';
                        }
                        $('#shop_' + id).html(table);
                    }
                    console.log(data);
                },
                error: function(){
                    $('#load_shop_' + id).hide();
                    alert('Error with loading items');
                }
            })
        });
        $('.npcs_hover').hover(function(){
            $('.' + $(this).attr('data')).show();
        }, function(){
            $('.' + $(this).attr('data')).hide();
        });
        $('.tab').on('click', function(){
            $('.tab').each(function(){
                $('#' + $(this).attr('data')).hide();
                $(this).removeClass('active');
            })
            $('#' + $(this).attr('data')).show();
            $(this).addClass('active');
        });
    })
</script>
<h2>Map Database</h2>
<a style="display:block;" href="<?=$this->url('map')?>">Back to Map Database</a>

<?php if($map){ ?>

<table style="display:inline-block" class="vertical-table" style="margin-top:10px;">
    <tr>
        <td>
            <h3>Map "<b><?=$map->name?></b>"</h3>
            <div style="display:inline-block;position:relative;width:512px;height:512px">
                <img src="<?=$this->mapImage($map->name)?>" style="width:100%;height:100%;">

                <?php if((int)$params->get('x') && (int)$params->get('y')){ ?>
                    <div class="you_here" style="
                        left:<?=$this->conv((int)$params->get('x'), $map->x, $map) - 5?>px;
                        bottom:<?=$this->conv((int)$params->get('y'), $map->y, $map) - 5?>px;
                        "></div>
                <?php } ?>

                <?php foreach($npcs as $npc){?>

                    <div class="npc_<?=$npc->x?>-<?=$npc->y?> points_npcs hide npc_resp" style="
                        left:<?=$this->conv($npc->x, $map->x, $map) - 5?>px;
                        bottom:<?=$this->conv($npc->y, $map->y, $map) - 5?>px;
                        "></div>

                <?php } ?>
                <?php foreach($shops as $shop){?>

                    <div class="npc_<?=$shop->x?>-<?=$shop->y?> points_npcs hide npc_resp" style="
                        left:<?=$this->conv($shop->x, $map->x, $map) - 5?>px;
                        bottom:<?=$this->conv($shop->y, $map->y, $map) - 5?>px;
                        "></div>

                <?php } ?>

                <?php $isResp = false; foreach($mobs as $mob){ if(!$mob->x){continue;} $isResp = true; ?>

                    <div class="mob_spawn_<?=$mob->id?> points hide mob_resp" style="
                        width:<?=$this->conv($mob->range_x, $map->x)?>px;
                        height:<?=$this->conv($mob->range_y, $map->y)?>px;
                        left:<?=$this->conv($mob->x, $map->x, $map) - $this->conv($mob->range_x, $map->x, $map) / 2?>px;
                        bottom:<?=$this->conv($mob->y, $map->y, $map) - $this->conv($mob->range_y, $map->y, $map) / 2?>px;
                        "></div>

                <?php } ?>


                <?php foreach($warps as $warp){?>

                    <a href="<?=$this->url('map', 'view', array('map' => $warp->to, 'x' => $warp->tx, 'y' => $warp->ty))?>">
                    <div class="warps" style="
                        left:<?=$this->conv($warp->x, $map->x, $map) - 10?>px;
                        bottom:<?=$this->conv($warp->y, $map->y, $map) - 10?>px;
                        "></div></a>

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
            <div>
                <div class="tab active" data="mobs_table">Mobs</div>
                <div class="tab" data="npcs_table">NPCs</div>
                <div class="tab" data="shops_table">Shops</div>
            </div>
            <div id="mobs_table" class="tabs_un">
                <?php if(sizeof($mobs)){ ?>
                    <table style="max-height: 500px;overflow: auto;display:inline-block" class="vertical-table" style="margin-top:10px;">
                        <tr>
                            <th>Mob Name</th>
                            <th>Spawn</th>
                            <th>Respawn time</th>
                            <?php if($isResp){ ?>
                                <th>Respawn Area</th>
                            <?php } ?>
                        </tr>
                        <tbody>
                        <?php foreach($mobs as $mob){ ?>

                            <tr>
                                <?php if($auth->actionAllowed('monster', 'view')){ ?>
                                    <td><a href="<?=$this->url('monster', 'view', array('id' => $mob->mob_id))?>"><?=$mob->name?></td>
                                <?php } else { ?>
                                    <td><?=$mob->name?></td>
                                <?php } ?>
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
            </div>

            <div id="npcs_table" class="tabs_un" style="display:none;">
                <?php if(sizeof($npcs)){ ?>
                    <table style="margin-top:10px; max-height: 500px;overflow: auto;display:inline-block" class="vertical-table">
                        <tr>
                            <th>NPC Name</th>
                            <th>Image</th>
                            <th>Coordinates</th>
                        </tr>
                        <tbody>
                        <?php foreach($npcs as $npc){?>
                            <tr class="npcs_hover" data="npc_<?=$npc->x?>-<?=$npc->y?>">
                                <?php if($auth->actionAllowed('npcs', 'view')){ ?>
                                    <td><a href="<?=$this->url('npcs', 'view', array('id' => $npc->id))?>"><?=$npc->name?></td>
                                <?php } else { ?>
                                    <td><?=$npc->name?></td>
                                <?php } ?>
                                <td><img src="<?=$this->npcImage($npc->sprite)?>" /></td>
                                <td><?=$npc->x . ',' . $npc->y?></td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>

                <?php }else{ ?>

                    No NPCs on this map.

                <?php } ?>
            </div>

            <div id="shops_table" class="tabs_un" style="display:none;">
                <?php if(sizeof($shops)){ ?>
                    <table style="margin-top:10px;max-height: 500px;overflow: auto;display:inline-block" class="vertical-table">
                        <tr>
                            <th>Shop Name</th>
                            <th>Image</th>
                            <th>Coordinates</th>
                        </tr>
                        <tbody>
                        <?php foreach($shops as $shop){?>
                            <tr class="npcs_hover" data="npc_<?=$shop->x?>-<?=$shop->y?>">
                                <?php if($auth->actionAllowed('npcs', 'view')){ ?>
                                    <td><a href="<?=$this->url('npcs', 'view', array('id' => $shop->id))?>"><?=$shop->name?></td>
                                <?php } else { ?>
                                    <td><?=$shop->name?></td>
                                <?php } ?>
                                <td><img src="<?=$this->npcImage($shop->sprite)?>" /></td>
                                <td><?=$shop->x . ',' . $shop->y?></td>
                            </tr>
                            <tr class="npcs_hover" data="npc_<?=$shop->x?>-<?=$shop->y?>">
                                <td colspan="3" align="center">
                                    <input type="button" data="<?=$shop->id?>" class="show_shop shop_<?=$shop->id?>" value="Show items" />
                                    <input type="button" data="<?=$shop->id?>" class="hide_shop shop_<?=$shop->id?> hide" value="Hide items" />
                                    <br>
                                    <img src="/themes/bootstrap/img/load.gif" id="load_shop_<?=$shop->id?>" class="shop_<?=$shop->id?> hide">
                                    <div class="shop_<?=$shop->id?> hide" id="shop_<?=$shop->id?>"></div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>

                <?php }else{ ?>

                    No Shops on this map.

                <?php } ?>
            </div>

            <?php }else{ ?>

                No area found

            <?php } ?>
        </td></tr></table>
