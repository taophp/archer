<?php

$dsn = 'sqlite:'.dirname($_SERVER['SCRIPT_FILENAME']).'/db.sq3';

$db = new PDO($dsn);
if ($_SERVER['REQUEST_URI'] === '/' && count($_GET)===0) {
	printHeader();
	echo <<<EOH
	<h1>Archer Index</h1>
	<h2>Ajout d'un raccourci</h2>
	<form>
	<input name="url" placeholder="URL">
	<!--input name="shortid" placeholder="Identifiant souhaité"-->
	<input type="submit">
	</form>
	<h2>Raccourcis existants</h2>
	<table>
		<tr><th>id</th><th>Destination</th></tr>
EOH;
	$sql = 'SELECT * FROM shorts';
	$query = $db->query($sql);
	$results = $query->fetchAll();
	foreach ($results as $result) {
		echo '<td>'.$result['id'].'</td><td>'.$result['url'].'</td>';
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


