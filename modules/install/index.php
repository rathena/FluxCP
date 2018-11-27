<?php
if (!defined('FLUX_ROOT')) exit;

require_once 'Flux/Installer/SchemaPermissionError.php';

// Force debug mode off here.
Flux::config('DebugMode', false);

if ($session->installerAuth) {
	if ($params->get('logout')) {
		$session->setInstallerAuthData(false);
	}
	else {
		$requiredMySqlVersion = '5.0';

		foreach (Flux::$loginAthenaGroupRegistry as $serverName => $loginAthenaGroup) {
			$sth = $loginAthenaGroup->connection->getStatement("SELECT VERSION() AS mysql_version, CURRENT_USER() AS mysql_user");
			$sth->execute();
			
			$res = $sth->fetch();
			if (!$res || version_compare($res->mysql_version, $requiredMySqlVersion, '<')) {
				$message  = "MySQL version $requiredMySqlVersion or greater is required for Flux.";
				$message .= $res ? " You are running version {$res->mysql_version}" : "You are running an unknown version";
				$message .= " on the server '$serverName'"; 
				throw new Flux_Error($message);
			}
		}
		
		if ($params->get('update_all')) {
			try {
				$installer->updateAll();
				if (!$installer->updateNeeded()) {
					$session->setMessageData('Updates have been installed.');
					$session->setInstallerAuthData(false);
					$this->redirect();
				}
			}
			catch (Flux_Installer_SchemaPermissionError $e) {
				$permissionError = $e;
			}
		}
		elseif (($username=$params->get('username')) && $username instanceOf Flux_Config &&
				($password=$params->get('password')) && $password instanceOf Flux_Config &&
				($update=$params->get('update')) && $update instanceOf Flux_Config) {
				
			$server64     = key($update->toArray());
			$username     = $username->get($server64);
			$password     = $password->get($server64);
			$serverName   = base64_decode($server64);
			$server       = array_key_exists($serverName, $installer->servers) ? $installer->servers[$serverName] : false;
			$updateNeeded = false;
			
			if ($server) {
				foreach ($server->schemas as $schema) {
					if (!$schema->isLatest()) {
						$updateNeeded = true;
						break;
					}
				}
				
				if (!$updateNeeded) {
					foreach ($server->charMapServers as $charMapServer) {
						foreach ($charMapServer->schemas as $schema) {
							if (!$schema->isLatest()) {
								$updateNeeded = true;
								break;
							}
						}
					}
				}
			}
			
			if (!$updateNeeded || !$server) {
				$errorMessage = 'Invalid server or the server has no updates.';
			}
			elseif (!$username || !$password) {
				$errorMessage = "Username and password are required for individual server updates.";
			}
			else {
				$connection = $server->loginAthenaGroup->connection;
				$connection->reconnectAs($username, $password);
				try {
					$server->updateAll();
					$session->setMessageData("Updates for $serverName have been installed.");
					$this->redirect();
				}
				catch (Flux_Installer_SchemaPermissionError $e) {
					$permissionError = $e;
				}
			}
		}
	}
}

if (count($_POST) && !$session->installerAuth) {
	$inputPassword  = $params->get('installer_password');
	$actualPassword = Flux::config('InstallerPassword');
	
	if ($inputPassword == $actualPassword) {
		$session->setInstallerAuthData(true);
	}
	else {
		$errorMessage = 'Incorrect password.';
	}
}
?>
