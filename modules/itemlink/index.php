<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/TemporaryTable.php';

// change this to your packetver
// values stolen from rathena, so if it doesnt work you might need to change it
$packetver = 20200724;

if ($packetver >= 20200724) {
    $cardsep = ')';
    $optsep = '+';
    $optparamsep = ',';
    $optvalsep = '-';
} else if ($packetver >= 20161116) {
    $cardsep = '(';
    $optsep = '*';
    $optparamsep = '+';
    $optvalsep = ',';
}


$itemlink = $params->get('itemlink');

if ($params->get('base64')) {
    $itemlink = $this->base64Url_decode($itemlink);
}

$itemlink_len = strlen($itemlink);

// get substring 5 characters
$ret = $this->parseBase62Until($itemlink, 0, 5);
$equip = $ret[0];
$idx = $ret[1];

$isequip = substr($itemlink, 5, 1);

$idx = 6;

// nameid
$ret = $this->parseBase62Until($itemlink, $idx);
$nameid = $ret[0];
$idx = $ret[1];

if ($server->isRenewal) {
    $fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
    $fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
$itemDescTable = Flux::config('FluxTables.ItemDescTable');

$sql = "SELECT items.id AS item_id, name_english AS name, slots ";

if (Flux::config('ShowItemDesc')) {
    $sql .= ', itemdesc ';
}

$sql .= "FROM {$server->charMapDatabase}.items ";
if (Flux::config('ShowItemDesc')) {
    $sql .= "LEFT OUTER JOIN {$server->charMapDatabase}.$itemDescTable ON $itemDescTable.itemid = items.id ";
}
$sql .= "WHERE items.id = ? LIMIT 1";

$sth = $server->connection->getStatement($sql);
$sth->execute(array($nameid));

$item = $sth->fetch();

if (!$item) {
    return;
}

$title = "Viewing Item Link for ($item->name)";


// refines
if ($itemlink[$idx] == '%') {
    $idx++;
    $ret = $this->parseBase62Until($itemlink, $idx);
    $refine = $ret[0];
    $idx = $ret[1];
} else {
    $refine = 0;
}

// view
if ($itemlink[$idx] == '&') {
    $idx++;
    $ret = $this->parseBase62Until($itemlink, $idx);
    $view = $ret[0];
    $idx = $ret[1];
} else {
    $view = 0;
}

// enchantgrade
if ($itemlink[$idx] == '\'') {
    $idx++;
    $ret = $this->parseBase62Until($itemlink, $idx);
    $enchantgrade = $ret[0];
    $idx = $ret[1];
} else {
    $enchantgrade = 0;
}

// cards
$cards = [];
while ($idx < $itemlink_len && $itemlink[$idx] == $cardsep) {
    $idx++;
    $ret = $this->parseBase62Until($itemlink, $idx);

    $sth = $server->connection->getStatement($sql);
    $sth->execute(array($ret[0]));
    $card = $sth->fetch();
    $cards[] = $card;

    $idx = $ret[1];
}

// options
$options = [];
while ($idx < $itemlink_len && $itemlink[$idx] == $optsep) {
    $option = [];
    $idx++;
    $ret = $this->parseBase62Until($itemlink, $idx);
    $option['opt'] = $ret[0];
    $idx = $ret[1];
    if ($itemlink[$idx] != $optparamsep) {
        break;
    }
    $idx++;
    $ret = $this->parseBase62Until($itemlink, $idx);
    $option['param'] = $ret[0];
    $idx = $ret[1];
    if ($itemlink[$idx] != $optvalsep) {
        break;
    }
    $idx++;
    $ret = $this->parseBase62Until($itemlink, $idx);
    $option['val'] = $ret[0];
    $idx = $ret[1];
    array_push($options, $option);
}
?>
