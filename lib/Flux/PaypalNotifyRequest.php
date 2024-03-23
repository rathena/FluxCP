<?php
require_once 'Flux/LogFile.php';
require_once 'Flux/Config.php';
require_once 'Flux/Error.php';

/**
 * Handles PayPal instant payment notifications.
 */
class Flux_PaymentNotifyRequest {
	/**
	 * Logger class for logging to the PayPal log stored on disk.
	 *
	 * @access private
	 * @var Flux_LogFile
	 */
	private $ppLogFile;

	/**
	 * Set to true after the notification has been verified by PayPal.
	 *
	 * @access private
	 * @var bool
	 */
	private $txnIsValid = false;

	/**
	 * PayPal server name to use for verification.
	 *
	 * @access public
	 * @var string
	 */
	public $ppServer;

	/**
	 * Your currently configured PayPal business email.
	 *
	 * @access public
	 * @var string
	 */
	public $myBusinessEmail;

	/**
	 * Your currently configured currency code.
	 *
	 * @access public
	 * @var string
	 */
	public $myCurrencyCode;

	/**
	 * PayPal's IPN variables organized into a Flux_Config instance.
	 *
	 * @access public
	 * @var Flux_Config
	 */
	public $ipnVariables;

	/**
	 * Transactions log table.
	 *
	 * @access public
	 * @var string
	 */
	public $txnLogTable;

	/**
	 * Account credit balance table.
	 *
	 * @access public
	 * @var string
	 */
	public $creditsTable;

	/**
	 * Construct new PaymentNotifyRequest instance from specified IPN variables.
	 *
	 * @param array $ipnPostVars
	 * @access public
	 */
	public function __construct(array $ipnPostVars)
	{
		$this->ppLogFile       = new Flux_LogFile(FLUX_DATA_DIR.'/logs/paypal.log');
		$this->ppServer        = Flux::config('PayPalIpnUrl');
		$this->myBusinessEmail = Flux::config('PayPalBusinessEmail');
		$this->myCurrencyCode  = strtoupper(Flux::config('DonationCurrency'));
		$this->ipnVariables    = new Flux_Config($ipnPostVars);
		$this->txnLogTable     = Flux::config('FluxTables.TransactionTable');
		$this->creditsTable    = Flux::config('FluxTables.CreditsTable');
	}

	/**
	 * Log to PayPal log file. Works like printf().
	 *
	 * @param string $format
	 * @param mixed ...
	 * @return string
	 * @access protected
	 */
	protected function logPayPal()
	{
		$args = func_get_args();
		$func = array($this->ppLogFile, 'puts');
		return call_user_func_array($func, $args);
	}
	
	/**
     * Get user IP.
     * Checks if CloudFlare used to get real IP.
     *
     * @access public
     */
    protected function fetchIP()
    {
        $alt_ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $alt_ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $alt_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $alt_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        return $alt_ip;
    }

