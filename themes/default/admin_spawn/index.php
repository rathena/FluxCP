<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>
<h2>Tables with monsters spawn and maps</h2>

<?php if($MobSpawnBase === false){ ?>
    Not found table `mob_spawn`
<?php } else { ?>
    <b id="monNum"><?=$MobSpawnBase?></b> Mobs in database
<?php } ?><br>
<?php if($npcsBase === false){ ?>
    Not found table `npcs`
<?php } else { ?>
    <b id="npcNum"><?=$npcsBase?></b> NPCs in database
<?php } ?><br>
<?php if($shopsBase === false){ ?>
    Not found table `shops`
<?php } else { ?>
    <b id="shopNum"><?=$shopsBase?></b> Shops in database
<?php } ?><br>
<?php if($warpsBase === false){ ?>
    Not found table `warps`
<?php } else { ?>
    <b id="warpNum"><?=$warpsBase?></b> Warps in database
<?php } ?><br>
<?php if($mapIndexBase === false){ ?>
    Not found table `map_index`
<?php } else { ?>
    <b id="mapNum"><?=$mapIndexBase?></b> Maps in database
<?php } ?><br>

<h3>Mysql Tables</h3>

<script>
    $(document).ready(function(){
        $('.complete').submit(function(){
            return confirm("Are you sure?");
        });
    });
</script>

<form class="complete" method="POST">
    <input type="hidden" name="act" value="create" />
    <input class="btn" type="submit" value="Create" />
</form>
<form class="complete" method="POST">
    <input type="hidden" name="act" value="truncate" />
    <input class="btn" type="submit" value="Clean" />
</form>
<form class="complete" method="POST">
    <input type="hidden" name="act" value="delete" />
    <input class="btn" type="submit" value="Delete" />
</form>

<hr />

<h3>Upload data</h3>

<div style="text-align: center; padding:20px;"><b>Max size file upload: <?=ini_get('upload_max_filesize')?></b></div>
<form class="forms" method="post" enctype="multipart/form-data">
    Upload ZIP archive with monter tables<br>
    <b>Example:</b> <br>Add to archive folder <b>*Athena/npc</b> and upload it<br>
    <input type="file" name="npc_zip"><br>
    <input class="btn" type="submit">
</form>

<form class="forms" method="post" enctype="multipart/form-data">
    Upload file  <b>*Athena/db/(re|pre-re)map_cache.dat</b><br>
    <input type="file" name="map_index"><br>
    <input class="btn" type="submit">
</form>

<?php if(sizeof($file)){ ?>
    <script>
        $(document).ready(function() {
            var FILES = ['<?=join('\',\'', $file)?>'];
            var COUNT = 0;
            var errors = [];
            $('.loading').show();
            var block = document.getElementById('main_table');
            startInsert();
            function startInsert() {
                if (errors[COUNT] >= 3) {
                    COUNT++;
                }
                if (typeof errors[COUNT] === 'undefined') {
                    errors[COUNT] = 0;
                }
                if (COUNT >= FILES.length) {
                    $('.loading').hide();
                    $.get('?module=admin_spawn&action=get&type=delDir');
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: '?module=admin_spawn&action=get',
                    dataType: 'json',
                    data: {
                        file_name: FILES[COUNT]
                    },
                    success: function (data) {
                        if(data.isError){
                            $('.table').append('<tr><td class="reds">' +
                                (COUNT + 1) + '/' + FILES.length + ' ' +
                                'File <b>' + FILES[COUNT] + '</b> unsuccessfly load. </td>' +
                                '<td colspan=4>' + data.error + '</td></tr>');
                        } else {
                            $('.table').append('<tr><td>' +
                                (COUNT + 1) + '/' + FILES.length + ' ' +
                                'File <b>' + data.file_short + '</b> successfly load. </td>' +
                                '<td>Mobs: <b>' + data.data.mobs + '</b>, </td>' +
                                '<td>Warps: <b>' + data.data.warps + '</b>, </td>' +
                            '<td>NPCs: <b>' + data.data.npcs + '</b></td>' +
                            '<td>Shops: <b>' + data.data.shops + '</b></td>' +
                            '</tr>');
                            $('#monNum').text(parseInt($('#monNum').text()) + data.data.mobs);
                            $('#warpNum').text(parseInt($('#warpNum').text()) + data.data.warps);
                            $('#npcNum').text(parseInt($('#npcNum').text()) + data.data.npcs);
                            $('#shopNum').text(parseInt($('#shopNum').text()) + data.data.shops);
                        }
                        COUNT++;
                        block.scrollTop = 9999;
                        startInsert();
                    },
                    error: function () {
                        errors[COUNT]++;
                        $('.table').append('<tr><td class="reds">' +
                            (COUNT + 1) + '/' + FILES.length + ' ' +
                            'File <b>' + FILES[COUNT] + '</b> unsuccessfly load </td>' +
                            '<td colspan=4>(attempt ' + errors[COUNT] + ' of 3)</td></tr>');
                        block.scrollTop = block.scrollHeight;
                        startInsert();
                    }
                })
            }
        });
    </script>
<?php } ?>

<div class="loading"><img src="/themes/bootstrap/img/load.gif"> Load . . .</div>
<div id="main_table">
<table class="table">
</table>
</div>
<div class="loading"><img src="/themes/bootstrap/img/load.gif"> Load . . .</div>


<style>
    #main_table{max-height: 200px;  overflow: auto;}
    .forms{display:inline-block;width:400px;}
    .btn{width:200px;height:30px;margin:10px;}
    .loading{padding: 20px;text-align: center;display:none;}
    .table{width: 100%;border-spacing:0}
    .table td{background-color: #93ff87}
    .table td.reds{background-color: #ff9b97}
</style>