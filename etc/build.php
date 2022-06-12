<?php
	include "functions.php";

	/* JS */
	$jsPath = "/var/www/html/hnschat/assets/js/";

	$resources = [];
	$getFiles = scandir($jsPath);
	foreach ($getFiles as $file) {
		if ($file[0] === "." || strpos($file, ".min.")) {
			continue;
		}

		if ($file !== "script.js" && $file !== "resources.js") {
			$resources[] = $jsPath.$file;
		}
	}

	$resourceString = implode(" ", $resources);
	$merged = shell_exec("cat ".$resourceString." > ".$jsPath."resources.js");

	$uglifiedResources = shell_exec("cat ".$jsPath."resources.js | uglifyjs -c -m");
	$uglifiedScript = shell_exec("cat ".$jsPath."script.js | uglifyjs -c -m");
	file_put_contents($jsPath."resources.min.js", $uglifiedResources);
	file_put_contents($jsPath."script.min.js", $uglifiedScript);


	/* CSS */
	$cssPath = "/var/www/html/hnschat/assets/css/";
	$cssMinifier = new CssMinifer([$cssPath."style.css"]);
	$minifiedStyle = $cssMinifier->minify();
	file_put_contents($cssPath."style.min.css", $minifiedStyle);


	/* VERSION STRING */
	$versionFile = "/var/www/html/hnschat/.version";
	$version = file_get_contents($versionFile);
	$split = explode("v", $version);
	$previousDate = $split[0];
	$previousVersion = $split[1];

	$currentDate = date("Ymd");
	$currentVersion = 1;

	if ($previousDate == $currentDate) {
		$currentVersion = $previousVersion + 1;
	}

	$newString = $currentDate."v".$currentVersion;
	file_put_contents($versionFile, $newString);
?>