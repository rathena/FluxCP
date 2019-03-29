<?php if (!defined('FLUX_ROOT')) exit; ?>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/x-icon" href="./favicon.ico" />
		<title>FluxCP: Install &amp; Update</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<style type="text/css">
			body { background-color: #ebeef2; }
			.contentcontainer {
				background-color: #FFFFFF;
				border:1px black solid;
				border-radius:5px;
				padding:30px;
			}
			.header { padding: 10px 0 130px 30px; background-color: #3c6994; margin-bottom: -125px; }
			.spacer30 {	padding-top:30px; }
			.uptodate { color: #008000; }
			.needtoupdate { color: #ff0000; }
		</style>
	</head>
	
	<body>
		<div class="header">
		</div>
		<div class="container">
			<div class="row" style="padding-bottom: 10px;"><img src="<?php echo $this->themePath('rathena-001.png') ?>" alt="Logo" width="354" height="80"></div>
			<div class="row">
				<div class="contentcontainer">
							<h1>Install &amp; Update</h1>
					<?php if ($message=$session->getMessage()): ?>
						<p class="message"><?php echo htmlspecialchars($message) ?></p>
					<?php endif ?>
					<?php if (!empty($errorMessage)): ?>
						<p class="error"><?php echo htmlspecialchars($errorMessage) ?></p>
					<?php endif ?>
