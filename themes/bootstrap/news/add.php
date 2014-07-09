<?php 
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired(); 
?>
<script type="text/javascript" src="<?php echo $this->themePath('tiny_mce/tiny_mce.js') ?>"></script>
<script type="text/javascript" src="<?php echo $this->themePath('tiny_mce/tiny_settings.js') ?>"></script>
<h2><?php echo htmlspecialchars(Flux::message('XCMSNewsAddTitle')) ?></h2>
<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>

<form action="<?php echo $this->urlWithQs ?>" method="post" class="generic-form">
	<table class="generic-form-table" width="100%"> 
        <tr>
            <th width="100"><label for="news_title"><?php echo htmlspecialchars(Flux::message('XCMSNewsTitleLabel')) ?></label></th>
            <td colspan="2"><input type="text" name="news_title" id="news_title" value="<?php echo htmlspecialchars($title) ?>"/></td>
        </tr>
        <tr>
            <th><label for="news_body"><?php echo htmlspecialchars(Flux::message('XCMSNewsBodyLabel')) ?></label></th>
            <td colspan="2">
				<textarea name="news_body" cols="70" class="cmsEnabled"><?php echo htmlspecialchars($body) ?></textarea>
			</td>
        </tr>
		<tr>
            <th><label for="news_link"><?php echo htmlspecialchars(Flux::message('XCMSNewsLinkLabel')) ?></label></th>
            <td width="100">
                <input type="text" name="news_link" id="news_link" value="<?php echo htmlspecialchars($link) ?>"/>
            </td>
			<td align="left"><?php echo htmlspecialchars(Flux::message('XCMSOptionalLabel')) ?></td>
        </tr>
	<tr>
            <th><label for="news_author"><?php echo htmlspecialchars(Flux::message('XCMSNewsAuthorLabel')) ?></label></th>
            <td width="100">
                <input type="text" name="news_author" id="news_author" value="<?php echo htmlspecialchars($author) ?>"/>
            </td>
			<td align="left"><?php echo htmlspecialchars(Flux::message('XCMSRequiredLabel')) ?></td>
        </tr>
        <tr>
            <td colspan="3"><input type="submit" value="Add" /></td>
        </tr>
    </table>
</form>