<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Buyers';

// Get total count and feed back to the paginator.
$sth = $server->connection->getStatement("SELECT COUNT(id) AS total FROM buyingstores");
$sth->execute();
$paginator = $this->getPaginator($sth->fetch()->total);

// Set the sortable columns
$sortable = array(
 'id' => 'asc', 'map', 'char_name'
);
$paginator->setSortableColumns($sortable);

// Create the main request.
$sql = "SELECT `buyingstores`.char_id,`char`.name as char_name, `buyingstores`.id, `buyingstores`.sex, `buyingstores`.map, `buyingstores`.x, `buyingstores`.y, `buyingstores`.title, autotrade ";
$sql .= "FROM buyingstores ";
$sql .= "LEFT JOIN `char` on buyingstores.char_id = `char`.char_id ";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute();

$stores = $sth->fetchAll();
