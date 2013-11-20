<?php
/* Tasklist Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.04
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<h2><?php echo htmlspecialchars(Flux::message('TaskListHeader')) ?></h2>

<?php if($tasklist): ?>
	<table class="horizontal-table" width="100%"> 
		<tbody>
		<tr>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderTasks')) ?></th>
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderOwner')) ?></th>    
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderPriority')) ?></th>    
			<th><?php  echo htmlspecialchars(Flux::message('TLHeaderStatus')) ?></th>    
			<th style="width:85px;"><?php  echo htmlspecialchars(Flux::message('TLHeaderCreated'))?> </th>    
		</tr>
		<?php foreach($tasklist as $trow):?>
			<tr >
				<td><a href="<?php echo $this->url('tasks', 'viewtasks', array('task' => $trow->id)) ?>" ><?php echo $trow->title?></a></td>
				<td><?php if($trow->assigned=='0'): ?>
						<span class="not-applicable"> <?php echo htmlspecialchars(Flux::message('TLNotAssigned')) ?></span>
					<?php else: ?>
						<?php echo $trow->assigned?>
					<?php endif ?>
				</td>
				<td><?php if($trow->priority==1):?>
						<font color="red"><?php echo htmlspecialchars(Flux::message('TLPriority'.$trow->priority.'')) ?></font>
						<?php elseif($trow->priority==2):?>
						<font color="orange"><?php echo htmlspecialchars(Flux::message('TLPriority'.$trow->priority.'')) ?></font>
						<?php elseif($trow->priority==3):?>
						<?php echo htmlspecialchars(Flux::message('TLPriority'.$trow->priority)) ?>
						<?php endif ?></td>
				<td><?php echo htmlspecialchars(Flux::message('TLStatus'.$trow->status)) ?></td>
				<td><?php echo date(Flux::config('DateFormat'),strtotime($trow->created))?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php else: ?>
	<p>
		<?php echo htmlspecialchars(Flux::message('TLNoMine')) ?><br/><br/>
		<a href="<?php echo $this->url('tasks', 'createnew') ?>">Create a Task</a>
	</p>
<?php endif ?>