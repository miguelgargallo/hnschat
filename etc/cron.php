<?php
	include "includes.php";

	// FETCH AVATARS
	$getUsers = sql("SELECT * FROM `domains` WHERE `claimed` = 1 AND `locked` = 0 AND `deleted` = 0");
	foreach ($getUsers as $key => $data) {
		$avatar = fetchAvatar($data["domain"]);
		$avatarFile = "/var/www/html/hnschat/etc/avatars/".$data["id"];

		$tld = tldForDomain($data["domain"]);
		if ($tld && in_array($tld, $allSpecialDomains)) {
			if ($data["avatar"]) {
				$avatar = $data["avatar"];
			}
		}

		if ($avatar) {
			$response = getContentsWithCode($avatar);

			if (validImageWithoutFetch($response["data"])) {
				if ($response["code"] == 200) {
					sql("UPDATE `domains` SET `avatar` = ? WHERE `id` = ?", [$avatar, $data["id"]]);

					$newSize = strlen($response["data"]);
					if (file_exists($avatarFile)) {
						$currentSize = filesize($avatarFile);
					}

					if (!@$currentSize || (int)$newSize !== (int)$currentSize) {
						file_put_contents($avatarFile, $response["data"]);
					}
				}
			}
		}
	}

	// HIDE UNUSED CHANNELS
	/*
	$whitelist = ["skmo", "hnschat", "general", "feedback", "test", "eth"];
	$getChannels = sql("SELECT * FROM `channels`");
	foreach ($getChannels as $key => $data) {
		if (!in_array($data["name"], $whitelist)) {
			$latestMessage = @sql("SELECT * FROM `messages` WHERE `conversation` = ? ORDER BY `ai` DESC LIMIT 1", [$data["id"]])[0];

			$currentTime = new DateTime();
			$otherTime = new DateTime();

			if ($latestMessage) {
				$otherTime->setTimestamp($latestMessage["time"]);
			}
			else {
				$otherTime->setTimestamp($data["created"]);
			}

			$difference = (array) date_diff($currentTime, $otherTime);

			if ($difference["days"] >= 30) {
				sql("UPDATE `channels` SET `hidden` = 1 WHERE `id` = ?", [$data["id"]]);
			}
		}
	}
	*/

	// ACTIVATE NEW CHANNELS
	$getChannels = sql("SELECT * FROM `channels` WHERE `tx` IS NOT NULL AND `activated` = 0 AND `hidden` = 1");
	if ($getChannels) {
		foreach ($getChannels as $key => $data) {
			$verify = verifyTransaction($data["tx"], $data["fee"]);

			if ($verify) {
				sql("UPDATE `channels` SET `activated` = 1, `hidden` = 0 WHERE `id` = ?", [$data["id"]]);
			}
		}
	}
?>