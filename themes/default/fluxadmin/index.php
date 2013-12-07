<?php
/* FluxAdmin
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<h2><?php echo htmlspecialchars(Flux::message('FAHeader')) ?></h2>
<p>Welcome to FluxAdmin, <?php echo $session->account->userid ?>. FluxAdmin is a tool to help you administrate your FluxCP installation from wherever you are.</p>
<br />
<h3>FluxCP Information</h3>
<table width="100%">
<tr><td width="33%">
	<table class="horizontal-table" width="100%">
	<tr><th colspan="2">Version Info</th></tr>
	<tr><td>Latest Version</td><td>version</td></tr>
	<tr><td>Current Version</td><td>version</td></tr>
</table>
	</td><td width="33%">
	<table class="horizontal-table" width="100%">
		<tr>
			<th>Latest Commit</th>
		</tr>
		<tr>
			<td><?php echo $fluxadminf->fcommessage ?> <br />
			<br />
			Author: <a href="<?php echo $fluxadminf->fcomuserlogin ?>"><?php echo $fluxadminf->fcomauthor ?></a> | Repo Link: <a href="<?php echo $fluxadminf->fcomurl ?>">FluxCP</a>
			</td>
		</tr>
	</table>
</div>
</td><td width="33%">
	<table class="vertical-table" width="100%">
		<tr>
			<th>Pull Requests</th>
		</tr>
		<?php echo $fpulldisplay ?>
	</table>
</td></tr></table>
<br />

<h3>rAthena Information</h3>
<table width="100%">
<tr><td width="33%">
	<table class="horizontal-table" width="100%">
	<tr><th colspan="2">Version Info</th></tr>
	<tr><td>Latest Version</td><td>version</td></tr>
	<tr><td>Current Version</td><td>version</td></tr>
</table>
	</td><td width="33%">
	<table class="horizontal-table" width="100%">
		<tr>
			<th>Latest Commit</th>
		</tr>
		<?php echo $rdisplaycommit ?>
	</table>
</div>
</td><td width="33%">
	<table class="vertical-table" width="100%">
		<tr>
			<th>Pull Requests</th>
		</tr>
		<?php echo $rpulldisplay ?>
	</table>
</td></tr></table>
