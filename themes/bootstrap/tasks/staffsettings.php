<?php
/* Tasklist Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.04
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<h2>Staff Settings</h2>
<h3>Current Staff Settings</h3>
<?php if($stafflist): ?>
	<table class="horizontal-table" width="100%"> 
		<tbody>
		<tr>
			<th>Account Name</th>
			<th>Preferred Name</th>
			<th>Enable Emails</th>
			<th>Options</th>
		</tr>
		<?php foreach($stafflist as $trow):?>
			<tr >
				<td><?php echo $trow->account_name?></td>
				<td><?php echo $trow->preferred_name?></td>
				<td>
					<?php if($trow->emailalerts=='0'): ?>No<?php elseif($trow->emailalerts=='1'): ?>Yes<?php endif ?></td>
				<td>
				<a href="<?php echo $this->url('tasks', 'staffsettings', array('option' => 'delete', 'staffid' => $trow->account_id))?>" >Delete</a></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
<?php else: ?>
	<p>
		There are no current staff settings<br/><br/>
	</p>
<?php endif ?>
<br />
<h3>Add Staff Settings</h3>
<form action="<?php echo $this->urlWithQs ?>" method="post">
	<table class="horizontal-table" width="100%">
		<tr>
			<th>Account Name</th>
			<th>Preferred Name</th>
			<th>Enable Emails</th>
		</tr>
		<tr>
			<td><input type="text" name="account_name" /></td>
			<td><input type="text" name="preferred_name" /></td>
			<td><input type="checkbox" name="emailalerts" value="1" /></td>
		</tr>
		<tr>
			<td colspan="3">
			<input type="hidden" name="account_id" value="<?php echo $session->account->account_id ?>" />
			<input type="submit" value="Add Staff" /></td>
		</tr>
    </table>
</form>
<br />
<h3>Please Note</h3>
<p>While the above form suggests emails can be sent, it will be implemented in a future version. Ticking the box will do nothing, however if/when you upgrade, staff settings won't need to be changed.</p>