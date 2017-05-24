<?php
	require("class.smtp.php");
	require("phpmailer.lang-es.php");
	require("class.phpmailer.php");

	function EnviarCorreo($Destinatario, $Asunto, $Mensaje)
	{
		require("variables_correo.php");
		$raiz = $_SERVER['PHP_SELF'];
		$arrRaiz = explode('/', $raiz);

		$raiz = '';
		for ($i=0; $i < (count($arrRaiz) - 2); $i++) 
		{ 
			$raiz .= '../';
		}

		$mail = new PHPMailer();
		$mail->IsSMTP();

		$mail->SMTPDebug = 0;

		// Configuración del servidor en modo seguro
		$mail->SMTPAuth = 'True';
		//$mail->SMTPSecure = "ssl";
		$mail->Host = $host;
		$mail->Port = $puerto;

		// Datos de autenticación
		$mail->Username = $username;
		$mail->Password = $clave;

		$mail->From = $username;
		$mail->FromName = utf8_decode($Titulo);

		$mail->Subject = utf8_decode($Asunto);
		$mail->ContentType = 'html';
		$mail->IsHTML(true);
		
		$mail->AddEmbeddedImage($raiz . 'recuperar/img/logo.png', 'logo', 'logo.png', 'base64', 'image/png');
		$mail->AddEmbeddedImage($raiz . 'recuperar/img/logo_wsp.png', 'logoWSP', 'logoWSP.png', 'base64', 'image/png');
		
		$mensaje = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			$mensaje .= '<html xmlns="http://www.w3.org/1999/xhtml">';
			$mensaje .= '<head>';
	    		$mensaje .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	    		$mensaje .= '<title>' . $Titulo . '</title>';
	    		$mensaje .= '<style type="text/css">';
	        		$mensaje .= 'body{width:100% !important; color: #636363; margin:0; font-family: "Shanti", sans-serif;}';
	        		$mensaje .= 'body{-webkit-text-size-adjust:none;}';
	        		$mensaje .= 'body{margin:0; padding:0;}';
	        		$mensaje .= 'img{border:0; height:auto; line-height:100%; outline:none; text-decoration:none;}';
	        		$mensaje .= 'table td{border-collapse:collapse;}';
	        		$mensaje .= "<link href='https://fonts.googleapis.com/css?family=Shanti' rel='stylesheet' type='text/css'>";
	        		
	            $mensaje .= '</style>';
			$mensaje .= '</head>';
			$mensaje .= '<body>';
				$mensaje .= '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="height:80px;">';
				    $mensaje .= '<tr>';
				        $mensaje .= '<td align="center">';
				            $mensaje .= '<center>';
				                $mensaje .= '<table border="0" cellpadding="0" cellspacing="0" width="600px" style="height:100%;">';
				                    $mensaje .= '<tr>';
				                        $mensaje .= '<td align="left" valign="middle" style="padding-left:20px;">';
				                                $mensaje .= utf8_decode('<h1 style="color:white; font-family:Shanti,sans-serif"><br> <br></h1>');
				                        $mensaje .= '</td>';
				                        $mensaje .= '<td align="right" valign="middle" style="padding-right:20px;">';
				                            $mensaje .= '<table border="0" cellpadding="0" cellspacing="0" width="130px" style="height:100%;">';
				                                $mensaje .= '<tr>';
				                                    $mensaje .= '<td>';
				                                    $mensaje .= '</td>';
				                                    $mensaje .= '<td>';
				                                        $mensaje .= '<a href="' . $url . '">';
				                                            $mensaje .= '<img src="cid:logoWSP.png"  width="auto" height="41" />';
				                                        $mensaje .= '</a>';
				                                    $mensaje .= '</td>';
				                                $mensaje .= '</tr>';
				                            $mensaje .= '</table>';
				                        $mensaje .= '</td>';
				                    $mensaje .= '</tr>';
				                $mensaje .= '</table>';
				            $mensaje .= '</center>';
				        $mensaje .= '</td>';
				    $mensaje .= '</tr>';
				$mensaje .= '</table>';
				$mensaje .= '<br>';
			$mensaje .= utf8_decode($Mensaje);
			$mensaje .= "<br><br><br><img src='cid:logo.png' width='auto' height='60' border='0' boder='0' />";
				$mensaje .= utf8_decode('<br><br><br><p> Este mensaje fue enviado porque  está registrado en la base de datos de ' . $nombreApp . ' o porque pertenece a alguno de nuestros aliados estratégicos. Si desea dejar de recibir nuestros mensajes,<a href="#" target="_blank"> puede hacer clic aquí</a></p>');			
				$mensaje .= utf8_decode('<p>Este mensaje ha sido generado de forma automática y las respuestas a la misma no serán tenidas en cuenta, para cualquier inquietud por favor contacte a nuestro <a href="mailto:Itcolombia.Servicedesk@wspgroup.com">administrador</a></p>');
			$mensaje .= '</body>';
			$mensaje .= '</html>';

		$mail->Body = $mensaje;


		// Destinatario del mensaje
		$Destinatario = explode(", ", $Destinatario);
		foreach ($Destinatario as $key => $value) 
		{
			if (trim($value) <> "")
			{
				$mail->AddAddress ($value);
			}
		}
		$mail->AddReplyTo("Itcolombia.Servicedesk@wspgroup.com");
		$mail->AddBCC($username);

		// Envío del mensaje
		if(!$mail->Send()){
		    $error_message = "Error en el envío: " . $mail->ErrorInfo;
		}else{
		    $error_message = 1;
		}
		return $error_message; 
	}
?>