<?php
require_once('vendor/autoload.php');
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

$here = (isset($_SERVER['HTTPS']) ? 'https' : 'http').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
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
		<tr><th>id</th><th>Shortcut</th><th>Destination</th><th>QR</th></tr>
EOH;
	$sql = 'SELECT rowid,* FROM shorts';
	$query = $db->query($sql);
	$results = $query->fetchAll();
	foreach ($results as $result) {
		$url = $here.($result['code'] ? $result['code'] : base_convert($result['rowid'],10,36));
		$qrFile = $result['rowid'].'.png';
		if (!file_exists($qrFile)) {
			renderQr($url,$qrFile);
		}
		echo '<tr></tr><td>'.$result['rowid'].'</td><td>'
			.'<a href="/'.$url.'">'
			.$url
			.'</a></td><td>'.$result['url'].'</td>'
			.'<td><a href="'.$qrFile.'">Afficher</a></td>'
			.'</tr>';
	}
	echo '</ul>';
	printFooter();
} else if (count($_GET) > 0) {

}else{
	$id = $db->quote(substr($_SERVER['REQUEST_URI'],1));
	$sql = 'SELECT * FROM shorts WHERE rowid = "'.base_convert($id,36,10).'" or code = "'.$id.'";';
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
function renderQr(string $s,string $filename) {
	$renderer = new ImageRenderer(
			new RendererStyle(400),
			new ImagickImageBackEnd()
	);
	$writer = new Writer($renderer);
	$writer->writeFile($s, $filename);
}


