<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('TransferTitle');

if (count($_POST)) {
	if ($session->account->balance) {
		$credits  = (int)$params->get('credits'); 
		$charName = trim($params->get('char_name'));
		
		if (!$credits || $credits < 1) {
			$errorMessage = Flux::message('TransferGreaterThanOne');
		}
		elseif (!$charName) {
			$errorMessage = Flux::message('TransferEnterCharName');
		}
		else {
			$res = $server->transferCredits($session->account->account_id, $charName, $credits);
			
			if ($res === -3) {
				$errorMessage = sprintf(Flux::message('TransferNoCharExists'), $charName);
			}
			elseif ($res === -2) {
				$errorMessage = Flux::message('TransferNoBalance');
			}
			elseif ($res !== true) {
				$errorMessage = Flux::message('TransferUnexpectedError');
			}
			else {
				$session->setMessageData(Flux::message('TransferSuccessful'));
				$this->redirect();
			}
		}
	}
	else {
		$this->deny();
	}
}
?>
