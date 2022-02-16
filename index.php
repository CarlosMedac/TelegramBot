<?php
//https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c/setwebhook?url=https://bottelegramcarlosv2.herokuapp.com
$path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$reply = $update["message"]["reply_to_message"]["text"];



if(empty($reply)){
    switch($message){
        case "hola":
            $response= "Hola humano";
            enviarMensajes($chatId,$response,True);
            break;
        case "/hora":
            $hora = date("H:i:s");
            enviarMensajes($chatId,"Son las ".$hora,True);
            break;
        case "/dia":
            $dia = date('l jS \of F Y');
            $diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");  
            $response=$diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');
            enviarMensajes($chatId,$response,True);
            break;
        case "/tiempo":
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
                    enviarMensajes($chatId,$tiempoDefinitivo,True);
            break;
        case "/noticias"
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
            break;
        default:
            $response="No te he entendido introduce /help para ver los comandos";
            enviarMensajes($chatId,$response,True); 
            break;
    }


}
function enviarMensajes($chatId,$response,$respuesta){
    $path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
    file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&text=".urlencode($response));

}




?>
