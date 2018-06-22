<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Vendors';

$vendor = trim($params->get('vendor'));
$shopname = trim($params->get('title'));
$map = trim($params->get('map'));

$sql_criteria = "";
$sql_params = array();
if ($vendor) {
    $sql_criteria .= '`name` LIKE ? ';
    $sql_params[] = "%$vendor%";
}
if ($shopname) {
    if (count($sql_params))
        $sql_criteria .= ' AND ';
    $sql_criteria .= '`title` LIKE ? ';
    $sql_params[] = "%$shopname%";
}
if ($map) {
    if (count($sql_params))
        $sql_criteria .= ' AND ';
    $sql_criteria .= '`map` LIKE ? ';
    $sql_params[] = "%$map%";
}

$vending_table = 'vendings';
$sql = "SELECT `ch`.char_id, `ch`.name, `$vending_table`.id as vending_id, `$vending_table`.account_id, `$vending_table`.sex, `$vending_table`.map, `$vending_table`.x, `$vending_table`.y, `$vending_table`.title, autotrade ";
$sql .= "FROM `$vending_table` ";
$sql .= "LEFT JOIN `char` ch ON `$vending_table`.char_id = `ch`.char_id ";

if (count($sql_params)) {
    $sql .= 'WHERE '.$sql_criteria;
}

$sortable = array(
    'vending_id' => 'ASC', 'map', 'name', 'title'
);

$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);
$paginator = $this->getPaginator($sth->rowCount());
$paginator->setSortableColumns($sortable);

$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);

$vendors = $sth->fetchAll();