	/**
	 * Process transaction.
	 *
	 * @access public
	 */
	public function process()
	{
		$allowed_hosts = Flux::config('PayPalAllowedHosts')->toArray();
		$received_from = gethostbyaddr($this->fetchIP());
		$this->logPayPal('Received notification from %s (%s)', $this->fetchIP(), $received_from);

		if ((in_array($received_from, $allowed_hosts) && $this->verify()) || $this->verifyiprange($received_from)) {
			$this->logPayPal('Proceeding to validate the authenticity of the transaction...');

			$accountEmails = Flux::config('PayPalReceiverEmails');
			$accountEmails = array_merge(array($this->myBusinessEmail), $accountEmails->toArray());
			$receiverEmail = $this->ipnVariables->get('receiver_email');
			$transactionID = $this->ipnVariables->get('txn_id');
			$paymentStatus = $this->ipnVariables->get('payment_status');
			$payerEmail    = $this->ipnVariables->get('payer_email');
			$currencyCode  = strtoupper(substr($this->ipnVariables->get('mc_currency'), 0, 3));
			$trusted       = true;

			// Identify transaction number.
			$this->logPayPal('Transaction identified as %s.', $transactionID);

			if (!in_array($receiverEmail, $accountEmails)) {
				$this->logPayPal('Receiver e-mail (%s) is not recognized, unauthorized to continue.', $receiverEmail);
			}
			else {
				$customArray  = @unserialize(base64_decode((string)$this->ipnVariables->get('custom')));
				$customArray  = $customArray && is_array($customArray) ? $customArray : array();
				$customData   = new Flux_Config($customArray);
				$accountID    = $customData->get('account_id');
				$serverName   = $customData->get('server_name');

				if ($currencyCode != $this->myCurrencyCode) {
					$this->logPayPal('Transaction currency not exchangeable, accepting anyways. (recv: %s, expected: %s)',
						$currencyCode, $this->myCurrencyCode);

					$exchangeableCurrency = false;
				}
				else {
					$exchangeableCurrency = true;
				}

				// How much was received? (and in what currency?)
				$this->logPayPal('Received %s (%s).', $this->ipnVariables->get('mc_gross'), $currencyCode);

				// How much will be deposited?
				$settleAmount   = $this->ipnVariables->get('settle_amount');
				$settleCurrency = $this->ipnVariables->get('settle_currency');

				if ($settleAmount && $settleCurrency) {
					$this->logPayPal('Deposited into PayPal account: %s %s.', $settleAmount, $settleCurrency);
				}

				// Let's see where the donation credits should go to.
				$this->logPayPal('Game server name: %s, account ID: %s',
					($serverName ? $serverName : '(absent)'), ($accountID ? $accountID : '(absent)'));

				if (!$accountID || !$serverName) {
					$this->logPayPal('Account ID and/or game server name absent, cannot exchange for credits.');
				}
				elseif ($this->ipnVariables->get('txn_type') != 'web_accept') {
					$this->logPayPal('Transaction type is not web_accept, amount will not be exchanged for credits.');
				}
				elseif (!($servGroup = Flux::getServerGroupByName($serverName))) {
					$this->logPayPal('Unknown game server "%s", cannot process donation for credits.', $serverName);
				}

				if ($paymentStatus == 'Completed') {
					$this->logPayPal('Payment for txn_id#%s has been completed.', $transactionID);

					if ($servGroup && $exchangeableCurrency) {
						$sql = "SELECT COUNT(account_id) AS acc_id_count FROM {$servGroup->loginDatabase}.login WHERE sex != 'S' AND group_id >= 0 AND account_id = ?";
						$sth = $servGroup->connection->getStatement($sql);
						$sth->execute(array($accountID));
						$res = $sth->fetch();

						if (!$res) {
							$this->logPayPal('Unknown account #%s on server %s, cannot exchange for credits.', $accountID, $serverName);
						}
						else {
							if (!$servGroup->loginServer->hasCreditsRecord($accountID)) {
								$this->logPayPal('Identified as first-time donation to the server from this account.');
							}

							$amount  = (float)$this->ipnVariables->get('mc_gross');
							$minimum = (float)Flux::config('MinDonationAmount');

							if ($amount >= $minimum) {
								$trustTable = Flux::config('FluxTables.DonationTrustTable');
								$holdHours  = +(int)Flux::config('HoldUntrustedAccount');

								if ($holdHours) {
									$sql = "SELECT account_id, email FROM {$servGroup->loginDatabase}.$trustTable WHERE account_id = ? AND email = ? LIMIT 1";
									$sth = $servGroup->connection->getStatement($sql);
									$sth->execute(array($accountID, $payerEmail));
									$res = $sth->fetch();

									if ($res && $res->account_id) {
										$this->logPayPal('Account ID and e-mail are trusted.');
										$trusted = true;
									}
									else {
										$trusted = false;
									}
								}

								$rate    = Flux::config('CreditExchangeRate');
								$credits = floor($amount / $rate);

								if ($trusted) {
									$sql = "SELECT * FROM {$servGroup->loginDatabase}.{$this->creditsTable} WHERE account_id = ?";
									$sth = $servGroup->connection->getStatement($sql);
									$sth->execute(array($accountID));
									$acc = $sth->fetch();

									$this->logPayPal('Updating account credit balance from %s to %s', (int)$acc->balance, $acc->balance + $credits);
									$res = $servGroup->loginServer->depositCredits($accountID, $credits, $amount);

									if ($res) {
										$this->logPayPal('Deposited credits.');
									}
									else {
										$this->logPayPal('Failed to deposit credits.');
									}
								}
								else {
									$this->logPayPal('Account/e-mail is not trusted, holding donation credits for %d hours.', $holdHours);
								}
							}
							else {
								$this->logPayPal('User has donated less than the configured minimum, not exchanging credits.');
							}
						}
					}
				}
				else {
					$this->logPayPal('Incomplete payment status: %s (exchanging for credits will not take place)', $paymentStatus);

					$banStatuses = Flux::config('BanPaymentStatuses');

					if ($banStatuses instanceOf Flux_Config) {
						$banStatuses = $banStatuses->toArray();
					}
					else {
						$banStatuses = array();
					}

					$pymntStatus = strtolower($paymentStatus);
					$banStatuses = array_map('strtolower', $banStatuses);

					if (in_array($pymntStatus, $banStatuses)) {
						$this->logPayPal('Auto-ban payment status detected: %s', $paymentStatus);

						if ($servGroup && $serverName && $accountID) {
							$this->logPayPal('Banning account! (serv: %s, account_id: %s)', $serverName, $accountID);
							$servGroup->loginServer->permanentlyBan(
								null, "Banned for invalid payment status: $paymentStatus",
								$accountID
							);
						}
						else {
							$this->logPayPal("Couldn't ban account, it's unknown.");
						}
					}
				}

				if (!$servGroup) {
					foreach (Flux::$loginAthenaGroupRegistry as $servGroup) {
						$this->logToPayPalTable($servGroup, $accountID, $serverName, $trusted);
					}
				}
				else {
					if (empty($credits)) {
						$credits = 0;
					}
					$this->logToPayPalTable($servGroup, $accountID, $serverName, $trusted, $credits);
				}

				$this->logPayPal('Saving transaction details for %s...', $transactionID);

				if ($logFile=$this->saveDetailsToFile()) {
					$this->logPayPal('Saved transaction details for %s to: %s', $transactionID, $logFile);
				}
				else {
					$this->logPayPal('Failed to save transaction details for %s to file.', $transactionID);
				}

				$this->logPayPal('Done processing %s.', $transactionID);
			}
		}
		else {
			$this->logPayPal('Transaction invalid, aborting.');
			
			if(!in_array($received_from, $allowed_hosts) && Flux::config('PaypalHackNotify')){
				require_once 'Flux/Mailer.php';
				
				$customArray  = @unserialize(base64_decode((string)$this->ipnVariables->get('custom')));
				$customArray  = $customArray && is_array($customArray) ? $customArray : array();
				$customData   = new Flux_Config($customArray);
				$accountID    = $customData->get('account_id');
				$serverName   = $customData->get('server_name');
				
				$mail = new Flux_Mailer();
				
				$tmpl = "<p>Paypal hack detected!</p>";
				$tmpl .= "<p>Account: ".$accountID."</p>";
				$tmpl .= "<p>serverName: ".$serverName."</p>";
				
				$tmpl .= "<br><br><br>";
				$tmpl .= "<p>======= IP Info ========</p>";
				$tmpl .= nl2br(var_export(['ip' => $this->fetchIP(), 'host' => $received_from], true));
				$tmpl .= "<p>======= End IP Info ========</p>";
				$tmpl .= "<br><br><br>";
				$tmpl .= "<p>======= Account Info ========</p>";
				$tmpl .= nl2br(var_export($customData, true));
				$tmpl .= "<p>======= End Account Info ========</p>";
				$tmpl .= "<br><br><br>";
				$tmpl .= "<p>======= Transaction Info ========</p>";
				$tmpl .= nl2br(var_export($this->ipnVariables->toArray(), true));
				$tmpl .= "<p>======= End Transaction Info ========</p>";
				
				$accountEmails = Flux::config('PayPalReceiverEmails');
				$accountEmails = array_merge(array($this->myBusinessEmail), $accountEmails->toArray());
				
				foreach($accountEmails as $email) {
					$sent = $mail->send($email, '['.Flux::config('SiteTitle').'] Paypal hack', $tmpl, array('_ignoreTemplate' => true));
				}
				
				$this->logPayPal('Hack detected!');
			}
		}

		return false;
	}

