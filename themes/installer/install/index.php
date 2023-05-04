<?php if (!$session->installerAuth): ?>
	<form action="<?php echo $this->url ?>" method="post" class="row g-3">
		<p>
			Please enter your <em>installer password</em> to continue with the update.
		</p>
		<div class="col-auto">
			<label for="installer_password">Password:</label>
		</div>
		<div class="col-auto">
			<input class="form-control" type="password" id="installer_password" name="installer_password" />
		</div>
		<div class="col-auto">
			<button type="submit" class="btn btn-success">Authenticate</button>
		</div>
	</form>
<?php else: ?>
	<?php if (isset($permissionError)): ?>
		<h2 class="error">MySQL Permission Error Encountered</h2>
		<p>Uh oh, the installer encountered a permission error while trying to execute one of the schema definitions!</p>
		<p>This typically means that the query failed due to lack of user/database/table permissions in MySQL.</p>
		<table class="schema-info">
			<!--
			<tr>
				<th>Schema Type</th>
				<td><?php echo $permissionError->isLoginDbSchema() ? 'Login Server Database' : 'Char/Map Server Database' ?></td>
			</tr>
			<tr>
				<th>Schema File</th>
				<td><?php echo htmlspecialchars(realpath($permissionError->schemaFile)) ?></td>
			</tr>
			-->
			<tr>
				<th>Server</th>
				<td>
					<?php echo htmlspecialchars($permissionError->mainServerName) ?>
					<?php if ($permissionError->charMapServerName): ?>
						(<?php echo htmlspecialchars($permissionError->charMapServerName) ?>)
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<th>Database</th>
				<td><?php echo htmlspecialchars($permissionError->databaseName) ?></td>
			</tr>
			<tr>
				<th>Error</th>
				<td><?php echo htmlspecialchars($permissionError->getMessage()) ?></td>
			</tr>
			<tr>
				<th>SQL Query</th>
				<td><code><?php echo nl2br(htmlspecialchars($permissionError->query)) ?></code></td>
			</tr>
		</table>
		<h4 style="margin: 9px 0 0 0">The recommended solution to a problem like this is to grant the user the the privileges to
			run the query on the database or table.</h4>
		<h4 style="margin: 4px 0 0 0">Manually running the SQL query is not a supported method because schema versioning will break
			and the installer will not go away.</h4>
	<?php else: ?>
	<div>
		<p class="menu">
			<a href="<?php echo $this->url($params->get('module'), null, array('logout' => 1)) ?>" onclick="return confirm('Are you sure you want to log out?')">Logout</a> |
			<a href="<?php echo $this->url($params->get('module'), null, array('update_all' => 1)) ?>" onclick="return confirm('By performing this action, changes to your database will be made.\n\nAre you sure you want to continue installing Flux and its associated updates?')"><strong>Install or Update Everything</strong></a>
		</p>
		<p>"Install or Update Everything" will use the pre-configured MySQL username and password for each server.</p>
		<p>Shown below is a list of currently installed / need-to-be-installed schemas.</p>
		<form action="<?php echo $this->urlWithQs ?>" method="post">
			<?php foreach ($installer->servers as $mainServerName => $mainServer): ?>
			<?php $servName = base64_encode($mainServerName) ?>
			<div class="row">
				<div class="col"><h3><?php echo htmlspecialchars($mainServerName) ?></h3></div>
			</div>
			<div class="row pb-2">
				<div class="col">Alternative MySQL username/password</div>
			</div>
			<div class="row pb-2">
				<div class="col-6">
					<label for="username_<?php echo $servName ?>">MySQL username</label>
				</div>
				<div class="col"><input class="form-control" type="text" name="username[<?php echo $servName ?>]" id="username_<?php echo $servName ?>" /></div>
			</div>
			<div class="row pb-3">
				<div class="col-6">
					<label for="password_<?php echo $servName ?>">MySQL password</label>
				</div>
				<div class="col"><input class="form-control" type="password" name="password[<?php echo $servName ?>]" id="password_<?php echo $servName ?>" /></div>
			</div>
			<div class="row pb-5">
				<div class="col text-center">
					<button type="submit" name="update[<?php echo $servName ?>]" class="btn btn-success">
						Update <strong><?php echo htmlspecialchars($mainServerName) ?></strong>
					</button>
				</div>
			</div>
			<div class="row">
				<table class="table">
					<th>Schema Name</th>
					<th>Latest Version</th>
					<th>Version Installed</th>
			</tr>
					<?php foreach ($mainServer->schemas as $schema): ?>
				<tr>
					<td>
						<span class="text-<?php echo ($schema->versionInstalled == $schema->latestVersion) ? 'success' : 'danger' ?>">
							<?php echo htmlspecialchars($schema->schemaInfo['name']) ?>
						</span>
					</td>
					<td>
						<?php if ($schema->latestVersion > $schema->versionInstalled): ?>
							<span class="schema-query" title="<?php echo htmlspecialchars(file_get_contents($schema->schemaInfo['files'][$schema->latestVersion])) ?>">
							<?php echo htmlspecialchars($schema->latestVersion) ?>
							</span>
						<?php else: ?>
							<?php echo htmlspecialchars($schema->latestVersion) ?>
						<?php endif ?>
					</td>
					<td><?php echo $schema->versionInstalled ? htmlspecialchars($schema->versionInstalled) : '<span class="none">None</span>' ?></td>
				</tr>
					<?php endforeach ?>

					<?php foreach ($mainServer->charMapServers as $charMapServerName => $charMapServer): ?>
				<tr>
					<th colspan="3" class="pt-4"><h4><?php echo htmlspecialchars($charMapServerName) ?></h4></th>
				</tr>
				<tr>
					<th>Schema Name</th>
					<th>Latest Version</th>
					<th>Version Installed</th>
				</tr>
						<?php foreach ($charMapServer->schemas as $schema): ?>
				<tr>
					<td>
						<span class="text-<?php echo ($schema->versionInstalled == $schema->latestVersion) ? 'success' : 'danger' ?>">
							<?php echo htmlspecialchars($schema->schemaInfo['name']) ?>
						</span>
					</td>
					<td><?php echo htmlspecialchars($schema->latestVersion) ?></td>
					<td><?php echo $schema->versionInstalled ? htmlspecialchars($schema->versionInstalled) : '<span class="none">None</span>' ?></td>
				</tr>
						<?php endforeach ?>

					<?php endforeach ?>
				<?php endforeach ?>
			</table>
						</div>
		</form>
		</div>
	<?php endif ?>
<?php endif ?>
