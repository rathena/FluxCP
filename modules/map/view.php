<?php
if (!defined('FLUX_ROOT')) exit;
error_reporting(0);

$title = 'Map Database';

try {
    $sth = $server->connection->getStatement('select * from `map_index` where name = ?');
    $sth->execute(array($params->get('map')));
    if((int)$sth->stmt->errorCode()){
        throw new Flux_Error('db not found');
    }
    $map = $sth->fetchAll();
    $map = $map[0];
} catch(Exception $e){
    $map = false;
}
if($map){
    try {
        $sql = 'select * from `mob_spawns` where map = ?';
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($map->name));
        if((int)$sth->stmt->errorCode()){
            throw new Flux_Error('db not found');
        }
        $mobs = $sth->fetchAll();
    } catch(Exception $e){
        $mobs = array();
    }
}

function conv($point, $size){
    return 512 / ($size / $point);
}