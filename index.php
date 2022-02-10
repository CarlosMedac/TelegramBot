<?php
$path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$hora = date("H:i:s");
$dia = getdate();

if ($message=="hola") {
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=hola");
    }
elseif ($message=="hora") {
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=La hora es".$hora);
    }
elseif ($message=="dia") {
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Hoy es".$dia);
    }

    // if (strpos($message, "/tiempo") === 0) {
    //     $location = substr($message, 8);
    //     $weather = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/home?name=".$location), TRUE)["description"]["temperatures"];
    //     file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=El tiempo en ".$location.": ". $weather);
    //     }
?>
