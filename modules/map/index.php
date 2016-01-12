<?php
if (!defined('FLUX_ROOT')) exit;
error_reporting(0);

$title = 'Map Database';
try {
    $sth = $server->connection->getStatement('select * from `map_index` order by name');
    $sth->execute();
    if((int)$sth->stmt->errorCode()){
        throw new Flux_Error('db not found');
    }
    $maps = $sth->fetchAll();
} catch(Exception $e){
    $maps = false;
}