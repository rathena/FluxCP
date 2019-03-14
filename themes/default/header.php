<?php if (!defined('FLUX_ROOT')) exit; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php if (isset($metaRefresh)): ?>
		<meta http-equiv="refresh" content="<?php echo $metaRefresh['seconds'] ?>; URL=<?php echo $metaRefresh['location'] ?>" />
		<?php endif ?>
		<title><?php echo Flux::config('SiteTitle'); if (isset($title)) echo ": $title" ?></title>
        <link rel="icon" type="image/x-icon" href="./favicon.ico" />
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux.css') ?>" type="text/css" media="screen" title="" charset="utf-8" />
		<link href="<?php echo $this->themePath('css/flux/unitip.css') ?>" rel="stylesheet" type="text/css" media="screen" title="" charset="utf-8" />
		<?php if (Flux::config('EnableReCaptcha')): ?>
		<link href="<?php echo $this->themePath('css/flux/recaptcha.css') ?>" rel="stylesheet" type="text/css" media="screen" title="" charset="utf-8" />
		<?php endif ?>
		<!--[if IE]>
		<link rel="stylesheet" href="<?php echo $this->themePath('css/flux/ie.css') ?>" type="text/css" media="screen" title="" charset="utf-8" />
		<![endif]-->	
		<!--[if lt IE 9]>
		<script src="<?php echo $this->themePath('js/ie9.js') ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo $this->themePath('js/flux.unitpngfix.js') ?>"></script>
		<![endif]-->
		<script type="text/javascript" src="<?php echo $this->themePath('js/jquery-1.8.3.min.js') ?>"></script>
		<script type="text/javascript" src="<?php echo $this->themePath('js/flux.datefields.js') ?>"></script>
		<script type="text/javascript" src="<?php echo $this->themePath('js/flux.unitip.js') ?>"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				var inputs = 'input[type=text],input[type=password],input[type=file]';
				$(inputs).focus(function(){
					$(this).css({
						'background-color': '#f9f5e7',
						'border-color': '#dcd7c7',
						'color': '#726c58'
					});
				});
				$(inputs).blur(function(){
					$(this).css({
						'backgroundColor': '#ffffff',
						'borderColor': '#dddddd',
						'color': '#444444'
					}, 500);
				});
				$('.menuitem a').hover(
					function(){
						$(this).fadeTo(200, 0.85);
						$(this).css('cursor', 'pointer');
					},
					function(){
						$(this).fadeTo(150, 1.00);
						$(this).css('cursor', 'normal');
					}
				);
				$('.money-input').keyup(function() {
					var creditValue = parseInt($(this).val() / <?php echo Flux::config('CreditExchangeRate') ?>, 10);
					if (isNaN(creditValue))
						$('.credit-input').val('?');
					else
						$('.credit-input').val(creditValue);
				}).keyup();
				$('.credit-input').keyup(function() {
					var moneyValue = parseFloat($(this).val() * <?php echo Flux::config('CreditExchangeRate') ?>);
					if (isNaN(moneyValue))
						$('.money-input').val('?');
					else
						$('.money-input').val(moneyValue.toFixed(2));
				}).keyup();
				
				// In: js/flux.datefields.js
				processDateFields();
			});
			
			function reload(){
				window.location.href = '<?php echo $this->url ?>';
			}
		</script>
		
		<script type="text/javascript">
			function updatePreferredServer(sel){
				var preferred = sel.options[sel.selectedIndex].value;
				document.preferred_server_form.preferred_server.value = preferred;
				document.preferred_server_form.submit();
			}
			
			function updatePreferredTheme(sel){
				var preferred = sel.options[sel.selectedIndex].value;
				document.preferred_theme_form.preferred_theme.value = preferred;
				document.preferred_theme_form.submit();
			}

            function updatePreferredLanguage(sel){
                var preferred = sel.options[sel.selectedIndex].value;
                setCookie('language', preferred);
                reload();
            }

			// Preload spinner image.
			var spinner = new Image();
			spinner.src = '<?php echo $this->themePath('img/spinner.gif') ?>';
			
			function refreshSecurityCode(imgSelector){
				$(imgSelector).attr('src', spinner.src);
				
				// Load image, spinner will be active until loading is complete.
				var clean = <?php echo Flux::config('UseCleanUrls') ? 'true' : 'false' ?>;
				var image = new Image();
				image.src = "<?php echo $this->url('captcha') ?>"+(clean ? '?nocache=' : '&nocache=')+Math.random();
				
				$(imgSelector).attr('src', image.src);
			}
			function toggleSearchForm()
			{
				//$('.search-form').toggle();
				$('.search-form').slideToggle('fast');
			}

            function setCookie(key, value) {
                var expires = new Date();
                expires.setTime(expires.getTime() + expires.getTime()); // never expires
                document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
            }
		</script>
		
		<?php if (Flux::config('EnableReCaptcha')): ?>
			<script src='https://www.google.com/recaptcha/api.js'></script>
		<?php endif ?>
		
	</head>
	<body>
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<!-- Header -->
				<td bgcolor="#8ebceb" width="20"></td>
				<td bgcolor="#8ebceb" colspan="3">
					<a href="<?php echo $this->basePath ?>">
						<img src="<?php echo $this->themePath($session->account->group_level >= Flux::config('AdminMenuGroupLevel') ? 'img/logo_admin.gif' : 'img/logo.gif') ?>" id="logo" />
					</a>
				</td>
				<td bgcolor="#8ebceb" width="20"></td>
			</tr>
			<tr>
				<!-- Spacing between header and content -->
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td style="padding: 10px"></td>
				<td width="198">
					<!-- Sidebar -->
					<?php include $this->themePath('main/sidebar.php', true) ?>
				</td>
				<!-- Spacing between sidebar and content -->
				<td style="padding: 10px"></td>
				<td width="100%">
					<!-- Login box / User information -->
					<?php include $this->themePath('main/loginbox.php', true) ?>
					
					<!-- Content -->
					<table cellspacing="0" cellpadding="0" width="100%" id="content">
						<tr>
							<td width="18"><img src="<?php echo $this->themePath('img/content_tl.gif') ?>" style="display: block"  /></td>
							<td bgcolor="#f5f5f5"></td>
							<td width="18"><img src="<?php echo $this->themePath('img/content_tr.gif') ?>" style="display: block" /></td>
						</tr>
						
						<tr>
							<td bgcolor="#f5f5f5"></td>
							<td bgcolor="#f5f5f5">
								<?php if (Flux::config('DebugMode') && @gethostbyname(Flux::config('ServerAddress')) == '127.0.0.1'): ?>
									<p class="notice">Please change your <strong>ServerAddress</strong> directive in your application config to your server's real address (e.g., myserver.com).</p>
								<?php endif ?>
								
								<!-- Messages -->
								<?php if ($message=$session->getMessage()): ?>
									<p class="message"><?php echo htmlspecialchars($message) ?></p>
								<?php endif ?>
								
								<!-- Sub menu -->
								<?php include $this->themePath('main/submenu.php', true) ?>
								
								<!-- Page menu -->
								<?php include $this->themePath('main/pagemenu.php', true) ?>
								
								<!-- Credit balance -->
								<?php if (in_array($params->get('module'), array('donate', 'purchase'))) include $this->themePath('main/balance.php', true) ?>
