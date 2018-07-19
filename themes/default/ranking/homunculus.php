<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Homunculus Ranking</h2>
<h3>
	Top <?php echo number_format($limit=(int)Flux::config('HomunRankingLimit')) ?> Homunculi
	<?php if (!is_null($homunClass)): ?>
	(<?php echo htmlspecialchars($className=$this->homunClassText($homunClass)) ?>)
	<?php endif ?>
	on <?php echo htmlspecialchars($server->serverName) ?>
</h3>
<?php if ($homuns): ?>
<form action="" method="get" class="search-form2">
	<?php echo $this->moduleActionFormInputs('ranking', 'homunculus') ?>
	<p>
		<label for="homunclass">Filter by class:</label>
		<select name="homunclass" id="homunclass">
			<option value=""<?php if (is_null($homunClass)) echo 'selected="selected"' ?>>All</option>
		<?php foreach ($classes as $homunClassIndex => $homunClassName): ?>
			<option value="<?php echo $homunClassIndex ?>"
				<?php if (!is_null($homunClass) && $homunClass == $homunClassIndex) echo ' selected="selected"' ?>>
				<?php echo htmlspecialchars($homunClassName) ?>
			</option>
		<?php endforeach ?>
		</select>
		
		<input type="submit" value="Filter" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<table class="horizontal-table">
	<tr>
		<th>Rank</th>
		<th>Homunculus</th>
		<th>Owner Name</th>
		<th>Intimacy</th>
		<th>Level</th>
		<th>Experience</th>
	</tr>
	<?php $topRankType = !is_null($homunClass) ? $className : 'homunculus' ?>
	<?php for ($i = 0; $i < $limit; ++$i): ?>
	<tr<?php if (!isset($homuns[$i])) echo ' class="empty-row"'; if ($i === 0) echo ' class="top-ranked" title="<strong>'.htmlspecialchars($homuns[$i]->homun_name).'</strong> is the top ranked '.$topRankType.'!"' ?>>
		<td align="right"><?php echo number_format($i + 1) ?></td>
		<?php if (isset($homuns[$i])): ?>
		<td><strong><?php echo htmlspecialchars($homuns[$i]->homun_name) ?></strong></td>
		<td><strong>
			<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
				<?php echo $this->linkToCharacter($homuns[$i]->owner, $homuns[$i]->owner_name) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($homuns[$i]->owner_name) ?>
			<?php endif ?>
		</strong></td>
		<td><?php echo number_format($homuns[$i]->intimacy) ?></td>
		<td><?php echo number_format($homuns[$i]->level) ?></td>
		<td><?php echo number_format($homuns[$i]->exp) ?></td>
		<?php else: ?>
		<td colspan="8"></td>
		<?php endif ?>
	</tr>
	<?php endfor ?>
</table>
<?php else: ?>
<p>There are no homunculi. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
