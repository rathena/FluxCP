<?php if (!defined('FLUX_ROOT')) exit; ?>
    <h2>NPC List</h2>
<?php if ($npcs): ?>
    <?php echo $paginator->infoText() ?>
    <?php echo $paginator->getHTML() ?>
    <table class="horizontal-table">
        <tr>
            <th><?php echo $paginator->sortableColumn('name', 'NPC name') ?></th>
            <th>Image</th>
            <th><?php echo $paginator->sortableColumn('map', 'Map') ?></th>
            <th>Coordinates</th>
            <th><?php echo $paginator->sortableColumn('is_shop', 'Is Shop') ?></th>
        </tr>
        <?php foreach ($npcs as $npc): ?>
            <tr>
                <td align="right">
                    <?php if ($auth->actionAllowed('npcs', 'view')): ?>
                        <?php echo '<a href="' . $this->url('npcs', 'view', array('id' => $npc->id)) . '">' . htmlspecialchars($npc->name) . '</a>' ?>
                    <?php else: ?>
                        <?php echo htmlspecialchars($npc->name) ?>
                    <?php endif ?>
                </td>
                <?php if ($icon = $this->npcImage($npc->sprite)){ ?>
                <td title="<img src='<?php echo htmlspecialchars($icon) ?>?nocache=<?php echo rand() ?>' />">
                    <img height="40px" src="<?php echo htmlspecialchars($icon) ?>?nocache=<?php echo rand() ?>" />
                <?php } else {echo '<td>';} ?>
                </td>
                <td <?php
                $m = $this->mapImage($npc->map, 1);
                if($m){
                    echo 'title="<img src=\'' . $m . '\'>"';
                }
                ?>>

                    <?php if($auth->actionAllowed('map', 'view')){ ?>
                        <a href="<?=$this->url('map', 'view', array('map' => $npc->map))?>"><?php echo htmlspecialchars($npc->map) ?></a>
                    <?php } else { ?>
                        <?php echo htmlspecialchars($npc->map) ?>
                    <?php } ?>
                    </td>
                <td><?=number_format($npc->x) ?>,<?=number_format($npc->y) ?></td>
                <td><?=$npc->is_shop ? 'Shop' : ''?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <?php echo $paginator->getHTML() ?>
<?php else: ?>
    <p>No NPCs found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>