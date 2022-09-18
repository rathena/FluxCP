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
        $check = false; // True: execute query
        $checkingdesc = false;
        $desccomplete = false;
        $last_setid = 0;
        $desc = '';
        $setid = 0;
        $first = true;

        for($i=0; $i < $ca; $i++){
            // Item ID
            if (preg_match('/\[(\d+)\]/', $array[$i], $matches)) {
                if ($first) {
                    $setid = $matches[1];
                    $first = false;
                    continue;
                }
                if ($setid != $matches[1] && $checkingdesc) {
                    $itemid = $setid;
                    $desccomplete = true;
                    $check = true;
                }
                $setid = $matches[1];
            }

            // Description Inline type
            // identifiedDescriptionName = { "desc1", "desc2" },
            if (preg_match('/^identifiedDescriptionName[ ]=[ ]\{(.*)\},/', $array[$i], $matches)) {
                $tmp = trim($matches[1]);
                $tmp = substr($tmp,0,strpos($tmp,"},"));
                $str = preg_split('/(",|"$)/', $tmp);
                foreach ($str as $x => $de) {
                    $de = trim($de);
                    $p = strtok($de,'"'); // Remove first quote
                    $desc .= $p."<br />";
                }
                $check = true;
                $desccomplete = true;
            }

            // Description Multiline type
            // identifiedDescriptionName = {
            //     "desc1",
            //     "desc2"
            // },
            if (!$desccomplete && preg_match('/([ \s]+|^)identifiedDescriptionName[ ]=[ ]\{[\r\n]*/', $array[$i])) {
                $checkingdesc = true;
            }
            if ($checkingdesc && preg_match('/"(.*)(",{0,1})[\r\n]*/', $array[$i], $matches)) {
                $tmp = trim($matches[1]);
                $desc .= $tmp;
                if ($matches[2] == '",')
                    $desc .= "<br />";
            }

            if ($check) {
                $newdesc = '';
                $hasColor = false;
                $p = strtok($desc, "^");
                while ($p) {
                    if (preg_match('/([\dA-Fa-f]{6})/', $p, $matches)) {
                        if ($hasColor)
                            $newdesc .= "</font>";
                        if ($matches[1] != '000000') {
                            $newdesc .= "<font color='#".$matches[1]."'>";
                            $hasColor = true;
                        }
                        else
                            $hasColor = false;
                        $newdesc .= substr($p,6,strlen($p));
                    }
                    else
                        $newdesc .= $p;
                    $p = strtok("^");
                }
                if ($hasColor)
                    $newdesc .= "</font>";
                $sql = "REPLACE INTO {$server->charMapDatabase}.$itemDescTable (`itemid`, `itemdesc`) VALUES ('".($checkingdesc ? $itemid : $setid)."','".addslashes($newdesc)."')";
                $sth = $server->connection->getStatement($sql);
                $sth->execute();
                $desc = '';
                $check = false;
                $checkingdesc = false;
                $desccomplete = false;
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
