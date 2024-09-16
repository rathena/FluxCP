<?php
require_once 'Flux/LogFile.php';
require_once 'Flux/Config.php';
require_once 'Flux/Error.php';

/**
 * Manage payments via Stripe.
 */
class Flux_StripeNotifyRequest {
	/**
	 * Logger class for logging to the Stripe log stored on disk.
	 *
	 * @access private
	 * @var Flux_LogFile
	 */
	private $logFile;

    /**
     *  maximum difference allowed between the header's
     *  timestamp and the current time
     *
     * @access private
     * @var int
     */
    private $DEFAULT_TOLERANCE = 300;

    /**
     *  Scheme for Stripe signature
     *
     * @access private
     * @var string
     */
    private $EXPECTED_SCHEME = 'v1';

	/**
	 * Your currently configured currency code.
	 *
	 * @access public
	 * @var string
	 */
	public $myCurrencyCode;

    /**
	 * Payload from Stripe.
	 *
	 * @access public
	 * @var string
	 */
    public $payload;

    /**
     * Stripe HTTP signature.
     *
     * @access public
     * @var string
     */
    public $sigHeader;

	/**
	 * Transactions log table.
	 *
	 * @access public
	 * @var string
	 */
	public $txnLogTable;

    /**
	 * Transactions table.
	 *
	 * @access public
	 * @var string
	 */
	public $txnTable;

	/**
	 * Account credit balance table.
	 *
	 * @access public
	 * @var string
	 */
	public $creditsTable;

    /**
	 * @access public
	 * @var Flux_Athena
	 */
	public $server;

	/**
	 * Construct new Stripe.
	 *
	 * @access public
	 */
	public function __construct(Flux_Athena $server)
	{
        $this->server          = $server;
		$this->logStripe       = new Flux_LogFile(FLUX_DATA_DIR.'/logs/stripe.log');
		$this->creditsTable    = Flux::config('FluxTables.CreditsTable');
        $this->txnTable        = Flux::config('FluxTables.StripeTransactions');
        $this->txnLogTable     = Flux::config('FluxTables.StripeTransactionLogTable');
		$this->myCurrencyCode  = strtoupper(Flux::config('DonationCurrency'));
        $this->webhookSecret   = Flux::config('StripeWebhookSecret');
        $this->sigHeader       = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $this->payload         = @file_get_contents("php://input");
	}

	/**
	 * Log to Stripe log file. Works like printf().
	 *
	 * @param string $format
	 * @param mixed ...
	 * @return string
	 * @access protected
	 */
	protected function logStripe()
	{
		$args = func_get_args();
		$func = array($this->logStripe, 'puts');
		return call_user_func_array($func, $args);
	}

    /**
	 * Process transaction.
	 *
	 * @access public
	 */
    public function process()
    {
        if (!$this->verifyHeader($this->payload, $this->sigHeader, $this->webhookSecret, $this->DEFAULT_TOLERANCE)) {
            http_response_code(400);
            return;
        }

        $this->logStripe('Webhook received!');

        $event = json_decode($this->payload, true);
        $eventObj = $event['data']['object'];

        switch ($event['type']) {
            case 'payment_intent.created':
                $this->createTransactionLog($eventObj, 'payment_intent.created');
                $this->logStripe('PaymentIntent was created!');
                break;

            case 'charge.succeeded':
                $this->createTransactionLog($eventObj, 'charge.succeeded');
                $this->logStripe('PaymentMethod was attached to a Customer!');
                break;

            case 'payment_intent.succeeded':
                $this->createTransactionLog($eventObj, 'payment_intent.succeeded');
                $this->logStripe('PaymentIntent was successful!');
                break;

            case 'payment_intent.payment_failed':
                $this->createTransactionLog($eventObj, 'payment_intent.payment_failed');
                $this->logStripe('PaymentIntent failed!');
                break;

            case 'checkout.session.completed':
                $this->logStripe('Checkout session was completed!');
                $this->createTransactionLog($eventObj, 'checkout.session.completed');
                $this->createTransaction($eventObj);
                break;
            default:
                http_response_code(400);
                return;
        }

        http_response_code(200);
        return;
    }

