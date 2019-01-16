<?php

$dsn = 'sqlite:'.dirname($_SERVER['SCRIPT_FILENAME']).'/db.sqlite';

$db = new PDO($dsn);
if ($_SERVER['REQUEST_URI'] === '/') {
	if (isset($_POST['url']) && filter_var($_POST['url'])) {
		$sql = 'INSERT INTO shorts values ('
			. (
				isset($_POST['short'])
				? $db->quote($_POST['code'])
				: '\'\''
			)
			. ','
			. $db->quote($_POST['url'])
			. ')'
		;
		if (!$db->exec($sql)) {
			var_dump($sql,$db->errorCode());
		}
	}
	printHeader();
	echo <<<EOH
	<h1>Archer Index</h1>
	<h2>Ajout d'un raccourci</h2>
	<form method="post">
	<input name="url" placeholder="URL">
	<input name="code" placeholder="Identifiant souhaité">
	<input type="submit">
	</form>
	<h2>Raccourcis existants</h2>
	<table>
		<tr><th>id</th><th>Shortcut</th><th>Destination</th></tr>
EOH;
	$sql = 'SELECT rowid,* FROM shorts';
	$query = $db->query($sql);
	$results = $query->fetchAll();
	foreach ($results as $result) {
		echo '<tr></tr><td>'.$result['rowid'].'</td><td>'
			.($result['code'] ? $result['code'] : base64_encode($result['rowid']))
			.'</td><td>'.$result['url'].'</td></tr>';
	}
	echo '</ul>';
	printFooter();
} else if (count($_GET) > 0) {

}else{
	$id = substr($_SERVER['REQUEST_URI'],1);
	$sql = 'SELECT * FROM shorts WHERE id = "'.$id.'";';
	$query = $db->query($sql);
	$result = $query->fetch();
	if ($result['url']) {
		header('Location:'.$result['url']);
		exit();
	}
}

function printHeader(){
	echo <<<EOH
	<!DOCTYPE html>
	<html>
		<head>
		</head>
		<body>
EOH;
}
function printFooter(){
	echo <<<EOH
	</body>
</html>
EOH;
}


