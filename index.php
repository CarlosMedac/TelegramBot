<?php
$path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c;
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = "hola";

if ($message=="hola") {
    file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=hola");
    }
?>