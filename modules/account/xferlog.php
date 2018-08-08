<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('XferLogTitle');

$xferTable = Flux::config('FluxTables.CreditTransferTable');

$col  = "from_account_id, target_account_id, amount, transfer_date, ";
$col .= "fa.userid AS from_userid, ta.userid AS target_userid, ";
$col .= "ch.char_id AS target_char_id, ch.name AS target_char_name, ";
$col .= "fa.email AS from_email";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.$xferTable ";
$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.login AS fa ON $xferTable.from_account_id = fa.account_id ";
$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.login AS ta ON $xferTable.target_account_id = ta.account_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS ch ON $xferTable.target_char_id = ch.char_id ";
$sql .= "WHERE target_account_id = ? ORDER BY transfer_date DESC";
$sth  = $server->connection->getStatement($sql);

$sth->execute(array($session->account->account_id));
$incomingXfers = $sth->fetchAll();

$sql  = "SELECT $col FROM {$server->charMapDatabase}.$xferTable ";
$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.login AS fa ON $xferTable.from_account_id = fa.account_id ";
$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.login AS ta ON $xferTable.target_account_id = ta.account_id ";
$sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS ch ON $xferTable.target_char_id = ch.char_id ";
$sql .= "WHERE from_account_id = ? ORDER BY transfer_date DESC";
$sth  = $server->connection->getStatement($sql);

$sth->execute(array($session->account->account_id));
$outgoingXfers = $sth->fetchAll();
?>
