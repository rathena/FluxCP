<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Castles';

$castleNames = Flux::config('CastleNames')->toArray();
$ids = implode(',', array_fill(0, count($castleNames), '?'));

$sql  = "SELECT castles.castle_id, castles.guild_id, castles.economy, guild.name AS guild_name, guild.emblem_id as emblem ";
$sql .= "FROM {$server->charMapDatabase}.guild_castle AS castles ";
$sql .= "LEFT JOIN guild ON guild.guild_id = castles.guild_id ";
$sql .= "WHERE castles.castle_id IN ($ids)";
$sql .= "ORDER BY castles.castle_id ASC";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array_keys($castleNames));

$castles = $sth->fetchAll();

?>