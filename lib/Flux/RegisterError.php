<?php
require_once 'Flux/Error.php';

class Flux_RegisterError extends Flux_Error {
	const USERNAME_ALREADY_TAKEN = 0;
	const USERNAME_TOO_SHORT     = 1;
	const USERNAME_TOO_LONG      = 2;
	const PASSWORD_TOO_SHORT     = 4;
	const PASSWORD_TOO_LONG      = 5;
	const PASSWORD_MISMATCH      = 6;
	const PASSWORD_NEED_UPPER    = 7;
	const PASSWORD_NEED_LOWER    = 8;
	const PASSWORD_NEED_NUMBER   = 9;
	const PASSWORD_NEED_SYMBOL   = 10;
	const PASSWORD_HAS_USERNAME  = 11;
	const EMAIL_ADDRESS_IN_USE   = 12;
	const INVALID_EMAIL_ADDRESS  = 13;
	const INVALID_GENDER         = 14;
	const INVALID_SERVER         = 15;
	const INVALID_SECURITY_CODE  = 16;
	const INVALID_USERNAME       = 17;
	const INVALID_PASSWORD       = 18;
	const INVALID_BIRTHDATE      = 19;
	const INVALID_EMAIL_CONF     = 20;
}
?>
