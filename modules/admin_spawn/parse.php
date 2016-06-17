<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/TemporaryTable.php';

class parse{

    private $pref = null;
    private $server = null;
    private $items = array();

    function __construct($server){
        $this->pref = $server->isRenewal ? 're' : 'pre-re';
        $this->server = $server;
    }

    private function getItems(){

        if($this->server->isRenewal) {
            $fromTables = array("{$this->server->charMapDatabase}.item_db_re", "{$this->server->charMapDatabase}.item_db2_re");
        } else {
            $fromTables = array("{$this->server->charMapDatabase}.item_db", "{$this->server->charMapDatabase}.item_db2");
        }
        $tableName = "{$this->server->charMapDatabase}.items";
        $tempTable = new Flux_TemporaryTable($this->server->connection, $tableName, $fromTables);
        $sth = $this->server->connection->getStatement('select * from ' . $tableName);
        $allItems = array();
        $sth->execute();
        $items = $sth->fetchAll();
        foreach($items as $item){
            $allItems[$item->id] = array(
                'name' => $item->name_japanese,
                'price' => $item->price_buy
            );
        }
        $this->items = $allItems;
    }

    function getFiles($path = false){
        $files = array();
        if(!$path) {
            $path = FLUX_ROOT . '/upload_npc/npc/' . $this->pref . '/scripts_main.conf';
            if(!file_exists($path)){
                throw new Flux_Error('file scripts_main.conf not found');
            }
        }
        if(!file_exists($path)){
            return array();
        }
        $data = file_get_contents($path);
        preg_match_all('/(.*)(npc|import): (.*)/', $data, $match);
        foreach($match[3] as $key => $item){
            if(trim($match[1][$key]) == '//'){
                continue;
            }
            switch(trim($match[2][$key])){
                case 'npc':
                    $files[] = FLUX_ROOT . '/upload_npc/' . trim($item);
                    break;
                case 'import':
                    $files = array_merge($files, $this->getFiles(FLUX_ROOT . '/upload_npc/' . trim($item)));
                    break;
            }
        }
        return $files;
    }

    function loadFiles(array $files){
        $array = array(
            'mobs' => 0,
            'npcs' => 0,
            'warps' => 0,
            'shops' => 0,
        );
        foreach($files as $file){
            $npcs = $this->getNpc($file);
            if(is_array($npcs) && sizeof($npcs)){
                $array['npcs'] += $this->loadNpc($npcs);
            }
            $warps = $this->getWarps($file);
            if(is_array($warps) && sizeof($warps)){
                $array['warps'] += $this->loadWarps($warps);
            }
            $monsters = $this->getMonsters($file);
            if(is_array($monsters) && sizeof($monsters)){
                $array['mobs'] += $this->loadMonsters($monsters);
            }
            $shops = $this->getShops($file);
            if(is_array($shops) && sizeof($shops)){
                $array['shops'] += $this->loadShops($shops);
            }
        }
        return $array;
    }

    private function loadShops(array $data){
        if(sizeof($data)){
            $this->getItems();
        }
        $sql = 'insert into npcs (`map`, `x`, `y`, `name`, `sprite`, `id`, `is_shop`)values';
        $sql_item = 'insert into shops_sells (`item`, `price`, `name`, `id_shop`)values';
        $sth = $this->server->connection->getStatement('select max(id) id from `npcs`');
        $sth->execute();
        $id = $sth->fetch();
        $id = (int)$id->id + 1;

        $array = array();
        $sells_items = array();
        $import_array = array();
        $import_sql = array();

        foreach($data as $item){
            $import = explode(',', $item['npc']);
            $sells = explode(',', $item['item']);
            if(sizeof($import) != 5){
                continue;
            }
            foreach($sells as $sel_item){
                $array[] = '(?, ?, ?, ?)';
                $sel_item = explode(':', $sel_item);
                $sel_item[1] = $sel_item[1] == -1 ? $this->items[$sel_item[0]]['price'] : $sel_item[1];
                $sel_item[] = $this->items[$sel_item[0]]['name'];
                $sells_items = array_merge($sells_items, $sel_item);
                $sells_items[] = $id;
            }
            $import[] = $id ++;
            $import[] = 1;
            $import_array = array_merge($import_array, $import);
            $import_sql[] = '(?, ?, ?, ?, ?, ?, ?)';
        }
        if(sizeof($import_sql)) {
            $sth = $this->server->connection->getStatement($sql . join(',', $import_sql));
            $sth->execute($import_array);
            $sth = $this->server->connection->getStatement($sql_item . join(',', $array));
            $sth->execute($sells_items);
        }
        return sizeof($import_sql);
    }

