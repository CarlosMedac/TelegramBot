<?php
//https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c/setwebhook?url=https://bottelegramcarlosv2.herokuapp.com
$path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$reply = $update["message"]["reply_to_message"]["text"];

$reply_a=explode(' ',$reply);

if(empty($reply)){
    switch($message){
        case "hola":
            $response= "Hola humano";
            enviarMensajes($chatId,$response,False);
            break;
        case "/hora":
            $hora = date("H:i:s");
            enviarMensajes($chatId,"Son las ".$hora,False);
            break;
        case "/dia":
            $dia = date('l jS \of F Y');
            $diassemana = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");  
            $response=$diassemana[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y');
            enviarMensajes($chatId,"Hoy es".$response,True);
            break;
        case "/help":
            $response= "Los comandos que puedes utilizar son:\n/hora\n/dia\n/hora\n/tiempo\n/noticias";
            enviarMensajes($chatId,$response,False);
            break;
        case "/tiempo":
            $response="Donde quieres consultar el tiempo?";
            enviarMensajes($chatId,$response,True);
            break;
        case "/noticias":
            $response="Que tipo de noticias quieres?\n /actualidad\n /deportes\n /tecnologia\n /internacional\n";
            enviarMensajes($chatId,$response,True);
            break;
        default:
            $response="No te he entendido introduce /help para ver los comandos";
            enviarMensajes($chatId,$response,False); 
    }
}else{
    switch($reply_a[0]){
        case "Que":
            include("simple_html_dom.php");
            $context = stream_context_create(array('http' =>  array('header' => 'Accept: application/xml')));
            if($message=="/actualidad"){
                $url = "http://www.europapress.es/rss/rss.aspx";
            }elseif($message=="/deportes"){
                $url = "https://www.europapress.es/rss/rss.aspx?ch=00067";
            }elseif($message=="/tecnologia"){
                $url = "https://www.europapress.es/rss/rss.aspx?ch=00564";
            }elseif($message=="/internacional"){
                $url = "https://www.europapress.es/rss/rss.aspx?ch=00069";
            }else{
                $response="No es una categoria valida";
                enviarMensajes($chatId,$response,False);
                break;
            }
                $xmlstring = file_get_contents($url, false, $context);
                $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
                $json = json_encode($xml);
                $array = json_decode($json, TRUE);

                for ($i=0; $i < 4; $i++) { 
                    $titulos = $titulos."\n\n".$array['channel']['item'][$i]['title']."<a href='".$array['channel']['item'][$i]['link']."'> +info</a>";
                }
            enviarMensajes($chatId,$titulos,False);
        break;
        case "Donde":
            $location = $message;
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
            $tiempoDefinitivo = $tiempoProvincia["ciudades"]["stateSky"]["description"];
            $contenidonubes=["nubes","cubierto"];
            $tiempoDefinitivo_a=explode(' ',$tiempoDefinitivo);
            $iconoTiempo;
            if(in_array($contenidonubes,$tiempoDefinitivo_a)){
                $iconoTiempo=☁️;
            }elseif((in_array("poco",$tiempoDefinitivo_a))){
                $iconoTiempo=🌥️;
            }elseif((in_array("lluvia",$tiempoDefinitivo_a))){
                $iconoTiempo=🌧️;
            }elseif((in_array("despejado",$tiempoDefinitivo_a))){
                $iconoTiempo=☀️;
            }elseif((in_array("soleado",$tiempoDefinitivo_a))){
                $iconoTiempo=☀️;
            }
            enviarMensajes($chatId,$location.":".$iconoTiempo,False);
        break;
        
    }
}
function enviarMensajes($chatId,$response,$respuesta){
    $path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
    $reply_mark = array("force_reply"=>true);
    if ($respuesta==True){
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&reply_markup=".json_encode($reply_mark)."&text=".urlencode($response));
    }else{
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&text=".urlencode($response));
    }
   

}




?>
