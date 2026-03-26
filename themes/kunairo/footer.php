<?php if (!defined('FLUX_ROOT')) exit; ?>
			</main>
		</div>
		<!-- end .kr-page -->

		<!-- ===== FOOTER ===== -->
		<footer class="kr-footer">
			<div class="kr-footer__border"></div>
			<div class="kr-footer__bg"></div>
			<div class="kr-footer__inner">
				<!-- Brand column -->
				<div class="kr-footer__col kr-footer__col--brand">
					<div class="kr-footer__logo-wrap">
						<span class="kr-footer__logo">Kunai<span class="kr-footer__logo-accent">RO</span></span>
					</div>
					<span class="kr-footer__tagline"><?php echo htmlspecialchars(Flux::message('KunaiROFooterTagline')) ?></span>
				</div>

				<!-- Nav column -->
				<div class="kr-footer__col">
					<h4 class="kr-footer__col-title"><?php echo htmlspecialchars(Flux::message('InformationLabel')) ?></h4>
					<div class="kr-footer__nav">
						<a href="<?php echo $this->url('server', 'status') ?>"><?php echo htmlspecialchars(Flux::message('ServerStatusLabel')) ?></a>
						<a href="<?php echo $this->url('woe') ?>"><?php echo htmlspecialchars(Flux::message('WoeHoursLabel')) ?></a>
						<a href="<?php echo $this->url('ranking', 'character') ?>"><?php echo htmlspecialchars(Flux::message('RankingInfoLabel')) ?></a>
						<a href="<?php echo $this->url('news') ?>"><?php echo htmlspecialchars(Flux::message('NewsLabel')) ?></a>
					</div>
				</div>

				<!-- Account column -->
				<div class="kr-footer__col">
					<h4 class="kr-footer__col-title"><?php echo htmlspecialchars(Flux::message('AccountLabel')) ?></h4>
					<div class="kr-footer__nav">
						<a href="<?php echo $this->url('account', 'create') ?>"><?php echo htmlspecialchars(Flux::message('AccountCreateHeading')) ?></a>
						<a href="<?php echo $this->url('account', 'login') ?>"><?php echo htmlspecialchars(Flux::message('LoginTitle')) ?></a>
						<a href="<?php echo $this->url('item') ?>"><?php echo htmlspecialchars(Flux::message('DatabaseLabel')) ?></a>
					</div>
				</div>

				<!-- Settings column -->
				<div class="kr-footer__col">
					<div class="kr-footer__selectors">
						<?php if (count(Flux::$appConfig->get('ThemeName', false)) > 1): ?>
						<div class="kr-footer__selector">
							<label>Theme</label>
							<select name="preferred_theme" onchange="updatePreferredTheme(this)" class="kr-select kr-select--sm">
								<?php foreach (Flux::$appConfig->get('ThemeName', false) as $themeName): ?>
								<option value="<?php echo htmlspecialchars($themeName) ?>"<?php if ($session->theme == $themeName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($themeName) ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<?php endif ?>
						<?php if (count(Flux::getAvailableLanguages()) > 1): ?>
						<div class="kr-footer__selector">
							<label><?php echo htmlspecialchars(Flux::message('KunaiROLabelLanguage')) ?></label>
							<select name="preferred_language" onchange="updatePreferredLanguage(this)" class="kr-select kr-select--sm">
								<?php foreach (Flux::getAvailableLanguages() as $lang_key => $lang): ?>
								<option value="<?php echo htmlspecialchars($lang_key) ?>"<?php if (!empty($_COOKIE['language']) && $_COOKIE['language'] == $lang_key) echo ' selected="selected"' ?>><?php echo htmlspecialchars($lang) ?></option>
								<?php endforeach ?>
							</select>
						</div>
						<?php endif ?>
					</div>
				</div>
			</div>

			<!-- Bottom bar -->
			<div class="kr-footer__bottom">
				<div class="kr-footer__bottom-inner">
					<?php if (Flux::config('ShowCopyright')): ?>
					<span class="kr-footer__copyright">
						&copy; Xponzy Network &mdash; <?php echo htmlspecialchars(Flux::message('KunaiROFooterCopyright')) ?>
					</span>
					<?php endif ?>
					<?php if (Flux::config('ShowRenderDetails')): ?>
					<span class="kr-footer__meta">
						<?php echo round(microtime(true) - __START__, 5) ?>s &middot; <?php echo (int)Flux::$numberOfQueries ?> queries
						<?php if (Flux::config('GzipCompressOutput')): ?>&middot; gzip<?php endif ?>
					</span>
					<?php endif ?>
				</div>
			</div>

			<form action="<?php echo $this->urlWithQs ?>" method="post" name="preferred_theme_form" style="display: none">
				<input type="hidden" name="preferred_theme" value="" />
			</form>
		</footer>
	</body>
</html>
