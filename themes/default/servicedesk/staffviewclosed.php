<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<h2><?php echo htmlspecialchars(Flux::message('SDHeader')) ?> - Staff Area</h2>
<h3><?php echo Flux::message('SDH3ClosedTickets') ?></h3>
<?php if($rowoutput): ?>
	<table class="horizontal-table" width="100%"> 
		<tbody>
		<tr>
			<th><?php  echo htmlspecialchars(Flux::message('SDHeaderID')) ?></th>
			<th><?php  echo htmlspecialchars(Flux::message('SDHeaderAccount')) ?></th>
			<th><?php  echo htmlspecialchars(Flux::message('SDHeaderSubject')) ?></th>    
			<th><?php  echo htmlspecialchars(Flux::message('SDHeaderCategory')) ?></th>    
			<th><?php  echo htmlspecialchars(Flux::message('SDHeaderStatus')) ?></th> 
			<th><?php  echo htmlspecialchars(Flux::message('SDHeaderLastAuthor')) ?></th>
			<th><?php  echo htmlspecialchars(Flux::message('SDHeaderTeam')) ?></th>
			<th><?php  echo htmlspecialchars(Flux::message('SDHeaderTimestamp')) ?></th>   
		</tr>
		<?php echo $rowoutput ?>
		</tbody>
	</table>
<?php else: ?>
	<p>
		<?php  echo htmlspecialchars(Flux::message('SDNoClosedTickets')) ?><br /><br />
	</p>
<?php endif ?>
