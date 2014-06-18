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
<table width="100%">
	<tr>
		<td width="50%">
			<table class="horizontal-table" width="100%">
				<tr>
					<th>Latest FluxCP Commit</th>
				</tr>
				<tr>
					<td>
						<?php echo $fcommessage ?> <br />
						<br />
						Author: <a href="<?php echo $fcomlogin ?>"><?php echo $fcomauthor ?></a> | Repo Link: <a href="<?php echo $frepourl ?>">FluxCP</a>
					</td>
				</tr>
			</table>
		</td>
		
		<td width="50%">
			<table class="vertical-table" width="100%">
				<tr>
					<th>Latest rAthena Commit</th>
				<tr>
					<td>
						<?php echo $rbcommessage ?> <br />
						<br />
						Author: <a href="<?php echo $rbcomlogin ?>"><?php echo $rbcomauthor ?></a> | Repo Link: <a href="<?php echo $rbrepourl ?>">rAthena</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

