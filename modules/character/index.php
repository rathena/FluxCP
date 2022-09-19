<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'List Characters';

$bind        = array();
$sqlpartial  = "LEFT OUTER JOIN {$server->charMapDatabase}.guild_member ON guild_member.char_id = ch.char_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.guild ON guild.guild_id = guild_member.guild_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS partner ON partner.char_id = ch.partner_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS mother ON mother.char_id = ch.mother ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS father ON father.char_id = ch.father ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS child ON child.char_id = ch.child ";

$sqlwhere    = "WHERE 1=1 ";
$sqlcount    = '';

$charID = $params->get('char_id');
if ($charID) {
	$sqlwhere   .= "AND ch.char_id = ? ";
	$bind[]      = $charID;
}
else {
	$opMapping   = array('eq' => '=', 'gt' => '>', 'lt' => '<');
	$opValues    = array_keys($opMapping);
	$account     = $params->get('account');
	$charName    = $params->get('char_name');
	$charClass   = $params->get('char_class');
	$baseLevelOp = $params->get('base_level_op');
	$baseLevel   = $params->get('base_level');
	$jobLevelOp  = $params->get('job_level_op');
	$jobLevel    = $params->get('job_level');
	$zenyOp      = $params->get('zeny_op');
	$zeny        = $params->get('zeny');
	$guild       = $params->get('guild');
	$partner     = $params->get('partner');
	$mother      = $params->get('mother');
	$father      = $params->get('father');
	$child       = $params->get('child');
	$online      = $params->get('online');
	$slotOp      = $params->get('slot_op');
	$slot        = $params->get('slot');
	
	if ($account) {
		$sqlcount .= "LEFT OUTER JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";
		if (preg_match('/^\d+$/', $account)) {
			$sqlwhere   .= "AND login.account_id = ? ";
			$bind[]      = $account;
		}
		else {
			$sqlwhere   .= "AND (login.userid LIKE ? OR login.userid = ?) ";
			$bind[]      = "%$account%";
			$bind[]      = $account;
		}
	}
	
	if ($charName) {
		$sqlwhere   .= "AND (ch.name LIKE ? OR ch.name = ?) ";
		$bind[]      = "%$charName%";
		$bind[]      = $charName;
	}
	
	if ($charClass) {
		$className = preg_quote($charClass, '/');
		$classIDs  = preg_grep("/.*?$className.*?/i", Flux::config('JobClasses')->toArray());
		
		if (count($classIDs)) {
			$classIDs    = array_keys($classIDs);
			$sqlwhere   .= "AND (";
			$partial     = '';
			
			foreach ($classIDs as $id) {
				$partial .= "ch.class = ? OR ";
				$bind[]   = $id;
			}
			
			$partial     = preg_replace('/\s*OR\s*$/', '', $partial);
			$sqlwhere   .= "$partial) ";
		}
		else {
			$sqlwhere .= 'AND ch.class IS NULL ';
		}
	}
	
	if (in_array($baseLevelOp, $opValues) && trim($baseLevel) != '') {
		$op          = $opMapping[$baseLevelOp];
		$sqlwhere   .= "AND ch.base_level $op ? ";
		$bind[]      = $baseLevel;
	}
	
	if (in_array($jobLevelOp, $opValues) && trim($jobLevel) != '') {
		$op          = $opMapping[$jobLevelOp];
		$sqlwhere   .= "AND ch.job_level $op ? ";
		$bind[]      = $jobLevel;
	}
	
	if (in_array($zenyOp, $opValues) && trim($zeny) != '') {
		$op          = $opMapping[$zenyOp];
		$sqlwhere   .= "AND ch.zeny $op ? ";
		$bind[]      = $zeny;
	}
	
	if ($guild) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.guild_member ON guild_member.char_id = ch.char_id ";
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.guild ON guild.guild_id = guild_member.guild_id ";
		$sqlwhere   .= "AND (guild.name LIKE ? OR guild.name = ?) ";
		$bind[]      = "%$guild%";
		$bind[]      = $guild;
	}
	
	if ($partner) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS partner ON partner.char_id = ch.partner_id ";
		$sqlwhere   .= "AND (partner.name LIKE ? OR partner.name = ?) ";
		$bind[]      = "%$partner%";
		$bind[]      = $partner;
	}
	
	if ($mother) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS mother ON mother.char_id = ch.mother ";
		$sqlwhere   .= "AND (mother.name LIKE ? OR mother.name = ?) ";
		$bind[]      = "%$mother%";
		$bind[]      = $mother;
	}
	
	if ($father) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS father ON father.char_id = ch.father ";
		$sqlwhere   .= "AND (father.name LIKE ? OR father.name = ?) ";
		$bind[]      = "%$father%";
		$bind[]      = $father;
	}
	
	if ($child) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS child ON child.char_id = ch.child ";
		$sqlwhere   .= "AND (child.name LIKE ? OR child.name = ?) ";
		$bind[]      = "%$child%";
		$bind[]      = $child;
	}
	
	if ($online == 'on' || $online == 'off') {
		if ($online == 'on') {
			$sqlwhere .= "AND ch.online > 0 ";
		}
		else {
			$sqlwhere .= "AND ch.online < 1 ";
		}
	}
	
	if (in_array($slotOp, $opValues) && trim($slot) != '') {
		$op          = $opMapping[$slotOp];
		$sqlwhere   .= "AND ch.char_num $op ? ";
		$bind[]      = $slot - 1;
	}
}

$sql  = "SELECT COUNT(ch.char_id) AS total FROM {$server->charMapDatabase}.`char` AS ch $sqlcount $sqlwhere";
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);
$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'ch.char_id' => 'asc', 'userid', 'char_name', 'ch.base_level', 'ch.job_level',
	'ch.zeny', 'guild_name', 'partner_name', 'mother_name', 'father_name', 'child_name',
	'ch.online', 'ch.char_num'
));

$col  = "ch.account_id, ch.char_id, ch.name AS char_name, ch.char_num, ";
$col .= "ch.online, ch.base_level, ch.job_level, ch.class, ch.zeny, ";
$col .= "guild.guild_id, guild.name AS guild_name, guild.emblem_id as emblem, ";
$col .= "login.userid, partner.name AS partner_name, partner.char_id AS partner_id, ";
$col .= "mother.name AS mother_name, mother.char_id AS mother_id, ";
$col .= "father.name AS father_name, father.char_id AS father_id, ";
$col .= "child.name AS child_name, child.char_id AS child_id";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.`char` AS ch $sqlpartial $sqlwhere";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);

$characters = $sth->fetchAll();
$authorized = $auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter;

if ($characters && count($characters) === 1 && $authorized && Flux::config('SingleMatchRedirect')) {
	$this->redirect($this->url('character', 'view', array('id' => $characters[0]->char_id)));
}
?>
