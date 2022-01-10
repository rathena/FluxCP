<?php
// This file should control all access to specified modules and actions.
return array(
	// Module/action permissions.
	// These are handled during runtime by Flux.
	// '*' is a default that is checked for any action that has not been
	// specified an access level.
	'modules' => array(
		'main'      => array(
			'*'        => AccountLevel::ANYONE
		),
		'donate'    => array(
			'index'    => AccountLevel::ANYONE,
			'notify'   => AccountLevel::ANYONE,
			'update'   => AccountLevel::ANYONE,
			'complete' => AccountLevel::ANYONE,
			'history'  => AccountLevel::NORMAL,
			'trusted'  => AccountLevel::NORMAL
		),
		'purchase'  => array(
			'index'    => AccountLevel::ANYONE,
			'add'      => AccountLevel::ANYONE,
			'clear'    => AccountLevel::NORMAL,
			'cart'     => AccountLevel::NORMAL,
			'checkout' => AccountLevel::NORMAL,
			'remove'   => AccountLevel::NORMAL,
			'pending'  => AccountLevel::NORMAL
		),
		'itemshop'  => array(
			'add'      => AccountLevel::ADMIN,
			'edit'     => AccountLevel::ADMIN,
			'delete'   => AccountLevel::ADMIN,
			'imagedel' => AccountLevel::ADMIN
		),
		'cashshop'  => array(
			'index'    => AccountLevel::ADMIN,
			'add'      => AccountLevel::ADMIN,
			'edit'     => AccountLevel::ADMIN,
			'delete'   => AccountLevel::ADMIN
		),
		'account'   => array(
			'index'    => AccountLevel::LOWGM,
			'view'     => AccountLevel::NORMAL,
			'create'   => AccountLevel::UNAUTH,
			'login'    => AccountLevel::UNAUTH,
			'logout'   => AccountLevel::NORMAL,
			'transfer' => AccountLevel::NORMAL,
			'xferlog'  => AccountLevel::NORMAL,
			'cart'     => AccountLevel::NORMAL,
			'changepass' => AccountLevel::NORMAL,
			'edit'       => AccountLevel::ADMIN,
			'changesex'  => AccountLevel::NORMAL,
			'confirm'    => AccountLevel::UNAUTH,
			'resend'     => AccountLevel::UNAUTH,
			'resetpass'  => AccountLevel::UNAUTH,
			'resetpw'    => AccountLevel::UNAUTH,
			'changemail' => AccountLevel::NORMAL,
			'confirmemail' => AccountLevel::NORMAL,
			'prune'        => AccountLevel::ANYONE
		),
		'character'	=> array(
			'index'			=> AccountLevel::LOWGM,
			'view'			=> AccountLevel::NORMAL,
			'online'		=> AccountLevel::ANYONE,
			'prefs'			=> AccountLevel::NORMAL,
			'changeslot'	=> AccountLevel::NORMAL,
			'resetlook'		=> AccountLevel::NORMAL,
			'resetpos'		=> AccountLevel::NORMAL,
			'mapstats'		=> AccountLevel::ANYONE,
			'divorce'		=> AccountLevel::NORMAL
		),
		'guild'		=> array(
			'emblem'		=> AccountLevel::ANYONE,
			'index'			=> AccountLevel::LOWGM,
			'export'		=> AccountLevel::ADMIN,
			'view'			=> AccountLevel::NORMAL
		),
		'castle'	=> array(
			'index'			=> AccountLevel::ANYONE
		),
		'economy'	=> array(
			'index'			=> AccountLevel::NORMAL
		),
		'auction'	=> array(
			'index'			=> AccountLevel::LOWGM
		),
		'ranking'	=> array(
			'character'		=> AccountLevel::ANYONE,
			'guild'			=> AccountLevel::ANYONE,
			'zeny'			=> AccountLevel::ANYONE,
			'death'			=> AccountLevel::ANYONE,
			'homun'			=> AccountLevel::ANYONE,
			'swordman'		=> AccountLevel::ANYONE,
			'bowman'		=> AccountLevel::ANYONE,
			'spearman'		=> AccountLevel::ANYONE,
			'mvp'       	=> AccountLevel::ANYONE,
		),
		'item'		=> array(
			'index'			=> AccountLevel::ANYONE,
			'view'			=> AccountLevel::ANYONE,
            'iteminfo'		=> AccountLevel::ADMIN
		),
		'monster'	=> array(
			'index'			=> AccountLevel::ANYONE,
			'view'			=> AccountLevel::ANYONE
		),
		'server'	=> array(
			'status'		=> AccountLevel::ANYONE,
			'status-xml'	=> AccountLevel::ANYONE,
			'info'			=> AccountLevel::ANYONE
		),
		'logdata'	=> array(
			'index'			=> AccountLevel::ADMIN,
			'char'			=> AccountLevel::ADMIN,
			'cashpoints'	=> AccountLevel::ADMIN,
			'feeding'		=> AccountLevel::ADMIN,
			'inter'			=> AccountLevel::ADMIN,
			'command'		=> AccountLevel::ADMIN,
			'branch'		=> AccountLevel::ADMIN,
			'chat'			=> AccountLevel::ADMIN,
			'login'			=> AccountLevel::ADMIN,
			'mvp'			=> AccountLevel::ADMIN,
			'npc'			=> AccountLevel::ADMIN,
			'pick'			=> AccountLevel::ADMIN,
			'zeny'			=> AccountLevel::ADMIN
		),
		'cplog'		=> array(
			'index'			=> AccountLevel::ADMIN,
			'create'		=> AccountLevel::ADMIN,
			'paypal'		=> AccountLevel::ADMIN,
			'login'			=> AccountLevel::ADMIN,
			'resetpass'		=> AccountLevel::ADMIN,
			'changepass'	=> AccountLevel::ADMIN,
			'changemail'	=> AccountLevel::ADMIN,
			'ban'			=> AccountLevel::ADMIN,
			'ipban'			=> AccountLevel::ADMIN,
			'txnview'		=> AccountLevel::ADMIN			
		),
		'ipban'		=> array(
			'index'			=> AccountLevel::ADMIN,
			'add'			=> AccountLevel::ADMIN,
			'unban'			=> AccountLevel::ADMIN,
			'edit'			=> AccountLevel::ADMIN,
			'remove'		=> AccountLevel::ADMIN
		),
		'service'	=> array(
			'tos'			=> AccountLevel::ANYONE
		),
		'captcha'	=> array(
			'index'			=> AccountLevel::ANYONE
		),
		'install'	=> array(
			'index'			=> AccountLevel::ANYONE,
			'reinstall'		=> AccountLevel::ADMIN
		),
		'test'		=> array(
			'*'				=> AccountLevel::ANYONE
		),
		'woe'		=> array(
			'index'			=> AccountLevel::ANYONE
		),
		'mail'		=> array(
			'index'			=> AccountLevel::ADMIN
		),
		'history'	=> array(
			'index'			=> AccountLevel::NORMAL,
			'cplogin'		=> AccountLevel::NORMAL,
			'gamelogin'		=> AccountLevel::NORMAL,
			'emailchange'	=> AccountLevel::NORMAL,
			'passchange'	=> AccountLevel::NORMAL,
			'passreset'		=> AccountLevel::NORMAL
		),
		'pages'		=> array(
			'index' 		=> AccountLevel::ADMIN,
			'add' 			=> AccountLevel::ADMIN,
			'delete' 		=> AccountLevel::ADMIN,
			'edit' 			=> AccountLevel::ADMIN,
			'content' 		=> AccountLevel::ANYONE,
		),
		'news'		=> array(
			'index' 		=>  AccountLevel::ANYONE,
			'view' 			=>  AccountLevel::ANYONE,
			'manage'		=>  AccountLevel::ADMIN,
			'add' 			=>  AccountLevel::ADMIN,
			'edit' 			=>  AccountLevel::ADMIN,
			'delete' 		=> AccountLevel::ADMIN,
		),
		'servicedesk'=> array(
			'index'			=> AccountLevel::NORMAL,
			'create'		=> AccountLevel::NORMAL,
			'view'			=> AccountLevel::NORMAL,
			'staffindex'	=> AccountLevel::LOWGM,
			'staffview'		=> AccountLevel::LOWGM,
			'staffviewclosed'=> AccountLevel::LOWGM,
			'staffsettings'	=> AccountLevel::LOWGM,
			'catcontrol'	=> AccountLevel::HIGHGM
		),
		'vending'		=> array(
			'index'			=> AccountLevel::ANYONE,
			'viewshop'		=> AccountLevel::ANYONE,
		),	
		'webcommands'	=> array(
			'index'			=> AccountLevel::ADMIN,
		),
	),
	// General feature permissions, handled by the modules themselves.
	'features' => array(
		'ViewAccount'		=> AccountLevel::HIGHGM, // View another person's account details.
		'ViewAccountBanLog'	=> AccountLevel::HIGHGM, // View another person's account ban log.
		'DeleteAccount'		=> AccountLevel::ADMIN,  // (not yet implemented)
		'DeleteCharacter'	=> AccountLevel::ADMIN,  // (not yet implemented)
		'SeeAccountPassword'	=> AccountLevel::NOONE,  // If not using MD5, view another person's password in list.
		'TempBanAccount'	=> AccountLevel::LOWGM,  // Has ability to temporarily ban an account.
		'TempUnbanAccount'	=> AccountLevel::LOWGM,  // Has ability to remove a temporary ban on an account.
		'PermBanAccount'	=> AccountLevel::HIGHGM, // Has ability to permanently ban an account.
		'PermUnbanAccount'	=> AccountLevel::HIGHGM, // Has ability to remove a permanent ban on an account.
		'SearchMD5Passwords'	=> AccountLevel::NOONE,  // Ability to search MD5'd passwords in list.
		'ViewCharacter'		=> AccountLevel::HIGHGM, // View another person's character details.
		'DivorceCharacter'	=> AccountLevel::LOWGM,  // Divorce another character.
		'AddShopItem'		=> AccountLevel::ADMIN,  // Ability to add an item to the shop.
		'EditShopItem'		=> AccountLevel::ADMIN,  // Ability to modify a shop item's details.
		'DeleteShopItem'     => AccountLevel::ADMIN,  // Ability to remove an item for sale on the shop.
		'ManageCashShop'     => AccountLevel::ADMIN,  // Ability to manage the in-game cash shop.
		'ViewGuild'          => AccountLevel::ADMIN,  // Ability to view another guild's details.
		'SearchWhosOnline'   => AccountLevel::ANYONE, // Ability to search the "Who's Online" page.
		'ViewOnlinePosition' => AccountLevel::LOWGM,  // Ability to see a character's current map on "Who's Online" page.
		'EditAccountGroupID' => AccountLevel::ADMIN,  // Ability to edit another person's account group ID.
		'EditAccountBalance' => AccountLevel::ADMIN,  // Ability to edit another person's account balance.
		'ModifyAccountPrefs' => AccountLevel::ADMIN,  // Ability to modify another person's account preferences.
		'ModifyCharPrefs'    => AccountLevel::ADMIN,  // Ability to modify another person's character preferences.
		'IgnoreHiddenPref'   => AccountLevel::LOWGM,  // Ability to see users on "Who's Online" page, hidden or not.
		'IgnoreHiddenPref2'  => AccountLevel::LOWGM,  // Ability to see users on "Who's Online" page, hidden by app config or not.
		'SeeHiddenMapStats'  => AccountLevel::LOWGM,  // Ability to see hidden map statistics.
		'ChangeSlot'         => AccountLevel::LOWGM,  // Minimum group level required to change another character's slot.
		'ModifyIpBan'        => AccountLevel::ADMIN,  // Minimum group level required to modify an existing IP ban.
		'RemoveIpBan'        => AccountLevel::ADMIN,  // Minimum group level required to remove an existing IP ban.
		'HideFromZenyRank'   => AccountLevel::NORMAL, // Ability to set "Hide from zeny ranking" pref.
		'SeeItemDbScripts'   => AccountLevel::ANYONE, // Ability to see item_db scripts in view page.
		'SeeItemDb2Scripts'  => AccountLevel::ADMIN,  // Ability to see item_db2 scripts in view page.
		'ViewRawTxnLogData'  => AccountLevel::ADMIN,  // Minimum group level required to view Raw Transaction Log in txnview page.
		'ResetLook'          => AccountLevel::LOWGM,  // Minimum group level required to reset another character's look.
		'ResetPosition'      => AccountLevel::LOWGM,  // Minimum group level required to reset another character's position.
		'ViewWoeDisallowed'  => AccountLevel::LOWGM,  // Minimum group level required to bypass WoE-disabled page security check.
		'SeeCpLoginLogPass'  => AccountLevel::NOONE,  // Minimum group level required to see password in CP login log (also requires CpLoginLogShowPassword in application.php)
		'SearchCpLoginLogPw' => AccountLevel::NOONE,  // Minimum group level required to search through passwords in the CP login log.
		'SeeCpResetPass'     => AccountLevel::NOONE,  // Minimum group level required to see passwords in CP log's "password resets" page.
		'SearchCpResetPass'  => AccountLevel::NOONE,  // Minimum group level required to search passwords in CP log's "password resets" page.
		'SeeCpChangePass'    => AccountLevel::NOONE,  // Minimum group level required to see passwords in CP log's "password changes" page.
		'SearchCpChangePass' => AccountLevel::NOONE,  // Minimum group level required to search passwords in CP log's "password changes" page.
		'SeeAccountID'       => AccountLevel::LOWGM,  // Minimum group level required to see Account ID on account view and character view pages.
		'SeeUnknownItems'    => AccountLevel::LOWGM,  // Minimum group level required to see unidentified items as identified.
		'AvoidSexChangeCost' => AccountLevel::LOWGM,  // Avoid paying cost (if any) for sex changes.
		
		'EditHigherPower'    => AccountLevel::NOONE,
		'BanHigherPower'     => AccountLevel::NOONE
	)
);
?>
