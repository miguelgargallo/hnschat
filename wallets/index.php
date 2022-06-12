<?php
	include "../etc/includes.php";

	$domain = $_SERVER["SERVER_NAME"];
	$request = $_SERVER["REQUEST_URI"];
	$wallet = @$_GET["wallet"];

	if ($wallet) {
		if (strtolower($request) == strtolower("/.well-known/wallets/HNS")) {
			$info = @sql("SELECT * FROM `domains` WHERE `domain` = ?", [$domain])[0];

			if ($info && @$info["address"]) {
				echo $info["address"];
				die();
			}
		}
	}
	http_response_code(404);
?>
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>