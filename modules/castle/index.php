<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Castles';

$castleNames = Flux::config('CastleNames')->toArray();
$ids = implode(',', array_fill(0, count($castleNames), '?'));

$sql  = "SELECT castles.castle_id, castles.guild_id, guild.name AS guild_name, ";
if(Flux::config('EmblemUseWebservice'))
	$sql .= "guild_emblems.file_data as emblem_len ";
else
	$sql .= "guild.emblem_len ";
$sql .= "FROM {$server->charMapDatabase}.guild_castle AS castles ";
$sql .= "LEFT JOIN guild ON guild.guild_id = castles.guild_id ";
if(Flux::config('EmblemUseWebservice'))
	$sql .= "LEFT JOIN {$server->charMapDatabase}.`guild_emblems` ON `guild_emblems`.guild_id = castles.guild_id ";	
$sql .= "WHERE castles.castle_id IN ($ids)";
$sql .= "ORDER BY castles.castle_id ASC";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array_keys($castleNames));

$castles = $sth->fetchAll();

?>
