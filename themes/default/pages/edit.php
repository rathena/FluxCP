<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script>tinymce.init(
	{
		selector:'textarea',
		plugins: [
			'advlist autolink lists link image charmap print preview anchor',
			'searchreplace visualblocks code fullscreen',
			'insertdatetime media table contextmenu paste code'
		],
		toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
	});
</script>
<h2><?php echo htmlspecialchars(Flux::message('CMSPageEditTitle')) ?></h2>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if ($page): ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="page_id" value="<?php echo $id?>" />
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
			<td colspan="2"><input type="submit" value="Update" /></td>
		</tr>
    </table>
</form>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('PageNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('CMSGoBackLabel')) ?></a>
</p>
<?php endif ?>
