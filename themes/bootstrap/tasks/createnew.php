<?php
/* Tasklist Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.04
 */
 
if (!defined('FLUX_ROOT')) exit;

?>
<h2><?php echo htmlspecialchars(Flux::message('TaskListAdd')) ?></h2>
<form action="<?php echo $this->urlWithQs ?>" method="post">
	<table class="vertical-table" width="70%">
		<tr>
			<th>Task Name</th>
			<td><input type="text" name="title" id="title" /></td>
		</tr>
		<tr>
			<th>URL Reference</th>
			<td><input type="text" name="link" id="link" /> Separate list of URLs with commas (,)</td>
		</tr>
		<tr>
			<th>Assign To</th>
			<td><select name="assign"><?php echo $staffselect ?></select></td>
		</tr>
		<tr>
			<th>Priority</th>
			<td><select name="priority" id="priority"><option value="3">Normal Priority</option><option value="2">High Priority</option><option value="1">Urgent</option></select></td>
		</tr>
		<tr>
			<th>Body</th>
			<td>
				<textarea name="body"></textarea><br />
				Tip: You can use html in this box!
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Add" /></td>
		</tr>
    </table>
</form>