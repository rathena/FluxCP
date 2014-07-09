<?php
/* CMS Addon
 * Created and maintained by Akkarin
 * Current Version: 1.0.1
 */
 
if (!defined('FLUX_ROOT')) exit;
?>
<h2><?php echo $title ?></h2>
<?php echo $body ?>
<p><small><?php echo htmlspecialchars(Flux::message('XCMSModifiedLabel')) ?> : <?php echo date(Flux::config('DateFormat'),strtotime($modified))?></small></p>