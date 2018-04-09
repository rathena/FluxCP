<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ResetPassButton');

$account = $params->get('account');
$code    = $params->get('code');
$login   = $params->get('login');

$resetPassTable = Flux::config('FluxTables.ResetPasswordTable');

if (!$login || !$account || !$code || strlen($code) !== 32) {
	$this->deny();
}

$loginAthenaGroup = Flux::getServerGroupByName($login);
if (!$loginAthenaGroup) {
	$this->deny();
}

$sql = "SELECT userid, email FROM {$loginAthenaGroup->loginDatabase}.login WHERE account_id = ? LIMIT 1";
$sth = $loginAthenaGroup->connection->getStatement($sql);
$sth->execute(array($account));
$acc = $sth->fetch();

if (!$acc) {
	$this->deny();
}

$sql  = "SELECT id FROM {$loginAthenaGroup->loginDatabase}.$resetPassTable WHERE ";
$sql .= "account_id = ? AND code = ? AND reset_done = 0 LIMIT 1";
$sth  = $loginAthenaGroup->connection->getStatement($sql);

if (!$sth->execute(array($account, $code)) || !($reset=$sth->fetch())) {
	$this->deny();
}

$sql  = "UPDATE {$loginAthenaGroup->loginDatabase}.$resetPassTable SET ";
$sql .= "reset_done = 1, reset_date = NOW(), reset_ip = ?, new_password = ? WHERE id = ?";
$sth  = $loginAthenaGroup->connection->getStatement($sql);

$newPassword = '';
$characters  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
$characters  = str_split($characters, 1);
$passLength  = intval(($len=Flux::config('RandomPasswordLength')) < 8 ? 8 : $len);

for ($i = 0; $i < $passLength; ++$i) {
	$newPassword .= $characters[array_rand($characters)];
}

$unhashedNewPassword = $newPassword;
if ($loginAthenaGroup->loginServer->config->getUseMD5()) {
	$newPassword = Flux::hashPassword($newPassword);
}

if (!$sth->execute(array($_SERVER['REMOTE_ADDR'], $newPassword, $reset->id))) {
	$session->setMessageData(Flux::message('ResetPwFailed'));
	$this->redirect();	
}

$sql = "UPDATE {$loginAthenaGroup->loginDatabase}.login SET user_pass = ? WHERE account_id = ?";
$sth = $loginAthenaGroup->connection->getStatement($sql);

if (!$sth->execute(array($newPassword, $account))) {
	$session->setMessageData(Flux::message('ResetPwFailed'));
	$this->redirect();
}

require_once 'Flux/Mailer.php';
$mail = new Flux_Mailer();
$sent = $mail->send($acc->email, 'Password Has Been Reset', 'newpass', array('AccountUsername' => $acc->userid, 'NewPassword' => $unhashedNewPassword));

if ($sent) {
	$message = Flux::message('ResetPwDone');
}
else {
	$message = Flux::message('ResetPwDone2');
}

$session->setMessageData($message);
$this->redirect();
?>
