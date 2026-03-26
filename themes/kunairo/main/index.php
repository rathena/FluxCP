<?php if (!defined('FLUX_ROOT')) exit; ?>

<!-- ===== HERO SECTION ===== -->
<section class="kr-hero">
	<div class="kr-hero__bg">
		<div class="kr-hero__bg-deep"></div>
		<div class="kr-hero__bg-fire"></div>
		<div class="kr-hero__bg-energy"></div>
		<div class="kr-hero__bg-smoke"></div>
		<div class="kr-hero__bg-sparks"></div>
		<div class="kr-hero__bg-radial"></div>
		<div class="kr-hero__bg-scanlines"></div>
		<div class="kr-hero__bg-vignette"></div>
	</div>

	<div class="kr-hero__content">
		<div class="kr-hero__badges">
			<span class="kr-badge kr-badge--fire"><?php echo htmlspecialchars(Flux::message('KunaiROBadgePreRenewal')) ?></span>
			<span class="kr-badge kr-badge--gold"><?php echo htmlspecialchars(Flux::message('KunaiROBadgeRates')) ?></span>
			<span class="kr-badge kr-badge--steel"><?php echo htmlspecialchars(Flux::message('KunaiROBadgeNoPTW')) ?></span>
		</div>

		<h1 class="kr-hero__title">
			<span class="kr-hero__title-kunai">Kunai</span><span class="kr-hero__title-ro">RO</span>
		</h1>

		<p class="kr-hero__tagline"><?php echo htmlspecialchars(Flux::message('KunaiROHeroTagline')) ?></p>

		<div class="kr-hero__divider">
			<span class="kr-hero__divider-wing kr-hero__divider-wing--left"></span>
			<span class="kr-hero__divider-diamond"></span>
			<span class="kr-hero__divider-wing kr-hero__divider-wing--right"></span>
		</div>

		<p class="kr-hero__description"><?php echo htmlspecialchars(Flux::message('KunaiROHeroDescription')) ?></p>

		<div class="kr-hero__actions">
			<a href="<?php echo $this->url('account', 'create') ?>" class="kr-btn kr-btn--primary kr-btn--xl kr-btn--glow">
				<?php echo htmlspecialchars(Flux::message('KunaiROBtnPlay')) ?>
			</a>
			<a href="<?php echo $this->url('account', 'create') ?>" class="kr-btn kr-btn--secondary kr-btn--lg">
				<?php echo htmlspecialchars(Flux::message('KunaiROBtnRegister')) ?>
			</a>
			<a href="#" class="kr-btn kr-btn--discord kr-btn--lg">
				<svg class="kr-btn__icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/></svg>
				<?php echo htmlspecialchars(Flux::message('KunaiROBtnDiscord')) ?>
			</a>
		</div>
	</div>

	<div class="kr-hero__fade"></div>
</section>

<!-- ===== STATS BAR (real data) ===== -->
<?php
$_krPlayers = 0;
$_krIsOnline = false;
$_krIsWoe = false;
try {
	foreach (Flux::$loginAthenaGroupRegistry as $_g) {
		$_krIsOnline = $_g->loginServer->isUp();
		foreach ($_g->athenaServers as $_a) {
			$_s = $_g->connection->getStatement("SELECT COUNT(char_id) AS c FROM {$_a->charMapDatabase}.`char` WHERE `online`>'0'");
			$_s->execute();
			$_r = $_s->fetch();
			if ($_r) $_krPlayers += intval($_r->c);
			if ($_a->isWoe()) $_krIsWoe = true;
		}
	}
} catch (Exception $e) {}
?>
<section class="kr-stats">
	<div class="kr-container">
		<div class="kr-stats__grid">
			<div class="kr-stats__item">
				<span class="kr-stats__icon">&#9876;</span>
				<div class="kr-stats__data">
					<span class="kr-stats__value"><?php echo number_format($_krPlayers) ?></span>
					<span class="kr-stats__label"><?php echo htmlspecialchars(Flux::message('KunaiROStatsPlayersLabel')) ?></span>
				</div>
			</div>
			<div class="kr-stats__item kr-stats__item--woe">
				<span class="kr-stats__icon">&#9760;</span>
				<div class="kr-stats__data">
					<span class="kr-stats__value"><?php echo $_krIsWoe ? htmlspecialchars(Flux::message('KunaiROStatsWoEActive')) : htmlspecialchars(Flux::message('KunaiROStatsWoEInactive')) ?></span>
					<span class="kr-stats__label"><?php echo htmlspecialchars(Flux::message('KunaiROStatsWoELabel')) ?></span>
				</div>
			</div>
			<div class="kr-stats__item">
				<span class="kr-stats__icon">&#9889;</span>
				<div class="kr-stats__data">
					<span class="kr-stats__value"><?php echo $_krIsOnline ? htmlspecialchars(Flux::message('KunaiROStatsOnline')) : htmlspecialchars(Flux::message('KunaiROStatsOffline')) ?></span>
					<span class="kr-stats__label"><?php echo htmlspecialchars(Flux::message('KunaiROStatsUptimeLabel')) ?></span>
				</div>
			</div>
			<div class="kr-stats__item">
				<span class="kr-stats__icon">&#9733;</span>
				<div class="kr-stats__data">
					<span class="kr-stats__value"><?php echo htmlspecialchars(Flux::message('KunaiROStatsRatesValue')) ?></span>
					<span class="kr-stats__label"><?php echo htmlspecialchars(Flux::message('KunaiROStatsRatesLabel')) ?></span>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ===== WHY KUNAIRO ===== -->
