<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<script src="https://cdn.tiny.cloud/1/<?php echo $tinymce_key ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: "textarea",
        skin: "bootstrap",
        plugins: "anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate mentions tableofcontents footnotes autocorrect typography inlinecss",
        toolbar: "undo redo | blocks | bold italic | align | checklist numlist bullist indent outdent | link image | emoticons charmap | removeformat",
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
