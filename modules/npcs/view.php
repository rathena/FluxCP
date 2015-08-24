<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Viewing NPCs';
$npcID = $params->get('id');


try {
    $sql = 'select * from npcs where id = ?';
    $sth = $server->connection->getStatement($sql);
    $sth->execute(array($npcID));
    if((int)$sth->stmt->errorCode()){
        throw new Flux_Error('db not found');
    }
    $npc = $sth->fetch();
    $sql = 'select * from map_index where name = ?';
    $sth = $server->connection->getStatement($sql);
    $sth->execute(array($npc->map));
    if((int)$sth->stmt->errorCode()){
        throw new Flux_Error('db not found');
    }
    $map = $sth->fetch();
    if($npc->is_shop){
        $sql = 'select * from shops_sells where id_shop = ?';
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($npc->id));
        if((int)$sth->stmt->errorCode()){
            throw new Flux_Error('db not found');
        }
        $items = $sth->fetchAll();
    }
} catch(Exception $e){
    $npc = false;
}