<section class="kr-section kr-section--why">
	<div class="kr-container">
		<h2 class="kr-section__heading">
			<span class="kr-section__heading-icon">&#10022;</span>
			<?php echo htmlspecialchars(Flux::message('KunaiROWhyHeading')) ?>
		</h2>
		<div class="kr-why__grid">
			<div class="kr-why__card">
				<div class="kr-why__icon-wrap"><span class="kr-why__icon">&#9878;</span></div>
				<h3 class="kr-why__title"><?php echo htmlspecialchars(Flux::message('KunaiROWhyBalancedTitle')) ?></h3>
				<p class="kr-why__desc"><?php echo htmlspecialchars(Flux::message('KunaiROWhyBalancedDesc')) ?></p>
			</div>
			<div class="kr-why__card">
				<div class="kr-why__icon-wrap kr-why__icon-wrap--red"><span class="kr-why__icon">&#10006;</span></div>
				<h3 class="kr-why__title"><?php echo htmlspecialchars(Flux::message('KunaiROWhyNoPTWTitle')) ?></h3>
				<p class="kr-why__desc"><?php echo htmlspecialchars(Flux::message('KunaiROWhyNoPTWDesc')) ?></p>
			</div>
			<div class="kr-why__card">
				<div class="kr-why__icon-wrap kr-why__icon-wrap--orange"><span class="kr-why__icon">&#9830;</span></div>
				<h3 class="kr-why__title"><?php echo htmlspecialchars(Flux::message('KunaiROWhyEconomyTitle')) ?></h3>
				<p class="kr-why__desc"><?php echo htmlspecialchars(Flux::message('KunaiROWhyEconomyDesc')) ?></p>
			</div>
			<div class="kr-why__card">
				<div class="kr-why__icon-wrap kr-why__icon-wrap--fire"><span class="kr-why__icon">&#9876;</span></div>
				<h3 class="kr-why__title"><?php echo htmlspecialchars(Flux::message('KunaiROWhyCompetitionTitle')) ?></h3>
				<p class="kr-why__desc"><?php echo htmlspecialchars(Flux::message('KunaiROWhyCompetitionDesc')) ?></p>
			</div>
		</div>
	</div>
</section>

<!-- ===== NEWS SECTION ===== -->
<section class="kr-section kr-section--news">
	<div class="kr-container">
