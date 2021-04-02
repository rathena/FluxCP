<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<script src="https://cdn.tiny.cloud/1/<?php echo $tinymce_key ?>/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>tinymce.init(
	{
		selector:'textarea',
		plugins: [
			'advlist autolink lists link image charmap print preview anchor',
			'searchreplace visualblocks code fullscreen',
			'insertdatetime media table paste code'
		],
		toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
	});
</script>
<h2><?php echo htmlspecialchars(Flux::message('CMSPageAddTitle')) ?></h2>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<table width="100%">
		<tr>
			<th><label for="page_title"><?php echo htmlspecialchars(Flux::message('CMSPageTitleLabel')) ?></label></th>
			<td><input type="text" name="page_title" id="page_title" value="<?php echo htmlspecialchars($title) ?>"/></td>
		</tr>
		<tr>
			<th width="100"><label for="page_path"><?php echo htmlspecialchars(Flux::message('CMSPagePathLabel')) ?></label></th>
			<td><input type="text" name="page_path" id="page_path" value="<?php echo htmlspecialchars($path) ?>"/></td>
		</tr>
		<tr>
			<th><label><?php echo htmlspecialchars(Flux::message('CMSPageBodyLabel')) ?></label></th>
			<td>
				<textarea name="page_body"><?php echo htmlspecialchars($body) ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Add" /></td>
		</tr>
    </table>
</form>
