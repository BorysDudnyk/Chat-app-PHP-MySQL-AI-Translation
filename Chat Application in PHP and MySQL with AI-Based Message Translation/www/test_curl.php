<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://libretranslate.de");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

echo "RESPONSE:\n$response\n\n";
echo "ERROR:\n$error";
?>
