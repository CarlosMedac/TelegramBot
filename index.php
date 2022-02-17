<?php
//https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c/setwebhook?url=https://bottelegramcarlosv2.herokuapp.com
$path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
$update = json_decode(file_get_contents("php://input"), TRUE);

$chatId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$reply = $update["message"]["reply_to_message"]["text"];
$reply_a=explode(' ',$reply);

$keyboard=[
    ["Tiempo\u{2602}","Temperatura\u{1F321}"],
];
$key = array('one_time_keyboard' => true,'resize_keyboard' => true,'keyboard'=>$keyboard);
$k=json_encode($key);

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
            $response="Selecciona la opcion que quieras";
            enviarMensajes($chatId,$response,False,$k);
            break;
        case "/noticias":
            $response="Que tipo de noticias quieres?\n /actualidad\n /deportes\n /tecnologia\n /internacional\n";
            enviarMensajes($chatId,$response,True);
            break;
        case "\u{1F321}Tiempo":
            $response="Introduce la localidad que quieras consultar?";
            enviarMensajes($chatId,$response,True);
            break;
        case "\u{2602}Temperatura":
            $response="Donde quieres consultar la temperatura?";
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
        case "Introduce":
            $location = $message;
            $location=strtolower($location);
            $location = ucfirst($location);
            $weather = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/provincias"),true);
            $tiempo = $weather["provincias"];
                if($location=="Grana"){
                    $location="Granada";
                } 
                for($i=0;$i<count($tiempo);$i++){
                     $provincias = $weather["provincias"][$i]["NOMBRE_PROVINCIA"];
                     if($provincias=="Araba/Álava"){
                         $provincias="Álava";
                     }elseif($provincias=="Alacant/Alicante"){
                         $provincias="Alicante";
                     }elseif($provincias=="Illes Balears"){
                        $provincias="Islas Baleares";
                     }elseif($provincias=="Illes Balears"){
                        $provincias="Baleares";
                     }elseif($provincias=="Castelló/Castellón"){
                        $provincias="Castellón";
                     }elseif($provincias=="València/Valencia"){
                        $provincias="Valencia";
                     }
                        if($provincias == $location){
                            $codigoProvincia = $weather["provincias"][$i]["CODPROV"];
                            break;
                        }
                    }
            $tiempoProvincia = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/provincias/".$codigoProvincia),true);
            $tiempoDefinitivo = $tiempoProvincia["ciudades"][0]["stateSky"]["description"];
            $tiempoenMinuscula=strtolower($tiempoDefinitivo);
            $tiempoDefinitivo_a=explode(' ',$tiempoenMinuscula);
            $iconoTiempo="";
            if(in_array("tormentoso",$tiempoDefinitivo_a)){
                $iconoTiempo="\u{1F329}";
            }elseif((in_array("poco",$tiempoDefinitivo_a))){
                $iconoTiempo="\u{1F324}";
            }elseif((in_array("lluvia",$tiempoDefinitivo_a))){
                $iconoTiempo="\u{1F327}";
            }elseif((in_array("viento",$tiempoDefinitivo_a))){
                $iconoTiempo="\u{1F32B}";
            }elseif((in_array("despejado",$tiempoDefinitivo_a))){
                $iconoTiempo="\u{2600}";
            }elseif((in_array("nuboso",$tiempoDefinitivo_a))){
                $iconoTiempo="\xE2\x98\x81";
            }elseif((in_array("cubierto",$tiempoDefinitivo_a))){
                $iconoTiempo="\xE2\x98\x81";
            }elseif((in_array("nubes",$tiempoDefinitivo_a))){
                $iconoTiempo="\xE2\x98\x81";
            }elseif((in_array("nieve",$tiempoDefinitivo_a))){
                $iconoTiempo="\u{1F328}";
            }else{
                $iconoTiempo="";
            }
            enviarMensajes($chatId,$location.": ".$tiempoDefinitivo." ".$iconoTiempo,False);
        break;
        case "Donde":
            $location = $message;
            $location=strtolower($location);
            $location = ucfirst($location);
            $weather = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/provincias"),true);
            $tiempo = $weather["provincias"];
                if($location=="Grana"){
                    $location="Granada";
                } 
                for($i=0;$i<count($tiempo);$i++){
                     $provincias = $weather["provincias"][$i]["NOMBRE_PROVINCIA"];
                     if($provincias=="Araba/Álava"){
                         $provincias="Álava";
                     }elseif($provincias=="Alacant/Alicante"){
                         $provincias="Alicante";
                     }elseif($provincias=="Illes Balears"){
                        $provincias="Islas Baleares";
                     }elseif($provincias=="Illes Balears"){
                        $provincias="Baleares";
                     }elseif($provincias=="Castelló/Castellón"){
                        $provincias="Castellón";
                     }elseif($provincias=="València/Valencia"){
                        $provincias="Valencia";
                     }
                        if($provincias == $location){
                            $codigoProvincia = $weather["provincias"][$i]["CODPROV"];
                            break;
                        }
                    }
            $tiempoProvincia = json_decode(file_get_contents("https://www.el-tiempo.net/api/json/v2/provincias/".$codigoProvincia),true);
            $temperaturaMax = $tiempoProvincia["ciudades"][0]["temperatures"]["max"];
            $temperaturaMin = $tiempoProvincia["ciudades"][0]["temperatures"]["min"];
            $iconoTemperatura="";
            if(intval($temperaturaMax)<15){
                $iconoTemperatura="\u{2744}";
            }elseif(intval($temperaturaMax)>=15){
                $iconoTemperatura="\u{1F525}";
            }else{
                $iconoTemperatura="";
            }
            enviarMensajes($chatId,"Temperatura en ".$location.": Max=".$temperaturaMax." ".$iconoTemperatura." Min=".$temperaturaMin,False);
        break;
        
    }
}
function enviarMensajes($chatId,$response,$respuesta,&$k = ''){
    $path = "https://api.telegram.org/bot5151110160:AAG_KjSmkluICZF9iEoelxRRt6XvKEN8X5c";
    $reply_mark = array("force_reply"=>true);
    if ($respuesta==True){
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&reply_markup=".json_encode($reply_mark)."&text=".urlencode($response));
    }
    if(isset($k)){
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&reply_markup=".$k."&text=".urlencode($response));
    }else{
        file_get_contents($path."/sendmessage?chat_id=".$chatId."&parse_mode=HTML&text=".urlencode($response));
    }
    
    
    
   

}




?>
