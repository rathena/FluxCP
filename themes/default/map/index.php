<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>
<script>
    $(document).ready(function(){
        $('#filtering').keyup(function(){
            var val = $(this).val();
            $('.maps').each(function(){
                if(this.id.indexOf(val) == -1){
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
            if($('.maps:visible').length == 0){
                $('#notFound').show();
            } else {
                $('#notFound').hide();
            }
        })
    })
</script>
<h2>Map Database</h2>
<?php if($maps !== false){ ?>
<h3>Quick Map Links</h3>
Filter <input type="text" id="filtering">
    <div style="margin:10px;border:1px solid lightgray;padding:20px;height:500px;overflow: auto">
        <table>
            <tr id="notFound" style="<?=sizeof($maps)?'display:none;':''?>"><td>
    No area found
</td></tr>
        <?php foreach($maps as $map){ ?>
            <tr class="maps" id="<?=$map->name?>">
                <td><img height="30" width="30" src="<?=$this->mapImage($map->name, true)?>"></td>
                <td style="padding-left:20px;"><a href="<?=$this->url('map', 'view')?>&map=<?=$map->name?>"><?=$map->name?></td>
            </tr>
        <?php } ?>
        </div>
    </table>
<?php } else { ?>

    database with maps not found

<?php } ?>