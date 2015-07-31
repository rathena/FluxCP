<?php
/* CMS Addon
 * Created and maintained by Akkarin
 * Current Version: 1.0.1
 */
 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<script type="text/javascript" src="<?php echo FLUX_ADDON_DIR.'/xcms/tiny_mce/tiny_mce.js'; ?>"></script>
<script type="text/javascript" src="<?php echo FLUX_ADDON_DIR.'/xcms/tiny_mce/tiny_settings.js'; ?>"></script>
<h2><?php echo htmlspecialchars(Flux::message('XCMSPageEditTitle')) ?></h2>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if ($page): ?>
<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<input type="hidden" name="page_id" value="<?php echo $id?>" />
	<table class="generic-form-table" width="100%">
		<tr>
			<th><label for="page_title"><?php echo htmlspecialchars(Flux::message('XCMSPageTitleLabel')) ?></label></th>
			<td><input type="text" name="page_title" id="page_title" value="<?php echo htmlspecialchars($title) ?>"/></td>
		</tr>
		<tr>
			<th width="100"><label for="page_path"><?php echo htmlspecialchars(Flux::message('XCMSPagePathLabel')) ?></label></th>
			<td><input type="text" name="page_path" id="page_path" value="<?php echo htmlspecialchars($path) ?>"/></td>
		</tr>
		<tr>
			<th><label><?php echo htmlspecialchars(Flux::message('XCMSPageBodyLabel')) ?></label></th>
			<td>
				<textarea name="page_body" class="cmsEnabled"><?php echo htmlspecialchars($body) ?></textarea>
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
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('XCMSGoBackLabel')) ?></a>
</p>
<?php endif ?>