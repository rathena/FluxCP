<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php
$adminMenuItems = $this->getAdminMenuItems();
$menuItems = $this->getMenuItems();
$_krModule = $params->get('module');
$_krAction = $params->get('action');
$isHomepage = ($_krModule == 'main' && (!$_krAction || $_krAction == 'index'));
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if (isset($metaRefresh)): ?>
		<meta http-equiv="refresh" content="<?php echo $metaRefresh['seconds'] ?>; URL=<?php echo $metaRefresh['location'] ?>" />
		<?php endif ?>
		<title><?php echo Flux::config('SiteTitle'); if (isset($title)) echo ": $title" ?></title>
		<link rel="icon" type="image/x-icon" href="./favicon.ico" />

		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com" />
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
		<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet" />

		<!-- Base FluxCP styles (inherited from default) -->
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux.css') ?>" type="text/css" media="screen" charset="utf-8" />
		<link href="<?php echo $this->themePath('css/flux/unitip.css') ?>" rel="stylesheet" type="text/css" media="screen" charset="utf-8" />
		<?php if (Flux::config('EnableReCaptcha')): ?>
		<link href="<?php echo $this->themePath('css/flux/recaptcha.css') ?>" rel="stylesheet" type="text/css" media="screen" charset="utf-8" />
		<?php endif ?>

		<!-- KunaiRO theme styles -->
		<link rel="stylesheet" href="<?php echo $this->themePath('css/kunairo.css') ?>" type="text/css" media="screen" charset="utf-8" />

		<!-- Scripts -->
		<script type="text/javascript" src="<?php echo $this->themePath('js/jquery-1.8.3.min.js') ?>"></script>
		<script type="text/javascript" src="<?php echo $this->themePath('js/flux.datefields.js') ?>"></script>
		<script type="text/javascript" src="<?php echo $this->themePath('js/flux.unitip.js') ?>"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				var inputs = 'input[type=text],input[type=password],input[type=file]';
				$(inputs).focus(function(){ $(this).addClass('kr-input--focused'); });
				$(inputs).blur(function(){ $(this).removeClass('kr-input--focused'); });
				$('.money-input').keyup(function() {
					var creditValue = parseInt($(this).val() / <?php echo Flux::config('CreditExchangeRate') ?>, 10);
					$('.credit-input').val(isNaN(creditValue) ? '?' : creditValue);
				}).keyup();
				$('.credit-input').keyup(function() {
					var moneyValue = parseFloat($(this).val() * <?php echo Flux::config('CreditExchangeRate') ?>);
					$('.money-input').val(isNaN(moneyValue) ? '?' : moneyValue.toFixed(2));
				}).keyup();
				processDateFields();

				// Mobile nav toggle
				$('.kr-nav-toggle').on('click', function() {
					$('.kr-navbar__nav').toggleClass('kr-navbar__nav--open');
					$(this).toggleClass('kr-nav-toggle--active');
				});

				// Mobile sidebar toggle (internal pages)
				$('.kr-sidebar-toggle').on('click', function() {
					$('.kr-sidebar').toggleClass('kr-sidebar--open');
				});
			});

			function reload(){ window.location.href = '<?php echo $this->url ?>'; }
			function updatePreferredServer(sel){
				document.preferred_server_form.preferred_server.value = sel.options[sel.selectedIndex].value;
				document.preferred_server_form.submit();
			}
			function updatePreferredTheme(sel){
				document.preferred_theme_form.preferred_theme.value = sel.options[sel.selectedIndex].value;
				document.preferred_theme_form.submit();
			}
			function updatePreferredLanguage(sel){
				setCookie('language', sel.options[sel.selectedIndex].value);
				reload();
			}
			var spinner = new Image();
			spinner.src = '<?php echo $this->themePath('img/spinner.gif') ?>';
			function refreshSecurityCode(imgSelector){
				$(imgSelector).attr('src', spinner.src);
				var clean = <?php echo Flux::config('UseCleanUrls') ? 'true' : 'false' ?>;
				var image = new Image();
				image.src = "<?php echo $this->url('captcha') ?>"+(clean ? '?nocache=' : '&nocache=')+Math.random();
				$(imgSelector).attr('src', image.src);
			}
			function toggleSearchForm(){ $('.search-form').slideToggle('fast'); }
			function setCookie(key, value) {
				var expires = new Date();
				expires.setTime(expires.getTime() + 31536000000);
				document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
			}
		</script>
		<?php if (Flux::config('EnableReCaptcha')): ?>
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<?php endif ?>
	</head>
	<body class="kr-body<?php if ($isHomepage) echo ' kr-body--home' ?>">

		<!-- ===== NAVBAR ===== -->
		<nav class="kr-navbar">
			<div class="kr-navbar__inner">
				<!-- Brand -->
				<a href="<?php echo $this->basePath ?>" class="kr-navbar__brand">
					<span class="kr-navbar__logo-text">Kunai</span><span class="kr-navbar__logo-accent">RO</span>
				</a>

				<!-- Horizontal nav links -->
				<div class="kr-navbar__nav">
					<a href="<?php echo $this->url('main') ?>" class="kr-navbar__link<?php if ($isHomepage) echo ' kr-navbar__link--active' ?>"><?php echo htmlspecialchars(Flux::message('HomeLabel')) ?></a>
					<a href="<?php echo $this->url('news') ?>" class="kr-navbar__link<?php if ($_krModule == 'news') echo ' kr-navbar__link--active' ?>"><?php echo htmlspecialchars(Flux::message('NewsLabel')) ?></a>
					<a href="<?php echo $this->url('ranking', 'character') ?>" class="kr-navbar__link<?php if ($_krModule == 'ranking') echo ' kr-navbar__link--active' ?>"><?php echo htmlspecialchars(Flux::message('RankingInfoLabel')) ?></a>
					<a href="<?php echo $this->url('server', 'status') ?>" class="kr-navbar__link<?php if ($_krModule == 'server') echo ' kr-navbar__link--active' ?>"><?php echo htmlspecialchars(Flux::message('ServerStatusLabel')) ?></a>
					<a href="<?php echo $this->url('woe') ?>" class="kr-navbar__link<?php if ($_krModule == 'woe') echo ' kr-navbar__link--active' ?>"><?php echo htmlspecialchars(Flux::message('WoeHoursLabel')) ?></a>
					<a href="<?php echo $this->url('item') ?>" class="kr-navbar__link<?php if ($_krModule == 'item') echo ' kr-navbar__link--active' ?>"><?php echo htmlspecialchars(Flux::message('DatabaseLabel')) ?></a>
				</div>

				<!-- Right actions -->
				<div class="kr-navbar__actions">
					<?php if ($session->isLoggedIn()): ?>
						<a href="<?php echo $this->url('account', 'view') ?>" class="kr-navbar__user-link"><?php echo htmlspecialchars($session->account->userid) ?></a>
						<a href="<?php echo $this->url('account', 'logout') ?>" class="kr-btn kr-btn--sm kr-btn--ghost" onclick="return confirm('Are you sure you want to logout?')"><?php echo htmlspecialchars(Flux::message('LogoutTitle')) ?></a>
					<?php else: ?>
						<a href="<?php echo $this->url('account', 'login') ?>" class="kr-btn kr-btn--sm kr-btn--ghost"><?php echo htmlspecialchars(Flux::message('LoginTitle')) ?></a>
						<a href="<?php echo $this->url('account', 'create') ?>" class="kr-btn kr-btn--sm kr-btn--primary"><?php echo htmlspecialchars(Flux::message('AccountCreateHeading')) ?></a>
					<?php endif ?>
					<!-- Mobile toggle -->
					<button class="kr-nav-toggle" aria-label="Menu">
						<span></span><span></span><span></span>
					</button>
				</div>
			</div>
		</nav>

		<!-- ===== LOGGED-IN INFO BAR ===== -->
		<?php if ($session->isLoggedIn()): ?>
		<div class="kr-userbar">
			<div class="kr-userbar__inner">
				<span class="kr-userbar__info">
					<?php echo htmlspecialchars($session->account->userid) ?> &mdash; <?php echo htmlspecialchars($session->serverName) ?>
					<?php $athenaServerNames = $session->getAthenaServerNames(); if (is_array($athenaServerNames) && count($athenaServerNames) > 1): ?>
					<select name="preferred_server" onchange="updatePreferredServer(this)" class="kr-select kr-select--inline">
						<?php foreach ($athenaServerNames as $serverName): ?>
						<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if (isset($server) && $server && $server->serverName == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
						<?php endforeach ?>
					</select>
					<?php endif ?>
				</span>
				<?php if (!empty($adminMenuItems) && Flux::config('AdminMenuNewStyle')): ?>
				<span class="kr-userbar__admin">
					<?php $mItems = array(); foreach ($adminMenuItems as $menuItem) $mItems[] = sprintf('<a href="%s" class="kr-userbar__admin-link">%s</a>', $menuItem['url'], htmlspecialchars(Flux::message($menuItem['name']))); echo implode(' ', $mItems) ?>
				</span>
				<?php endif ?>
				<form action="<?php echo $this->urlWithQs ?>" method="post" name="preferred_server_form" style="display: none">
					<input type="hidden" name="preferred_server" value="" />
				</form>
			</div>
		</div>
		<?php endif ?>

		<?php if ($isHomepage): ?>
		<!-- ===== HOMEPAGE: Full-width, no sidebar ===== -->
		<div class="kr-page kr-page--home">
			<main class="kr-main kr-main--full">
				<?php if ($message=$session->getMessage()): ?>
					<div class="kr-container"><div class="kr-alert kr-alert--info"><?php echo htmlspecialchars($message) ?></div></div>
				<?php endif ?>
		<?php else: ?>
		<!-- ===== INTERNAL PAGE: With sidebar ===== -->
		<div class="kr-page kr-page--internal">
			<button class="kr-sidebar-toggle" aria-label="Sidebar">
				<span></span><span></span><span></span>
			</button>
			<!-- Sidebar for internal pages -->
			<aside class="kr-sidebar">
				<?php if (!empty($adminMenuItems) && !Flux::config('AdminMenuNewStyle')): ?>
				<div class="kr-sidebar__section">
					<h3 class="kr-sidebar__heading">Admin</h3>
					<ul class="kr-sidebar__menu">
						<?php foreach ($adminMenuItems as $menuItem): ?>
						<li><a href="<?php echo $this->url($menuItem['module'], $menuItem['action']) ?>"<?php if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout') echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>><?php echo htmlspecialchars(Flux::message($menuItem['name'])) ?></a></li>
						<?php endforeach ?>
					</ul>
				</div>
				<?php endif ?>
				<?php if (!empty($menuItems)): ?>
				<?php foreach ($menuItems as $menuCategory => $menus): ?>
				<?php if (!empty($menus)): ?>
				<div class="kr-sidebar__section">
					<h3 class="kr-sidebar__heading"><?php echo htmlspecialchars(Flux::message($menuCategory)) ?></h3>
					<ul class="kr-sidebar__menu">
						<?php foreach ($menus as $menuItem): ?>
						<li><a href="<?php echo $menuItem['url'] ?>"<?php if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout') echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>><?php echo htmlspecialchars(Flux::message($menuItem['name'])) ?></a></li>
						<?php endforeach ?>
					</ul>
				</div>
				<?php endif ?>
				<?php endforeach ?>
				<?php endif ?>
			</aside>

			<main class="kr-main kr-main--with-sidebar">
				<?php if (Flux::config('DebugMode') && @gethostbyname(Flux::config('ServerAddress')) == '127.0.0.1'): ?>
					<div class="kr-alert kr-alert--warning">Please change your <strong>ServerAddress</strong> directive in your application config to your server's real address.</div>
				<?php endif ?>

				<?php if ($message=$session->getMessage()): ?>
					<div class="kr-alert kr-alert--info"><?php echo htmlspecialchars($message) ?></div>
				<?php endif ?>

				<?php
				$subMenuItems = $this->getSubMenuItems();
				// Map config submenu names to translatable keys
				$_krSubMap = array(
					// Account
					'Login'                      => 'KunaiROSubLogin',
					'Register'                   => 'KunaiROSubRegister',
					'Reset Password'             => 'KunaiROSubResetPass',
					'Resend E-mail Confirmation' => 'KunaiROSubResend',
					'Change Password'            => 'KunaiROSubChangePass',
					'Change E-mail'              => 'KunaiROSubChangeMail',
					'Change Gender'              => 'KunaiROSubChangeGender',
					'View Account'               => 'KunaiROSubViewAccount',
					'Transfer Credits'           => 'KunaiROSubTransfer',
					'Credit Transfer History'    => 'KunaiROSubXferLog',
					'Go to Shopping Cart'        => 'KunaiROSubCart',
					'List Accounts'              => 'KunaiROSubListAccounts',
					// Purchase
					'Shop'                       => 'KunaiROSubShop',
					'Go to Cart'                 => 'KunaiROSubGoToCart',
					'Checkout'                   => 'KunaiROSubCheckout',
					'Empty Cart'                 => 'KunaiROSubEmptyCart',
					'Pending Redemption'         => 'KunaiROSubPending',
					// Donate
					'Make a Donation'            => 'KunaiROSubDonate',
					'Donation History'           => 'KunaiROSubDonateHistory',
					'Trusted PayPal E-mails'     => 'KunaiROSubTrustedPaypal',
				);
				?>
				<?php if (!empty($subMenuItems)): ?>
				<nav class="kr-submenu">
					<?php $menus = array(); foreach ($subMenuItems as $menuItem): ?>
					<?php
						$_krSubName = $menuItem['name'];
						if (isset($_krSubMap[$_krSubName])) {
							$_krSubName = Flux::message($_krSubMap[$_krSubName]);
						}
					?>
					<?php $menus[] = sprintf('<a href="%s" class="kr-submenu__item%s">%s</a>',
						$this->url($menuItem['module'], $menuItem['action']),
						$_krModule == $menuItem['module'] && $_krAction == $menuItem['action'] ? ' kr-submenu__item--active' : '',
						htmlspecialchars(trim($_krSubName))) ?>
					<?php endforeach ?>
					<?php echo implode('', $menus) ?>
				</nav>
				<?php endif ?>

				<?php if (!empty($pageMenuItems)): ?>
				<nav class="kr-pagemenu">
					<span class="kr-pagemenu__label"><?php echo empty($title) ? 'Actions' : htmlspecialchars($title) ?>:</span>
					<?php foreach ($pageMenuItems as $menuItemName => $menuItemLink): ?>
					<a href="<?php echo $menuItemLink ?>" class="kr-pagemenu__item"><?php echo htmlspecialchars($menuItemName) ?></a>
					<?php endforeach ?>
				</nav>
				<?php endif ?>

				<?php if (in_array($params->get('module'), array('donate', 'purchase'))): ?>
				<div class="kr-balance">
					<span class="kr-balance__label">Donation Credits</span>
					<span class="kr-balance__amount"><?php echo number_format((int)$session->account->balance) ?></span>
				</div>
				<?php endif ?>
		<?php endif ?>
