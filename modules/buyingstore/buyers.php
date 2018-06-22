<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Buyers';

$buyer = trim($params->get('buyer'));
$shopname = trim($params->get('title'));
$map = trim($params->get('map'));

$sql_criteria = "";
$sql_params = array();
if ($buyer) {
    $sql_criteria .= '`name` LIKE ? ';
    $sql_params[] = "%$buyer%";
}
if ($map) {
    if (count($sql_params))
        $sql_criteria .= ' AND ';
    $sql_criteria .= '`map` LIKE ? ';
    $sql_params[] = "%$map%";
}
if ($shopname) {
    if (count($sql_params))
        $sql_criteria .= ' AND ';
    $sql_criteria .= '`title` LIKE ? ';
    $sql_params[] = "%$shopname%";
}

$buyingstore_table = 'buyingstores';
$sql = "SELECT `$buyingstore_table`.id as buyid, `$buyingstore_table`.sex, `$buyingstore_table`.map, `$buyingstore_table`.x, `$buyingstore_table`.y, `$buyingstore_table`.title, autotrade ";
$sql .= ",`$buyingstore_table`.char_id,`char`.name ";
$sql .= "FROM `$buyingstore_table` ";
$sql .= "LEFT JOIN `char` on `$buyingstore_table`.`char_id` = `char`.char_id ";
if (count($sql_params)) {
    $sql .= 'WHERE '.$sql_criteria;
}

$sortable = array(
    'buyid' => 'ASC', 'name', 'map', 'title'
);

$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);
$paginator = $this->getPaginator($sth->rowCount());
$paginator->setSortableColumns($sortable);

$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($sql_params);

$stores = $sth->fetchAll();
