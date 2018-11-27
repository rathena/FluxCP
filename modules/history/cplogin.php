<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('HistoryCpLoginTitle');
$loginLogTable = Flux::config('FluxTables.LoginLogTable');

// Partial SQL query.
$sqlpartial = "WHERE account_id = ?";
$bind       = array($session->account->account_id);

// Fetch record count.
$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

// Paginator.
$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('login_date' => 'desc', 'ip', 'error_code'));

// Fetch actual record data.
$sql = "SELECT ip, login_date, error_code FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$logins = $sth->fetchAll();

if ($logins) {
	$loginErrors = Flux::config('LoginErrors');
	foreach ($logins as $login) {
		if ($errorType=$loginErrors->get($login->error_code)) {
			$login->error_type = $errorType;
		}
		else {
			$login->error_type = null;
		}
	}
}
?>