	/**
	 * Translate the IPN variables into a query string for use in a POST
	 * request.
	 *
	 * @return string
	 * @access private
	 */
	private function ipnVarsToQueryString()
	{
		$ipnVars = $this->ipnVariables->toArray();
		$qString = '';
		foreach ($ipnVars as $key => $value) {
			$qString .= sprintf('&%s=%s', $key, urlencode($value));
		}
		$qString = ltrim($qString, '&');
		return $qString;
	}

	/**
	 * Verify IPN variables against PayPal server.
	 *
	 * Updated to comply with changes being implemented Feb 1, 2013
	 * https://www.x.com/node/320404
	 *
	 * @return bool True if verified, false if not.
	 * @access private
	 */
	private function verify()
	{
		$qString  = 'cmd=_notify-validate&'.$this->ipnVarsToQueryString();
		$request  = "POST /cgi-bin/webscr HTTP/1.1\r\n";
		$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$request .= 'Content-Length: '.strlen($qString)."\r\n";
		$request .= 'Host: '.$this->ppServer."\r\n";
		$request .= "Connection: close\r\n\r\n";
		$request .= $qString;

		$this->logPayPal('Query string: %s', $qString);
		$this->logPayPal('Establishing connection to PayPal server at %s:443...', $this->ppServer);

		$fp = @fsockopen('ssl://'.$this->ppServer, 443, $errno, $errstr, 20);
		if (!$fp) {
			$this->logPayPal("Failed to connect to PayPal server: [%d] %s", $errno, $errstr);
			return false;
		}
		else {
			$this->logPayPal('Connected. Sending request back to PayPal...');

			// Send POST request just as PayPal sent it.

			$this->logPayPal('Sent %d bytes of transaction data. Request size: %d bytes.', strlen($qString), fputs($fp, $request));
			$this->logPayPal('Reading back response from PayPal...');

			// Read until body starts
			while (!feof($fp) && ($line = trim(fgets($fp))) != '');
			
			$line = '';

			// Read until EOF, contains VERIFIED or INVALID.
			while (!feof($fp)) {
				$line .= strtoupper(trim(fgets($fp)));
			}

			// Close connection.
			fclose($fp);

			// Check verification status of the notify request.
			if (strpos($line, 'VERIFIED') !== false) {
				$this->logPayPal('Notification verified. (recv: %s)', $line);
				$this->txnIsValid = true;
				return true;
			}
			else {
				$this->logPayPal('Notification failed to verify. (recv: %s)', $line);
				return false;
			}
		}
	}

