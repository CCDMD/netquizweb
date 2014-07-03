<?php
    $response = array();

    $from = $_GET["from"];
    $to = $_GET["destinataire"];
    $subject = utf8_decode($_GET["sujet"]);
    $body = nl2br(utf8_decode($_GET["resultats"]));
    $msgOk = utf8_decode($_GET["msgok"]);
    $msgEmailError = utf8_decode($_GET["msgemailerror"]);
    
    $headers = "From: " . $from . "\n" . "X-Mailer: php" . "MIME-Version: 1.0\n" . "Content-type: text/html; charset=UTF-8\n" . "Content-Transfer-Encoding: 8bit";
    
    mb_language("uni");
  
    if (mb_send_mail ($to, $subject, $body, $headers)){
    	  $response["response"] = 1;
    	  $response["responseString"] = utf8_encode($msgOk);      
    }
    else{
    	  $response["response"] = 0;
    	  $response["responseString"] = utf8_encode($msgEmailError);        
    }  
    
    //$response = array_map("json_utf8", $response);
    $response = json_encode($response);
    
    echo $response;
?>