    private function getShops($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\t(shop|duplicate\(([^\)]+)\))\t(.*?)\t([0-9]+),?(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            preg_match("/\tduplicate\(([^\)]+)\)\t/", $item, $match);
            $duplicate = $match[1];
            if($duplicate){
                preg_match("/\tshop\t" . preg_quote($duplicate) . "\t([0-9]+),?(.*)/", $text, $sell_items);
                if(!sizeof($sell_items)) {
                    unset($data[$key]);
                    continue;
                } else {
                    $sell_items = $sell_items[2];
                }
            } else {
                preg_match("/\tshop\t(.*?)\t([0-9]+),?(.*)/", $item, $sell_items);
                $sell_items = $sell_items[3];
            }
            $item = preg_replace("/,([0-9]+)\t(shop|duplicate\(([^\)]+)\))\t(.*?)\t([0-9]+),?(.*)/", ',$4,$5', $item);
            $item = explode(',', $item);
            $item[3] = explode('#', $item[3]);
            $item[3] = $item[3][0] ? $item[3][0] : 'No Name';
            $item = join(',', $item);
            $item = explode('::', $item);
            $item = $item[0];
            $item = array(
                'npc' => $item,
                'item' => $sell_items
            );
        }unset($item);
        return $data;
    }

    private function getNpc($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\t(script|duplicate\(([^\)]+)\))\t(.*?)\t([0-9]+),?([0-9]+,)?([0-9]+,)?(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            preg_match("/\tduplicate\(([^\)]+)\)\t/", $item, $match);
            $duplicate = $match[1];
            if($duplicate && !preg_match("/\tscript\t" . $duplicate . "\t/", $text)){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/,([0-9]+)\t(script|duplicate\(([^\)]+)\))\t(.*?)\t([0-9]+),?([0-9]+,)?([0-9]+,)?(.*)/", ',$4,$5', $item);
            $item = explode(',', $item);
            $item[3] = explode('#', $item[3]);
            $item[3] = $item[3][0] ? $item[3][0] : 'No Name';
            $item = join(',', $item);
            $item = explode('::', $item);
            $item = $item[0];
        }unset($item);
        return $data;
    }

    private function loadNpc(array $data){
        $sql = 'insert into npcs (`map`, `x`, `y`, `name`, `sprite`, `id`, `is_shop`)values';
        $sth = $this->server->connection->getStatement('select max(id) id from `npcs`');
        $sth->execute();
        $id = $sth->fetch();
        $id = (int)$id->id + 1;
        $array = array();
        $insert = array();
        foreach($data as $item){
            $import = explode(',', $item);
            if(sizeof($import) != 5){
                continue;
            }
            $import[] = $id++;
            $import[] = 0;
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?, ?, ?)';
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
        return sizeof($insert);
    }

    private function getWarps($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\twarp\t(.*?)\t([0-9]+),([0-9]+),(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/,([0-9]+)\twarp\t(.*?)\t([0-9]+),([0-9]+),/", ',', $item);
        }unset($item);
        return $data;
    }

    private function loadWarps(array $data){
        $sql = 'insert into warps (`map`, `x`, `y`, `to`, `tx`, `ty`)values';
        $array = array();
        $insert = array();
        foreach($data as $item){
            $import = explode(',', $item);
            if(sizeof($import) != 6){
                continue;
            }
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?, ?)';
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
        return sizeof($insert);
    }

    private function getMonsters($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*)\t(boss_)?monster\t(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/\t(boss_)?monster\t(.*?)\t/", ',$2,', $item);
        }unset($item);
        return $data;
    }

    private function loadMonsters(array $data){
        $sql = 'insert into mob_spawns (`map`, `x`, `y`, `range_x`, `range_y`, `name`, `mob_id`, `count`, `time_to`, `time_from`)values';
        $array = array();
        $insert = array();
        foreach($data as $item){
            $import = explode(',', $item);
            if(sizeof($import) > 10){
                $import = array_slice($import, 0, 10);
            }
            if(sizeof($import) < 9){
                for($i = sizeof($import) ; $i < 10 ; $i ++){
                    $import[$i] = 0;
                }
            }
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
        return sizeof($insert);
    }




}