	/*

	*/
	private function verifyiprange($received_from)
	{
		$allowed_hosts = Flux::config('PayPalAllowedHosts')->toArray();
		$ip_long = ip2long ( $received_from );

		for ($i = 0; $i < 72; $i++)
		{
			if(strpos($allowed_hosts[$i], '/') !== false) {
				$ip_arr = explode ( '/' , $allowed_hosts[$i] );

				$network_long = ip2long ( $ip_arr[0] );
		 
				$x = ip2long ( $ip_arr [1]);
				$mask = long2ip ( $x ) == $ip_arr [ 1 ] ? $x : 0xffffffff << ( 32 - $ip_arr [ 1 ]);
			   
				if(( $ip_long & $mask ) == ( $network_long & $mask ))
					return true;
			} else {
				if($allowed_hosts[$i] == $received_from)
					return true;
			}
		}
		return false;
    }

	/**
	 * Save the transaction details to disk in the file name format of:
	 * data/logs/transactions/TXN_TYPE/PAYMENT_STATUS.log
	 *
	 * @return string File name
	 * @access private
	 */
	private function saveDetailsToFile()
	{
		if ($this->txnIsValid) {
			$logDir1 = realpath(FLUX_DATA_DIR.'/logs/transactions');
			$logDir2 = $logDir1.'/'.$this->ipnVariables->get('txn_type');
			$logDir3 = $logDir2.'/'.$this->ipnVariables->get('payment_status');
			$logFile = $logDir3.'/'.$this->ipnVariables->get('txn_id').'.log.php';

			if (!is_dir($logDir2)) {
				mkdir($logDir2, 0600);
			}
			if (!is_dir($logDir3)) {
				mkdir($logDir3, 0600);
			}

			$fp = fopen($logFile, 'w');
			if ($fp) {
				fwrite($fp, "<?php exit('Forbidden'); ?>\n");
				foreach ($this->ipnVariables->toArray() as $key => $value) {
					fwrite($fp, "$key: $value\n");
				}
				fclose($fp);
				return $logFile;
			}
		}
		return false;
	}