    /**
     * Verifies the signature header sent by Stripe.
     *
     * @param string $payload the payload sent by Stripe
     * @param string $header the contents of the signature header sent by Stripe
     * @param string $secret secret used to generate the signature
     * @param int $tolerance maximum difference allowed between the header's timestamp and the current time
     *
     *
     * @return bool
     */
    public function verifyHeader($payload, $header, $secret, $tolerance = null)
    {
        // Extract timestamp and signatures from header
        $timestamp = $this->getTimestamp($header);
        $signatures = $this->getSignatures($header, $this->EXPECTED_SCHEME);
        if (-1 === $timestamp) {
            $this->logStripe('Unable to extract timestamp and signatures from header');
            return false;
        }
        if (empty($signatures)) {
            $this->logStripe('No signatures found with expected scheme');
            return false;
        }

        // Check if expected signature is found in list of signatures from
        // header
        $signedPayload = "{$timestamp}.{$payload}";
        $expectedSignature = $this->computeSignature($signedPayload, $secret);
        $signatureFound = false;
        foreach ($signatures as $signature) {
            if ($expectedSignature === $signature) {
                $signatureFound = true;
                break;
            }
        }

        if (!$signatureFound) {
            $this->logStripe('No signatures found matching the expected signature for payload');
            return false;
        }

        // Check if timestamp is within tolerance
        if (($tolerance > 0) && (abs(time() - $timestamp) > $tolerance)) {
            $this->logStripe('Timestamp outside the tolerance zone');
            return false;
        }

        return true;
    }

    /**
     * Extracts the timestamp in a signature header.
     *
     * @param string $header the signature header
     *
     * @return int the timestamp contained in the header, or -1 if no valid
     *  timestamp is found
     */
    private function getTimestamp($header)
    {
        $items = explode(',', $header);

        foreach ($items as $item) {
            $itemParts = explode('=', $item, 2);
            if ('t' === $itemParts[0]) {
                if (!is_numeric($itemParts[1])) {
                    return -1;
                }

                return (int) ($itemParts[1]);
            }
        }

        return -1;
    }

    /**
     * Extracts the signatures matching a given scheme in a signature header.
     *
     * @param string $header the signature header
     * @param string $scheme the signature scheme to look for
     *
     * @return array the list of signatures matching the provided scheme
     */
    private function getSignatures($header, $scheme)
    {
        $signatures = [];
        $items = explode(',', $header);

        foreach ($items as $item) {
            $itemParts = explode('=', $item, 2);
            if (trim($itemParts[0]) === $scheme) {
                $signatures[] = $itemParts[1];
            }
        }

        return $signatures;
    }

    /**
     * Computes the signature for a given payload and secret.
     *
     * The current scheme used by Stripe ("v1") is HMAC/SHA-256.
     *
     * @param string $payload the payload to sign
     * @param string $secret the secret used to generate the signature
     *
     * @return string the signature as a string
     */
    private function computeSignature($payload, $secret)
    {
        return hash_hmac('sha256', $payload, $secret);
    }

    /**
     * Create transaction log.
     *
     * @param array $payload
     * @param string $type
     * @access private
     */
    private function createTransactionLog($payload, $type)
    {
        $event_reference_id = isset($payload['payment_intent']) ? $payload['payment_intent'] : null;
        $error = null;
        $error_reason = null;
        $amount =  $payload['amount'];

        if (isset($payload['amount_total'])) {
            $amount = $payload['amount_total'];
        }

        if (isset($payload['latest_charge'])) {
            $event_reference_id = $payload['latest_charge'];

            if (isset($payload['last_payment_error'])) {
                $error = $payload['last_payment_error']['code'];
                $error_reason = $payload['last_payment_error']['message'];
                $event_reference_id = $payload['last_payment_error']['charge'];
            }
        }

        $this->logStripe('Creating transaction log for %s', $type);
        $sql = "
            INSERT INTO {$this->txnLogTable} (
                event_id,
                event_reference_id,
                object,
                amount,
                currency,
                status,
                error,
                error_reason,
                json_payload,
                created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )
        ";

        $sth = $this->server->connection->getStatement($sql);
        $ret = $sth->execute(array(
            $payload['id'],
            $event_reference_id,
            $payload['object'],
            $amount,
            $payload['currency'],
            $payload['status'],
            $error,
            $error_reason,
            json_encode($payload)
        ));

        if ($ret) {
            $this->logStripe('Stored information in Stripe transactions logs table.');
        }
        else {
            $errorInfo = implode('/', $sth->errorInfo());
            $this->logStripe('Failed to save information in Stripe transactions logs table. (%s)', $errorInfo);
        }
    }

