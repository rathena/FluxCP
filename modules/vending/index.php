<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Vendors';

// Get total count and feed back to the paginator.
$sth = $server->connection->getStatement("SELECT COUNT(id) AS total FROM vendings");
$sth->execute();
$paginator = $this->getPaginator($sth->fetch()->total);

// Set the sortable columns
$sortable = array(
    'id' => 'asc', 'map', 'char_name'
    
);
$paginator->setSortableColumns($sortable);

// Create the main request.
$sql    = "SELECT `char`.name as char_name, `vendings`.id, `vendings`.sex, `vendings`.map, `vendings`.x, `vendings`.y, `vendings`.title, autotrade ";
$sql    .= "FROM vendings ";
$sql    .= "LEFT JOIN `char` on vendings.char_id = `char`.char_id ";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute();

$vendings = $sth->fetchAll();
?>
