<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/FileLoad.php';
$itemDescTable = Flux::config('FluxTables.ItemDescTable');
$title = 'Item Info';
$fileLoad = new FileLoad();

// upload and parse map.
if($files->get('iteminfo')) {
    $itemInfo = FLUX_ROOT . '/itemInfo.lua';
    $is_loaded = $fileLoad->load($files->get('iteminfo'), $itemInfo);
    if($is_loaded === true) {
        $fp = @fopen($itemInfo, 'r'); 
        if($fp){ $array = explode("\n", fread($fp, filesize($itemInfo))); }
        fclose($fp);
        $ca = count($array);
        $check = false;
        for($i=0; $i < $ca; $i++){
            switch($array[$i]){
                case (preg_match('/\s{2,}unident/', $array[$i]) ? true : false):
                    break;
                case (preg_match('/\s([\[])([0-9]{1,})([\]]) = {/', $array[$i]) ? true : false):
                    $setid = preg_replace('/\s([\[])([0-9]{1,})([\]]) = {/', '$2', $array[$i]);
                    break;
                case (preg_match('/\sidentifiedDescriptionName = {$/', $array[$i]) ? true : false):
                    $sql="REPLACE INTO {$server->charMapDatabase}.$itemDescTable (`itemid`, `itemdesc`) VALUES ('$setid','";
                    $check = true;
                    break;
                case 'identifiedDescriptionName = {},':
                    if($check == true){
                        $sql.="');";
                        $sth = $server->connection->getStatement($sql);
                        $sth->execute();
                        $check = false;
                    }
                    break;
                case (preg_match('/(.*?)(\")(.*?)(\^)([0-9a-fA-F]{6})(.*?)(\^0{6})(.*?)\"(,?)/', $array[$i]) ? true : false):
                    $array[$i] = preg_replace('/(.*?)(\")(.*?)(\^)([0-9a-fA-F]{6})(.*?)(\^0{6})(.*?)\"(,?)/', '$3<font color="#$5">$6</font>$8<br />', $array[$i]);
                case (preg_match('/\s{2,}(\")(.*?)\"(,?)/', $array[$i]) ? true : false):
                    if($check == true){
                        $sqlp = preg_replace('/\s{2,}(\")(.*?)\"(,?)/', '$2<br />', $array[$i]);
                        $sql.= addslashes($sqlp);
                    }
                    break;
                case (preg_match('/\s{2,}},/', $array[$i]) ? true : false):
                    if($check == true){
                        $sql.="');";
                        $sth = $server->connection->getStatement($sql);
                        $sth->execute();
                        $check = false;
                    }
                default:
                    break;
            }
        }
        $fileLoad->delete();
    } else {
        $errorMessage = $is_loaded;
    }
}

$sth = $server->connection->getStatement("SELECT COUNT(itemid) AS count FROM {$server->charMapDatabase}.$itemDescTable");
$sth->execute();
$return = $sth->fetch();
