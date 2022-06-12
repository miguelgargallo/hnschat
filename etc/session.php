<?php
	$revision = file_get_contents("/var/www/html/hnschat/.version");

	$ipAddress = @$_SERVER["REMOTE_ADDR"];
	$httpHost = @$_SERVER["HTTP_HOST"];
	$self = @$_SERVER["PHP_SELF"];
	$scriptFile = @$_SERVER["SCRIPT_FILENAME"];

	$specialDomains = ["connext", "handynews", "handytalk", "hnsdeveloper", "theshake", "orbitlaunch", "xn--qq8hq7a", "xpander"];
	$fullyStakedDomains = ["theshake"];
	sort($specialDomains);

	array_unshift($specialDomains, "hnschat");
	array_unshift($fullyStakedDomains, "hnschat");

	$allSpecialDomains = $specialDomains;
	array_unshift($allSpecialDomains, "handycon");

	$bannedNames = ["root", "admin", "administrator", "staff", "www", "eskimo", "skmo", "on", "news"];

	$channelPrice = 50;

	if ($self === "/beta.php") {
		$isBeta = true;
		$revision = time();
	}
?>