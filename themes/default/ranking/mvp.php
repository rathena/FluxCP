<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>MVP Ranking</h2>
<h3>Search</h3>
<form action="" method="get" class="search-form2">
	<?php echo $this->moduleActionFormInputs('ranking', 'mvp') ?>
	<p>
		<label for="mvpdata">Filter by monster:</label>
		<select name="mvpdata" id="mvpdata">
		<?php foreach ($moblist as $mob): ?>
			<option value="<?php echo $mob->id ?>" <?php if ($mvpdata && $mob->id == $mvpdata) echo "selected" ?>>
				<?php echo htmlspecialchars($mob->name_english) ?> (<?php echo htmlspecialchars($mob->name_aegis) ?>)
			</option>
		<?php endforeach ?>
		</select>
		
		<input type="submit" value="Filter" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>




<?php if ($mvpdata): ?>
    <?php if($kills):?>
    <h3>Latest <?php echo Flux::config('MVPRankingLimit') ?> Kills</h3>
    <table class="horizontal-table">
    	<tr>
    		<th><?php echo Flux::message('MVPLogCharacterLabel') ?></th>
    		<th><?php echo Flux::message('MVPLogMonsterLabel') ?></th>
    		<th>Kills</th>
    	</tr>
    	<?php foreach ($kills as $kill): ?>
    	<tr>
    		<td align="center">
    			<?php if ($kill->kill_char_id): ?>
    				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
    					<?php echo $this->linkToCharacter($kill->kill_char_id, $char_ids[$kill->kill_char_id] ? htmlspecialchars($char_ids[$kill->kill_char_id]['name']) : "Char Deleted") ?>
    				<?php else: ?>
    					<?php echo $char_ids[$kill->kill_char_id] ? htmlspecialchars($char_ids[$kill->kill_char_id]['name']) : "Char Deleted" ?>
    				<?php endif ?>
    			<?php else: ?>
    				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
    			<?php endif ?>
    		</td>
    		<td align="center">
    		<?php if ($auth->actionAllowed('monster', 'view')): ?>
    				<?php echo $this->linkToMonster($kill->monster_id, $monsters[$kill->monster_id] ? htmlspecialchars($monsters[$kill->monster_id]) : htmlspecialchars(Flux::message('UnknownLabel'))) ?>
    			<?php else: ?>
    				<?php echo $monsters[$kill->monster_id] ? htmlspecialchars($monsters[$kill->monster_id]) : htmlspecialchars(Flux::message('UnknownLabel')) ?>
    			<?php endif ?>
    		</td>
    		<td align="center"><?php echo htmlspecialchars(number_format($kill->count)) ?></td>
        </tr>
        <?php endforeach ?>
    </table>
    <?php else: ?>
    <p>
    	<?php echo htmlspecialchars(Flux::message('MVPLogNotFound')) ?>
    	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
    </p>
    <?php endif ?>

<?php else: ?>
    <?php if($mvps):?>
    <h3>Latest <?php echo Flux::config('MVPRankingLimit') ?> Kills</h3>
    <table class="horizontal-table">
    	<tr>
    		<th><?php echo Flux::message('MVPLogDateLabel') ?></th>
    		<th><?php echo Flux::message('MVPLogCharacterLabel') ?></th>
    		<th><?php echo Flux::message('MVPLogMonsterLabel') ?></th>
    		<th><?php echo Flux::message('MVPLogExpLabel') ?></th>
    		<th><?php echo Flux::message('MVPLogMapLabel') ?></th
    	</tr>
    	<?php foreach ($mvps as $mvp): ?>
    	<tr>
    		<td align="center"><?php echo $this->formatDateTime($mvp->mvp_date) ?></td>
    		<td align="center">
    			<?php if ($mvp->kill_char_id): ?>
    				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
    					<?php echo $this->linkToCharacter($mvp->kill_char_id, $char_ids[$mvp->kill_char_id] ? htmlspecialchars($char_ids[$mvp->kill_char_id]['name']) : "Char Deleted") ?>
    				<?php else: ?>
    					<?php echo $char_ids[$mvp->kill_char_id] ? htmlspecialchars($char_ids[$mvp->kill_char_id]['name']) : "Char Deleted" ?>
    				<?php endif ?>
    			<?php else: ?>
    				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
    			<?php endif ?>
    		</td>
    		<td align="center">
    		<?php if ($auth->actionAllowed('monster', 'view')): ?>
    				<?php echo $this->linkToMonster($mvp->monster_id, $monsters[$mvp->monster_id] ? htmlspecialchars($monsters[$mvp->monster_id]) : htmlspecialchars(Flux::message('UnknownLabel'))) ?>
    			<?php else: ?>
    				<?php echo $monsters[$mvp->monster_id] ? htmlspecialchars($monsters[$mvp->monster_id]) : htmlspecialchars(Flux::message('UnknownLabel')) ?>
    			<?php endif ?>
    		</td>
    		<td align="center"><?php echo htmlspecialchars(number_format($mvp->mvpexp)) ?></td>
    		<td align="center">
    			<?php if (strlen(basename($mvp->map, '.gat')) > 0): ?>
    				<?php echo htmlspecialchars(basename($mvp->map, '.gat')) ?>
    			<?php else: ?>
    				<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
    			<?php endif ?>
    		</td>
            
        </tr>
        <?php endforeach ?>
    </table>
    <?php else: ?>
    <p>
    	<?php echo htmlspecialchars(Flux::message('MVPLogNotFound')) ?>
    	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
    </p>
    <?php endif ?>
<?php endif ?>
