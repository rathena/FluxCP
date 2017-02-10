<?php if (!empty($errorMessage)): ?>
    <p class="red"><?php echo htmlspecialchars($errorMessage) ?></p>
<?php endif ?>
<?php if (!empty($successMessage)): ?>
    <p class="green"><?php echo htmlspecialchars($successMessage) ?></p>
<?php endif ?>

<h3>Upload itemInfo.lua</h3>
<form class="forms" method="post" enctype="multipart/form-data">
    <input type="file" name="iteminfo"><br>
    <input class="btn" type="submit">
</form>

<h3>Current Count</h3>
<p>There are currently <?php echo number_format($return->count) ?> item descriptions in the database</p>