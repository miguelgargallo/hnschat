<?php
	include "includes.php";

	$id = $_GET["id"];
	$previewFile = "/var/www/html/hnschat/etc/previews/".$id;

	$getImage = @sql("SELECT `image` FROM `previews` WHERE `id` = ? AND `image` IS NOT NULL", [$id])[0];

	if ($getImage) {
		if (file_exists($previewFile)) {
			$image = file_get_contents($previewFile);
			$type = mime_content_type($previewFile);
		}
		else {
			$getImage["image"] = html_entity_decode(html_entity_decode($getImage["image"]));
			$image = getContents($getImage["image"]);
			if (validImageWithoutFetch($image)) {
				file_put_contents($previewFile, $image);
				$type = mime_content_type($previewFile);
			}
		}

		header("Content-Type: ".$type);
		die($image);
	}
?>