<?php if(Flux::config('CMSNewsOnHomepage')): ?>
		<h2 class="kr-section__heading">
			<span class="kr-section__heading-icon">&#9998;</span>
			<?php echo htmlspecialchars(Flux::message('KunaiRONewsHeading')) ?>
		</h2>

	<?php if($newstype == '1'):?>
		<?php if($news): ?>
			<?php $newsIndex = 0; foreach($news as $nrow): $newsIndex++; ?>
				<?php if ($newsIndex === 1): ?>
				<!-- Featured news -->
				<article class="kr-featured">
					<div class="kr-featured__accent"></div>
					<div class="kr-featured__inner">
						<div class="kr-featured__badge"><?php echo htmlspecialchars(Flux::message('KunaiROBadgePreRenewal')) ?></div>
						<h3 class="kr-featured__title"><?php echo $nrow->title ?></h3>
						<span class="kr-featured__meta">
							<?php echo $nrow->author ?> &middot; <?php echo date(Flux::config('DateFormat'),strtotime($nrow->created))?>
						</span>
						<div class="kr-featured__body">
							<?php echo $nrow->body ?>
						</div>
						<?php if($nrow->link): ?>
						<a href="<?php echo $nrow->link ?>" class="kr-btn kr-btn--secondary kr-btn--sm"><?php echo htmlspecialchars(Flux::message('CMSNewsLink')) ?> &rarr;</a>
						<?php endif; ?>
					</div>
				</article>
				<?php else: ?>
				<?php if ($newsIndex === 2): ?><div class="kr-news-grid"><?php endif; ?>
				<article class="kr-card">
					<div class="kr-card__accent"></div>
					<div class="kr-card__inner">
						<div class="kr-card__header">
							<h3 class="kr-card__title"><?php echo $nrow->title ?></h3>
							<span class="kr-card__meta">
								<?php echo $nrow->author ?> &middot; <?php echo date(Flux::config('DateFormat'),strtotime($nrow->created))?>
							</span>
						</div>
						<div class="kr-card__body"><?php echo $nrow->body ?></div>
						<?php if($nrow->link): ?>
						<a href="<?php echo $nrow->link ?>" class="kr-card__link"><?php echo htmlspecialchars(Flux::message('CMSNewsLink')) ?> &rarr;</a>
						<?php endif; ?>
					</div>
				</article>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ($newsIndex > 1): ?></div><?php endif; ?>
		<?php else: ?>
			<div class="kr-empty">
				<span class="kr-empty__icon">&#9998;</span>
				<p class="kr-empty__text"><?php echo htmlspecialchars(Flux::message('KunaiRONewsEmpty')) ?></p>
			</div>
		<?php endif ?>

	<?php elseif($newstype == '2'):?>
		<?php if(isset($xml) && isset($xml->channel)): ?>
			<?php $i = 0; foreach($xml->channel->item as $rssItem): ?>
				<?php $i++; if($i <= $newslimit): ?>
				<?php if ($i === 1): ?>
				<article class="kr-featured">
					<div class="kr-featured__accent"></div>
					<div class="kr-featured__inner">
						<h3 class="kr-featured__title"><?php echo $rssItem->title ?></h3>
						<span class="kr-featured__meta"><?php echo date(Flux::config('DateFormat'),strtotime($rssItem->pubDate))?></span>
						<div class="kr-featured__body"><?php echo $rssItem->description ?></div>
						<a href="<?php echo $rssItem->link ?>" class="kr-btn kr-btn--secondary kr-btn--sm"><?php echo htmlspecialchars(Flux::message('CMSNewsLink')) ?> &rarr;</a>
					</div>
				</article>
				<?php else: ?>
				<?php if ($i === 2): ?><div class="kr-news-grid"><?php endif; ?>
				<article class="kr-card">
					<div class="kr-card__accent"></div>
					<div class="kr-card__inner">
						<div class="kr-card__header">
							<h3 class="kr-card__title"><?php echo $rssItem->title ?></h3>
							<span class="kr-card__meta"><?php echo date(Flux::config('DateFormat'),strtotime($rssItem->pubDate))?></span>
						</div>
						<div class="kr-card__body"><?php echo $rssItem->description ?></div>
						<a href="<?php echo $rssItem->link ?>" class="kr-card__link"><?php echo htmlspecialchars(Flux::message('CMSNewsLink')) ?> &rarr;</a>
					</div>
				</article>
				<?php endif; ?>
				<?php endif ?>
			<?php endforeach; ?>
			<?php if ($i > 1): ?></div><?php endif; ?>
		<?php else: ?>
			<div class="kr-empty">
				<span class="kr-empty__icon">&#9998;</span>
				<p class="kr-empty__text"><?php echo htmlspecialchars(Flux::message('KunaiRONewsEmpty')) ?></p>
			</div>
		<?php endif ?>
	<?php endif ?>

<?php else: ?>
	<h2 class="kr-section__heading"><?php echo htmlspecialchars(Flux::message('MainPageHeading')) ?></h2>
	<p><strong><?php echo htmlspecialchars(Flux::message('MainPageInfo')) ?></strong></p>
	<p><?php echo htmlspecialchars(Flux::message('MainPageInfo2')) ?></p>
	<ol>
		<li><p><?php echo htmlspecialchars(sprintf(Flux::message('MainPageStep1'), __FILE__)) ?></p></li>
		<li><p><?php echo htmlspecialchars(Flux::message('MainPageStep2')) ?></p></li>
	</ol>
<?php endif ?>
	</div>
</section>
