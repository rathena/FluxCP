<?php
if (!defined('FLUX_ROOT')) exit;
$emailTitle = sprintf('New Ticket Created');
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
		
		<p>You have received this e-mail because you have enabled email alerts for new tickets.</p>
		
		<p>
			<table style="margin-left: 18px">
				<tr>
					<td align="right">Category:&nbsp;&nbsp;</td>
					<th align="left">{Category}</th>
				</tr>
				<tr>
					<td align="right">Subject:&nbsp;&nbsp;</td>
					<th align="left">{Subject}</th>
				</tr>
				<tr>
					<td align="right">Ticket Body:&nbsp;&nbsp;</td>
					<th align="left">{Text}</th>
				</tr>
			</table>
		</p>
		<br />
		<p><em><strong>Note:</strong> This is an automated e-mail, please do not reply to this address.</em></p>
	</body>
</html>
