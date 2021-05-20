<?php
if (!defined('FLUX_ROOT')) exit;
$title = 'MVP Ranking';
$mvpdata = (int)$params->get('mvpdata');
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

// Get all group_id based on killer_char_id
$sql = "SELECT DISTINCT(`kill_char_id`) FROM {$server->logsDatabase}.`mvplog`";
$sql_params = array();
if ($mvpdata) {
    $sql .= " WHERE `monster_id`=?";
    $sql_params[] = $mvpdata;
}
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute($sql_params);
$killer_char_ids = $sth->fetchAll(PDO::FETCH_COLUMN, 0);

$char_ids_filter = [];
if(count($killer_char_ids)) {
    // Get group id of the killer and filter -_-
    $groups = AccountLevel::getGroupID((int)Flux::config('RankingHideGroupLevel'), '<');
    $sql = "SELECT `char`.`char_id` FROM {$server->charMapDatabase}.`char`";
    $sql .= " LEFT JOIN {$server->loginDatabase}.`login` ON `char`.`account_id` = `login`.`account_id`";
    $sql .= " WHERE `char`.`char_id`IN(".implode(',',array_fill(0, count($killer_char_ids), '?')).") AND `login`.`group_id` NOT IN (".implode(',',array_fill(0, count($groups), '?')).")";
    $sql_params = array_merge($killer_char_ids, $groups);
    $sth = $server->connection->getStatement($sql);
    $sth->execute($sql_params);
    $char_ids_filter = $sth->fetchAll(PDO::FETCH_COLUMN, 0);
}

$bind = array();
$col = "id, name_english, name_aegis";
$sql = "SELECT $col FROM $tableName WHERE `mvp_exp` > 0 ORDER BY `name_english`";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$moblist = $sth->fetchAll();

$char_ids = array();
$monsters = array();

if($mvpdata){
    // Players with most kills
    $bind[] = $mvpdata;
    $col = "mlog.kill_char_id, mlog.monster_id, count(*) AS count ";
    $sql = "SELECT $col FROM {$server->logsDatabase}.`mvplog` AS mlog ";
    $sql.= "WHERE mlog.monster_id = ? ";
    if (count($char_ids_filter)) {
        $sql .= " AND `kill_char_id` NOT IN(".implode(',',array_fill(0, count($char_ids_filter), '?')).")";
    }
    $sql.= "GROUP BY mlog.kill_char_id ORDER BY count DESC LIMIT $limit";
    $sth = $server->connection->getStatementForLogs($sql);
    $bind = array_merge($bind, $char_ids_filter);
    $sth->execute($bind);
    $kills = $sth->fetchAll();
    foreach ($kills as $kill) {
        $char_ids[$kill->kill_char_id] = null;
        $monsters[$kill->monster_id] = null;
    }
} else {

    // Latest x Kills
    $col = "mlog.mvp_id, mlog.mvp_date, mlog.kill_char_id, mlog.monster_id, mlog.mvpexp, mlog.map ";
    $sql = "SELECT $col FROM {$server->logsDatabase}.`mvplog` AS mlog ";
    if (count($char_ids_filter)) {
        $sql .= " WHERE  `kill_char_id` NOT IN(".implode(',',array_fill(0, count($char_ids_filter), '?')).")";
    }
    $sql.= "ORDER BY mlog.mvp_date DESC LIMIT $limit";
    $sth = $server->connection->getStatementForLogs($sql);
    $sth->execute($char_ids_filter);
    $mvps = $sth->fetchAll();
    foreach ($mvps as $mvp) {
        $char_ids[$mvp->kill_char_id] = null;
        $monsters[$mvp->monster_id] = null;
    }
}

if (count($char_ids)) {
    $sql = "SELECT `char_id`,`name`,login.`group_id` FROM {$server->charMapDatabase}.`char` ";
    $sql .= "LEFT JOIN {$server->loginDatabase}.`login` ON `char`.`account_id` = login.`account_id` ";
    $sql .= "WHERE `char_id` IN(".implode(',', array_fill(0, count($char_ids), '?')).")";
    $sth = $server->connection->getStatement($sql);
    $sth->execute(array_keys($char_ids));
    $temp = $sth->fetchAll();
    foreach ($temp as $char) {
        $char_ids[$char->char_id] = array('name' => $char->name, 'group_id' => $char->group_id);
    }
}

if (count($monsters)) {
    $sql = "SELECT `id`,`name_english` FROM $tableName WHERE `id` IN(".implode(',', array_fill(0, count($monsters), '?')).")";
    $sth = $server->connection->getStatement($sql);
    $sth->execute(array_keys($monsters));
    $temp = $sth->fetchAll();
    foreach ($temp as $mon) {
        $monsters[$mon->id] = $mon->name_english;
    }
}
$char_ids_filter = null;
$temp = null;
