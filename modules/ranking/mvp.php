<?php
if (!defined('FLUX_ROOT')) exit;
$title = 'MVP Ranking';
$mvpdata = $params->get('mvpdata');
$limit = (int)Flux::config('MVPRankingLimit');
$minlevel = (int)Flux::config('MVPRankingMaxGroupID');

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

if($mvpdata){
    // Players with most kills
    $col = "mlog.kill_char_id, mlog.monster_id, char.name AS name, $tableName.iName AS iName, count(*) AS count ";
    $sql = "SELECT $col FROM {$server->logsDatabase}.`mvplog` AS mlog ";
    $sql.= "LEFT JOIN {$server->charMapDatabase}.`char` ON char.char_id = mlog.kill_char_id ";
    $sql.= "LEFT JOIN {$server->charMapDatabase}.`login` ON login.account_id = char.account_id ";
    $sql.= "LEFT JOIN $tableName ON id = mlog.monster_id ";
    $sql.= "WHERE mlog.monster_id = ? and login.group_id <= $minlevel GROUP BY mlog.kill_char_id ORDER BY count DESC LIMIT $limit";
    $sth = $server->connection->getStatementForLogs($sql);
    $sth->execute(array($mvpdata));
    $kills = $sth->fetchAll();
    
} else {
    
    // Latest x Kills
    $col = "mlog.mvp_id, mlog.mvp_date, mlog.kill_char_id, mlog.monster_id, mlog.mvpexp, mlog.map, char.name AS name, $tableName.iName AS iName ";
    $sql = "SELECT $col FROM {$server->logsDatabase}.`mvplog` AS mlog ";
    $sql.= "LEFT JOIN {$server->charMapDatabase}.`char` ON char.char_id = mlog.kill_char_id ";
    $sql.= "LEFT JOIN {$server->charMapDatabase}.`login` ON login.account_id = char.account_id ";
    $sql.= "LEFT JOIN $tableName ON id = mlog.monster_id where login.group_id <= $minlevel ORDER BY mlog.mvp_date DESC LIMIT $limit";
    $sth = $server->connection->getStatementForLogs($sql);
    $sth->execute();
    $mvps = $sth->fetchAll();
 }