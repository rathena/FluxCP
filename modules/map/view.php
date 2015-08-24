<?php
if (!defined('FLUX_ROOT')) exit;
error_reporting(0);

$title = 'Map Database';
if($params->get('npc_id')){
    $sth = $server->connection->getStatement('select * from `shops_sells` where id_shop = ?');
    $sth->execute(array($params->get('npc_id')));
    $items = $sth->fetchAll();
    $json = array();
    foreach($items as $item){
        $img = $this->iconImage($item->item);
        $json[] = array(
            'id' => $item->item,
            'link' => $auth->actionAllowed('item', 'view') ? $this->url('item', 'view', array('id' => $item->item)) : '',
            'img' => $img ? $img : '',
            'name' => $item->name,
            'price' => preg_replace('/(\d)(?=(\d\d\d)+([^\d]|$))/', '$1 ', $item->price)
        );
    }
    echo json_encode($json);
    die();
}

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
    $tables = array(
        '`mob_spawns` where map = ?' => 'mobs',
        '`warps` where map = ?' => 'warps',
        '`npcs` where map = ? and is_shop = 0' => 'npcs',
        '`npcs` where map = ? and is_shop = 1' => 'shops'
    );
    foreach($tables as $table => $var) {
        try {
            $sql = 'select * from ' . $table;
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($map->name));
            if ((int)$sth->stmt->errorCode()) {
                throw new Flux_Error('db not found');
            }
            $$var = $sth->fetchAll();
        } catch (Exception $e) {
            $$var = array();
        }
    }
}