	/**
	 * Log the transaction details into the flux_paypal_transactions table.
	 *
	 * @param Flux_LoginAthenaGroup $servGroup
	 * @param string $accountID
	 * @param string $serverName
	 * @access private
	 */
	private function logToPayPalTable(Flux_LoginAthenaGroup $servGroup, $accountID, $serverName, $trusted, $credits = 0)
	{
		if ($this->txnIsValid) {
			$holdUntil = null;
			if (!$trusted) {
				$email = $this->ipnVariables->get('payer_email');
				$sql   = "SELECT hold_until FROM {$servGroup->loginDatabase}.{$this->txnLogTable} ";
				$sql  .= "WHERE account_id = ? AND payer_email = ? AND hold_until > NOW() AND payment_status = 'Completed' LIMIT 1";
				$sth   = $sth = $servGroup->connection->getStatement($sql);

				$sth->execute(array($accountID, $email));
				$row = $sth->fetch();

				if ($row && $row->hold_until) {
					$holdUntil = $row->hold_until;
				}
				else {
					$hours     = +(int)Flux::config('HoldUntrustedAccount');
					$holdUntil = date('Y-m-d H:i:s', time()+($hours*60*60));
				}
			}

			$this->logPayPal('Saving transaction details to PayPal transactions table...');
			$sql = "
				INSERT INTO {$servGroup->loginDatabase}.{$this->txnLogTable} (
					account_id,
					server_name,
					credits,
					receiver_email,
					item_name,
					item_number,
					quantity,
					payment_status,
					pending_reason,
					payment_date,
					mc_gross,
					mc_fee,
					tax,
					mc_currency,
					parent_txn_id,
					txn_id,
					txn_type,
					first_name,
					last_name,
					address_street,
					address_city,
					address_state,
					address_zip,
					address_country,
					address_status,
					payer_email,
					payer_status,
					payment_type,
					notify_version,
					verify_sign,
					referrer_id,
					process_date,
					hold_until
				) VALUES (
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
					?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(),
					?
				)
			";
			$var = $this->ipnVariables;
			$sth = $servGroup->connection->getStatement($sql);
			$ret = $sth->execute(array(
				$accountID,
				$serverName,
				$credits,
				$var->get('receiver_email'),
				$var->get('item_name'),
				$var->get('item_number'),
				$var->get('quantity'),
				$var->get('payment_status'),
				$var->get('pending_reason'),
				$var->get('payment_date'),
				$var->get('mc_gross'),
				$var->get('mc_fee'),
				$var->get('tax'),
				$var->get('mc_currency'),
				$var->get('parent_txn_id'),
				$var->get('txn_id'),
				$var->get('txn_type'),
				$var->get('first_name'),
				$var->get('last_name'),
				$var->get('address_street'),
				$var->get('address_city'),
				$var->get('address_state'),
				$var->get('address_zip'),
				$var->get('address_country'),
				$var->get('address_status'),
				$var->get('payer_email'),
				$var->get('payer_status'),
				$var->get('payment_type'),
				$var->get('notify_version'),
				$var->get('verify_sign'),
				$var->get('receiver_id'),
				$holdUntil
			));

			if ($ret) {
				if (!trim($serverName)) {
					$serverName = '(unknown)';
				}
				$this->logPayPal('Stored information in PayPal transactions table for server %s.', $serverName);
			}
			else {
				$errorInfo = implode('/', $sth->errorInfo());
				$this->logPayPal('Failed to save information in PayPal transactions table. (%s)', $errorInfo);
			}
		}
	}
}
?>