    private function createTransaction($payload)
    {
        $customArray  = @unserialize(base64_decode((string)$payload['client_reference_id']));
        $customArray  = $customArray && is_array($customArray) ? $customArray : array();
        $customData   = new Flux_Config($customArray);
        $accountID    = $customData->get('account_id');
        $serverName   = $customData->get('server_name');
        $email        = $payload['customer_details']['email'];
        $amount       = $payload['amount_total'];
        $minimum      = (float)Flux::config('MinDonationAmount');
        $rate         = Flux::config('CreditExchangeRate');
        $credits      = floor($amount / $rate);

        // How much will be deposited?
        $settleAmount   = $amount / 100;
        $settleCurrency = $payload['currency'];

        if ($settleAmount && $settleCurrency) {
            $this->logStripe('Deposited into PayPal account: %s %s.', $settleAmount, $settleCurrency);
        }

        if ($settleCurrency != $this->myCurrencyCode) {
            $this->logStripe('Transaction currency not exchangeable, accepting anyways. (recv: %s, expected: %s)',
            $settleCurrency, $this->myCurrencyCode);

            $exchangeableCurrency = false;
        }
        else {
            $exchangeableCurrency = true;
        }

        // Let's see where the donation credits should go to.
        $this->logStripe('Game server name: %s, account ID: %s',
            ($serverName ? $serverName : '(absent)'), ($accountID ? $accountID : '(absent)'));

        if (!$accountID || !$serverName) {
            $this->logStripe('Account ID and/or game server name absent, cannot exchange for credits.');
        }
        elseif (!($servGroup = Flux::getServerGroupByName($serverName))) {
            $this->logStripe('Unknown game server "%s", cannot process donation for credits.', $serverName);
            return;
        }

        if ($servGroup && $exchangeableCurrency) {
            $checkServGroup = $this->checkServGroup($accountID);
            if (!$checkServGroup) {
                $this->logStripe('Unknown account #%s on server %s, cannot exchange for credits.', $accountID, $serverName);
                return;
            }
        }
        else {
            if (!$servGroup->loginServer->hasCreditsRecord($accountID)) {
                $this->logStripe('Identified as first-time donation to the server from this account.');
            }

            if ($amount >= $minimum) {
                $sql = "SELECT * FROM {$servGroup->loginDatabase}.{$this->creditsTable} WHERE account_id = ?";
                $sth = $servGroup->connection->getStatement($sql);
                $sth->execute(array($accountID));
                $acc = $sth->fetch();
                $balance = (int)$acc->balance;

                $this->logStripe('Updating account credit balance from %s to %s', $balance, $balance + $credits);
                $res = $servGroup->loginServer->depositCredits($accountID, $credits, $amount);

                if ($res) {
                    $this->logStripe('Deposited credits.');
                }
                else {
                    $this->logStripe('Failed to deposit credits.');
                }
            }
        }

        $sql = "
            INSERT INTO {$this->txnTable} (
                event_reference_id,
                account_id,
                email,
                server_name,
                credits,
                amount,
                settleAmount,
                settleCurrency,
                status,
                payment_status,
                json_payload,
                created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )
        ";

        $sth = $this->server->connection->getStatement($sql);
        $ret = $sth->execute(array(
            $payload['payment_intent'],
            $accountID,
            $email,
            $serverName,
            $credits,
            $amount,
            $settleAmount,
            $settleCurrency,
            $payload['status'],
            $payload['payment_status'],
            json_encode($payload)
        ));

        if ($ret) {
            if (!trim($serverName)) {
                $serverName = '(unknown)';
            }
            $this->logStripe('Stored information in Stripe transactions table for server %s.', $serverName);
        }
        else {
            $errorInfo = implode('/', $sth->errorInfo());
            $this->logStripe('Failed to save information in Stripe transactions table. (%s)', $errorInfo);
            return;
        }
    }

    private function checkServGroup()
    {
        $sql = "SELECT COUNT(account_id) AS acc_id_count FROM {$servGroup->loginDatabase}.login WHERE sex != 'S' AND group_id >= 0 AND account_id = ?";
        $sth = $servGroup->connection->getStatement($sql);
        $sth->execute(array($accountID));
        $res = $sth->fetch();

        if (!$res) {
            return false;
        }

        return true;
    }
}
?>
