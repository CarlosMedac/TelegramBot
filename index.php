<?php
$path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$hoy = date("H:i:s");

if ($message=="hola") {
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=hola");
    }
if ($message=="hora") {
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=".$hoy);
    }

    // if (strpos($message, "/tiempo") === 0) {
    //     $location = substr($message, 8);
    //     $weather = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/home?name=".$location), TRUE)["description"]["temperatures"];
    //     file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=El tiempo en ".$location.": ". $weather);
    //     }
?>
