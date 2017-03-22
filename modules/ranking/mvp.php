<?php
if (!defined('FLUX_ROOT')) exit;
$title = 'MVP Ranking';
$mvpdata = $params->get('mvpdata');
$limit = (int)Flux::config('MVPRankingLimit');

require_once 'Flux/TemporaryTable.php';

if (trim($mvpdata) === '') { $mvpdata = null; }

// List MVPS
$tableName  = "{$server->charMapDatabase}.monsters";
if($server->isRenewal) {
    $fromTables = array("{$server->charMapDatabase}.mob_db_re", "{$server->charMapDatabase}.mob_db2_re");
} else {
    $fromTables = array("{$server->charMapDatabase}.mob_db", "{$server->charMapDatabase}.mob_db2");
}
$tempTable  = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
// Statement parameters, joins and conditions.
$bind = array();
$col = "id, iName";
$sql = "SELECT $col FROM $tableName WHERE `MEXP` > 0 ORDER BY `iName`";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);
$moblist = $sth->fetchAll();

$groups = AccountLevel::getGroupID((int)Flux::config('RankingHideGroupLevel'), '<');
if(!empty($groups)) {
    $ids   = implode(', ', array_fill(0, count($groups), '?'));
    $minlevel  = "login.group_id IN ($ids) ";
    $bind  = array_merge($bind, $groups);
}

if($mvpdata){
    // Players with most kills    
    $bind[] = $mvpdata;
    $col = "mlog.kill_char_id, mlog.monster_id, char.name AS name, $tableName.iName AS iName, count(*) AS count ";
    $sql = "SELECT $col FROM {$server->logsDatabase}.`mvplog` AS mlog ";
    $sql.= "LEFT JOIN {$server->charMapDatabase}.`char` ON char.char_id = mlog.kill_char_id ";
    $sql.= "LEFT JOIN {$server->loginDatabase}.`login` ON login.account_id = char.account_id ";
    $sql.= "LEFT JOIN $tableName ON id = mlog.monster_id ";
    $sql.= "WHERE $minlevel and mlog.monster_id = ? GROUP BY mlog.kill_char_id ORDER BY count DESC LIMIT $limit";
    $sth = $server->connection->getStatementForLogs($sql);
    $sth->execute($bind);
    $kills = $sth->fetchAll();
} else {
    
    // Latest x Kills
    $col = "mlog.mvp_id, mlog.mvp_date, mlog.kill_char_id, mlog.monster_id, mlog.mvpexp, mlog.map, char.name AS name, $tableName.iName AS iName ";
    $sql = "SELECT $col FROM {$server->logsDatabase}.`mvplog` AS mlog ";
    $sql.= "LEFT JOIN {$server->charMapDatabase}.`char` ON char.char_id = mlog.kill_char_id ";
    $sql.= "LEFT JOIN {$server->loginDatabase}.`login` ON login.account_id = char.account_id ";
    $sql.= "LEFT JOIN $tableName ON id = mlog.monster_id where $minlevel ORDER BY mlog.mvp_date DESC LIMIT $limit";
    $sth = $server->connection->getStatementForLogs($sql);
    $sth->execute($bind);
    $mvps = $sth->fetchAll();
 }