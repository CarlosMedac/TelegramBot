<?php
//https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c/setwebhook?url=https://bottelegramcarlosv2.herokuapp.com
$path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$reply = $update["message"]["reply_to_message"]["text"];
$hora = date("H:i:s");
$dia = date('l jS \of F Y');
// $keyboard=[
//     ['Hola','XD'],
//     ['Cancelar'],
// ]
if(empty($reply)){
    if ($message=="hola") {
        // $saludo="Holahumano";
        // $key = array('one_time_keyboard' => true,'resize_keyboard' =>true,'keyboard'=>$keyboard);
        // $k=json_encode($key);
        enviarMensajes($chatId,"Hola humano",TRUE);
    }
    elseif ($message=="hora") {
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Son las ".$hora);
        }
    elseif ($message=="dia") {
        
        $diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");  
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=Hoy es ".$diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') );

    }
    elseif (strpos($message, "/tiempo") === 0) {
            $location = substr($message, 8);
            $weather = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/provincias"),true);
            $tiempo = $weather["provincias"];
            for($i=0;$i<count($tiempo);$i++){
                $provincias = $weather["provincias"][$i]["NOMBRE_PROVINCIA"]; 
                if($provincias == $location){
                    $codigoProvincia = $weather["provincias"][$i]["CODPROV"];
                    break;
                }
            }
            $tiempoProvincia = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/provincias/".$codigoProvincia),true);
            $tiempoDefinitivo = $tiempoProvincia["today"]["p"];
            file_get_contents($path."/sendmessage?chat_id=".$chatId."&text=El tiempo en ".$location.": ".urlencode($tiempoDefinitivo));
            }

    elseif($message=="/noticias"){
        include("simple_html_dom.php");

        $context = stream_context_create(array('http' =>  array('header' => 'Accept: application/xml')));
        $url = "http://www.europapress.es/rss/rss.aspx";

        $xmlstring = file_get_contents($url, false, $context);

        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        for ($i=0; $i < 4; $i++) { 
            $titulos = $titulos."\n\n".$array['channel']['item'][$i]['title']."<a href='".$array['channel']['item'][$i]['link']."'> +info</a>";
        }
        enviarMensajes($chatId,$titulos,True);
        
    }

}
function enviarMensajes($Id,$mensaje,$Respuesta){
    $path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
    file_get_contents($path."/sendmessage?chat_id=".$Id."&parse_mode=HTML&text= ".urlencode($mensaje));

}




?>
