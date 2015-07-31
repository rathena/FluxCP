<?php
if (!defined('FLUX_ROOT')) exit;
error_reporting(0);

if($params->get('type') == 'delDir'){
    rmdirs(FLUX_ROOT . DIRECTORY_SEPARATOR . 'mobs_spawn');
    die();
}

$file = $params->get('file_name');

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
    $sth = $server->connection->getStatement($sql);
    $sth->execute($array);
}
echo json_encode(array(
    'file' => $file,
    'total' => sizeof($data)
));
die();




function rmdirs($dir) {
    if ($objs = glob($dir.'/*')) {
        foreach($objs as $obj) {
            is_dir($obj) ? rmdirs($obj) : unlink($obj);
        }
    }
    rmdir($dir);
}








