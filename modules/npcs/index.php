<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List NPCs';

try {
    $sth = $server->connection->getStatement("SELECT COUNT(*) AS total FROM `npcs`");
    $sth->execute();
    $paginator = $this->getPaginator($sth->fetch()->total);
    $paginator->setSortableColumns(array(
        'map' => 'is_shop',
        'name' => 'name',
        'is_shop' => 'is_shop'
    ));

    $sql  = $paginator->getSQL("SELECT * FROM `npcs`");
    $sth  = $server->connection->getStatement($sql);

    $sth->execute();
    $npcs = $sth->fetchAll();

    $authorized = $auth->actionAllowed('npcs', 'view');

    if ($monsters && count($monsters) === 1 && $authorized && Flux::config('SingleMatchRedirectMobs')) {
        $this->redirect($this->url('npcs', 'view', array('id' => $monsters[0]->monster_id)));
    }
}
catch (Exception $e) {
    // Raise the original exception.
    $class = get_class($e);
    throw new $class($e->getMessage());
}
