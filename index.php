<?php
//https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c/setwebhook?url=https://bottelegramcarlosv2.herokuapp.com
$path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$hora = date("H:i:s");
$dia = date('l jS \of F Y');

if ($message=="hola") {
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=hola");
    }
elseif ($message=="hora") {
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=La hora es ".$hora);
    }
elseif ($message=="dia") {
     
    $diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");  
    file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Today is ".$diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') );

    }
elseif (strpos($message, "/tiempo") === 0) {
        $location = substr($message, 8);
        $weather = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/home"),true);
        $tiempo = $weather["ciudades"]["name"]["Barcelona"]["description"]; 
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=El tiempo en ".$location.": ". $tiempo);
        }
?>
