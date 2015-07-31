<?php
if (!defined('FLUX_ROOT')) exit;
error_reporting(0);


$title = 'Spawn Monsters';

if($params->get('act')){
    switch($params->get('act')){
        case 'truncate':
            try {
                $sth = $server->connection->getStatement('truncate table `mob_spawns`; truncate table `map_index`;');
                $sth->execute();
            } catch(Exception $e){}

            $successMessage = 'Database successfully clean';
            break;
        case 'create':
            $sth = $server->connection->getStatement('
CREATE TABLE IF NOT EXISTS `mob_spawns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map` varchar(20) NOT NULL,
  `x` smallint(4) NOT NULL,
  `y` smallint(4) NOT NULL,
  `range_x` smallint(4) NOT NULL,
  `range_y` smallint(4) NOT NULL,
  `mob_id` smallint(5) NOT NULL,
  `count` smallint(4) NOT NULL,
  `name` varchar(40) NOT NULL,
  `time_to` int(11) NOT NULL,
  `time_from` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map` (`map`),
  KEY `mob_id` (`mob_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `map_index` (
  `name` varchar(20) NOT NULL,
  `x` smallint(4) NOT NULL,
  `y` smallint(4) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
');
            $sth->execute();
            $successMessage = 'Database successfully create';
            break;
        case 'delete':
            try {
                $sth = $server->connection->getStatement('drop table `mob_spawns`; drop table `map_index`;');
                $sth->execute();
            } catch(Exception $e){}
            $successMessage = 'Database successfully delete';
            break;
    }
}

if($files->get('map_index')) {
    $tmp = $files->get('map_index')->get('tmp_name');
    $array_insert = array();

    $data = file_get_contents($tmp);
    // parse map_cache.dat
    $array = array(
        // 12 symbol - map name
        array('A12', 12),
        // size x and y
        array('S', 2),
        array('S', 2),
        // compress sells data length
        array('L', 4),
    );

    $count = 0;
    $i = 8;
    while($i < strlen($data)){
        $byte = '';
        for($k = $i ; $k < $i + $array[$count][1] ; $k++){
            $byte .= $data[$k];
        }
        $datas = unpack($array[$count][0], $byte);
        if($count != 3) {
            $array_insert[] = $datas[1];
        }
        $i += $array[$count][1];
        $count ++;
        if(!isset($array[$count])) {
            $count = 0;
            $i += $datas[1];
        }
    }

    if(sizeof($array_insert) % 3 != 0){
        $errorMessage = 'File map_cache.dat not validate';
        return;
    }
    $rows = sizeof($array_insert) / 3;
    $sql  = 'insert into map_index (`name`, `x`, `y`)values';
    $insert = array();
    for($i = 0 ; $i < $rows ; $i ++){
        $insert[] = '(?, ?, ?)';
    }

    try {
        $sql .= join(',', $insert);
        $sth = $server->connection->getStatement($sql);
        $sth->execute($array_insert);
        $successMessage = 'Maps successfully added to database. Total maps - ' . ($rows);
    } catch(Exception $e){
        $errorMessage = $e->getMessage();
    }
}

if($files->get('mobs_zip')) {
    $dirExtract = FLUX_ROOT . DIRECTORY_SEPARATOR . 'mobs_spawn';
    $zip = new ZipArchive;
    if ($zip->open($files->get('mobs_zip')->get('tmp_name')) === true) {
        $zip->extractTo($dirExtract);
        $zip->close();
        $file = scanDirs($dirExtract);
        foreach($file as &$f){
            $f = str_replace('\\', '/', $f);
        }unset($f);
    } else {
        $file = array();
        $errorMessage = 'file must be ZIP ARCHIVE';
    }
    if(sizeof($file) == 0){
        $errorMessage = 'files in the archive not found';
    }
} else {
    $file = array();
}



try {
    $sth = $server->connection->getStatement('select count(*) as count from `mob_spawns`');
    $sth->execute();
    $MobSpawnBase = $sth->fetch()->count;
    if($MobSpawnBase === false || $MobSpawnBase === null){
        throw new Flux_Error('db not found');
    }
} catch(Exception $e){
    $MobSpawnBase = false;
}
try {
    $sth = $server->connection->getStatement('select count(*) as count from `map_index`');
    $sth->execute();
    $mapIndexBase = $sth->fetch()->count;
    if($mapIndexBase === false || $mapIndexBase === null){
        throw new Flux_Error('db not found');
    }
} catch(Exception $e){
    $mapIndexBase = false;
}





function scanDirs($path, $array = array()){
    $dir = array_diff(scandir($path), array('.', '..'));
    foreach($dir as $item){
        $innerDir = $path . DIRECTORY_SEPARATOR . $item;
        if(is_dir($innerDir)){
            $array = array_merge(scanDirs($innerDir, $array), $array);
        } else {
            $array[$innerDir] = $innerDir;
        }
    }
    return $array;
}
