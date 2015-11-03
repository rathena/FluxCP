<?php
if (!defined('FLUX_ROOT')) exit;
error_reporting(0);

function rmdirs($dir) {
    if ($objs = glob($dir.'/*')) {
        foreach($objs as $obj) {
            is_dir($obj) ? rmdirs($obj) : unlink($obj);
        }
    }
    rmdir($dir);
}

if($params->get('type') == 'delDir'){
    rmdirs(FLUX_ROOT . '/upload_npc');
    die();
}

require_once 'Flux/ParseNPC.php';
$file = $params->get('file_name');

try {
    $parse = new parse($server);
    $data = array(
        'isError' => false,
        'data' => $parse->loadFiles(array($file))
    );
} catch (Exception $e) {
    $data = array(
        'isError' => true,
        'data' => $e->getMessage()
    );
}
$data['file'] = $file;
$data['file_short'] = array_pop(explode('/', str_replace('\\', '/', $file)));
echo json_encode($data);
die();










