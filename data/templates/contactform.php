<?php
/* Contact Form Addon
 * Created and maintained by Akkarin
 * Current Version: 1.00.01
 */
 
if (!defined('FLUX_ROOT')) exit;
$emailTitle = sprintf('Contact Form Submission');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo htmlspecialchars($emailTitle) ?></title>
		<style type="text/css" media="screen">
			body, table {
				font-family: sans-serif;
				font-size: 10pt;
			}
		</style>
	</head>
	<body>
		<h2><?php echo htmlspecialchars($emailTitle) ?></h2>
		
		<p>A member of our site has submitted a question/query.</p>
		
		<p>
			<table style="margin-left: 18px">
				<tr>
					<td align="right">AccountID:&nbsp;&nbsp;</td>
					<th align="left">{AccountID}</th>
				</tr>
				<tr>
					<td align="right">Name:&nbsp;&nbsp;</td>
					<th align="left">{Name}</th>
				</tr>
				<tr>
					<td align="right">Email:&nbsp;&nbsp;</td>
					<th align="left">{Email}</th>
				</tr>
				<tr>
					<td align="right">Subject:&nbsp;&nbsp;</td>
					<th align="left">{Subject}</th>
				</tr>
				<tr>
					<td align="right">Body:&nbsp;&nbsp;</td>
					<th align="left">{Body}</th>
				</tr>
				<tr>
					<td align="right">IP:&nbsp;&nbsp;</td>
					<th align="left">{IP}</th>
				</tr>
			</table>
		</p>
		<br />
		<p><em><strong>Note:</strong> This is an automated e-mail, please do not reply to this address.</em></p>
	</body>
</html>
