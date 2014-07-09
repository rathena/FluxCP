<?php
/* Tasklist Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.04
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<?php if($tasklist): ?>
<h2><?php echo htmlspecialchars($trow->title) ?></h2>
	<table class="vertical-table" width="70%"> 
		<tbody>
		<tr>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderTasks')) ?></th>
				<td><?php echo htmlspecialchars($trow->title) ?></td>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderStatus')) ?></th>
				<td><?php echo htmlspecialchars(Flux::message('TLStatus'.$trow->status)) ?> <?php if($disps) echo $disps ?></td>
		</tr>
		<tr>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderPriority')) ?></th>
			<td><?php if($trow->priority==1):?>
						<font color="red"><?php echo htmlspecialchars(Flux::message('TLPriority'.$trow->priority.'')) ?></font>
						<?php elseif($trow->priority==2):?>
						<font color="orange"><?php echo htmlspecialchars(Flux::message('TLPriority'.$trow->priority.'')) ?></font>
						<?php elseif($trow->priority==3):?>
						<?php echo htmlspecialchars(Flux::message('TLPriority'.$trow->priority)) ?>
						<?php endif ?></td>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderOwner')) ?></th>
			<td>
			<?php if($trow->assigned=='0' || $trow->assigned!=$staffsess->preferred_name): ?>
						<span class="not-applicable"> <?php echo htmlspecialchars(Flux::message('TLNotAssigned')) ?> <?php echo $assignedlink ?></span>
					<?php elseif($trow->assigned==$staffsess->preferred_name): ?>
						<span><?php echo $trow->assigned?> <?php echo $assignedlink ?></span>
					<?php else: ?>
						<?php echo $trow->assigned?>
					<?php endif ?></td>
		</tr>
		<tr>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderCreated')) ?></th>
				<td><?php echo htmlspecialchars($trow->created) ?></td>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderModified')) ?></th>
				<td><?php echo htmlspecialchars($trow->modified) ?></td>
		</tr>
		<tr>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderResources')) ?></th>
			<td colspan="3"><?php echo $resources ?></td></tr>
		</tr>
		<tr>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderBody')) ?><br />&nbsp;<br />&nbsp;<br />&nbsp;</th>
			<td colspan="3"><?php echo $trow->body ?></td></tr>
		</tr>
		<tr>
			<td colspan="4">
				<?php if($trow->status==5): ?>
					This Task has been marked as completed. Would you like to set the status back to <strong>New Task</strong>?<br />
					<a href="<?php echo $this->url('tasks', 'viewtasks', array('update' => $taskID)) ?>">Yes, Lets update it!</a>
				<?php elseif($trow->status==2): ?>
					This Task is set to <strong>Awaiting Implementation</strong>. Would you like to set the status to <strong>Completed</strong>?<br />
					<a href="<?php echo $this->url('tasks', 'viewtasks', array('update' => $taskID)) ?>">Yes, Lets update it!</a>
				<?php elseif($trow->status==1): ?>
					This Task has been marked as <strong>In-Progress</strong>. Would you like to set the status to <strong>Awaiting Implementation</strong>?<br />
					<a href="<?php echo $this->url('tasks', 'viewtasks', array('update' => $taskID)) ?>">Yes, Lets update it!</a>
				<?php elseif($trow->status==0): ?>
					This Task has been marked as new. Would you like to set the status to <strong>In-Progress</strong>?<br />
					<a href="<?php echo $this->url('tasks', 'viewtasks', array('update' => $taskID)) ?>">Yes, Lets update it!</a>
				<?php endif ?>   
			</td>
		</tr>
		</tbody>
	</table>
<?php else: ?>
	<p>
		<?php echo htmlspecialchars(Flux::message('TLHuh')) ?>
	</p>
<?php endif ?>