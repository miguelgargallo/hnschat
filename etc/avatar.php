<?php
	include "includes.php";

	$id = $_GET["id"];
	$avatarFile = "/var/www/html/hnschat/etc/avatars/".$id;
	
	$domainInfo = domainForID($id);
	if (@$domainInfo && @$domainInfo["avatar"] && file_exists($avatarFile)) {
		$image = file_get_contents($avatarFile);
		$type = mime_content_type($avatarFile);
		header("Content-Type: ".$type);
		die($image);
	}
?>