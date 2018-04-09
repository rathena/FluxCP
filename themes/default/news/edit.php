<?php 
if (!defined('FLUX_ROOT')) exit;
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
<h2><?php echo htmlspecialchars(Flux::message('CMSNewsEditTitle')) ?></h2>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if ($new): ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<table width="100%"> 
        <tr>
            <th width="100"><label for="news_title"><?php echo htmlspecialchars(Flux::message('CMSNewsTitleLabel')) ?></label></th>
            <td colspan="2"><input type="text" name="news_title" id="news_title" value="<?php echo htmlspecialchars($title) ?>"/></td>
        </tr>
        <tr>
            <th><label for="news_body"><?php echo htmlspecialchars(Flux::message('CMSNewsBodyLabel')) ?></label></th>
            <td colspan="2">
				<textarea name="news_body" cols="70"><?php echo htmlspecialchars($body) ?></textarea>
			</td>
        </tr>
		<tr>
            <th><label for="news_link"><?php echo htmlspecialchars(Flux::message('CMSNewsLinkLabel')) ?></label></th>
            <td width="100">
                <input type="text" name="news_link" id="news_link" value="<?php echo htmlspecialchars($link) ?>"/>
            </td>
			<td align="left"><?php echo htmlspecialchars(Flux::message('CMSOptionalLabel')) ?></td>
        </tr>
	<tr>
            <th><label for="news_author"><?php echo htmlspecialchars(Flux::message('CMSNewsAuthorLabel')) ?></label></th>
            <td width="100">
                <input type="text" name="news_author" id="news_author" value="<?php echo htmlspecialchars($author) ?>"/>
            </td>
			<td align="left"><?php echo htmlspecialchars(Flux::message('CMSRequiredLabel')) ?></td>
        </tr>
        <tr>
            <td colspan="3"><input type="submit" value="update" /></td>
        </tr>
    </table>
</form>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('CMSNewsNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('CMSGoBackLabel')) ?></a>
</p>
<?php endif ?>
