<?php
   include("../conectar.php"); 
   include("../../../vendors/mensajes/correo.php");
   include("datosUsuario.php");

   date_default_timezone_set('America/Bogota');

   $link = Conectar();

   $idUsuario = addslashes($_POST['Usuario']);
   
   $datos = json_decode($_POST['datos']);

   foreach ($datos as $key => $value) 
   {
      if (is_array($value))
      {
         foreach ($value as $key2 => $value2) 
         {
            foreach ($value2 as $key3 => $value3) 
            {
               $value2->$key3 = addslashes($value3);
            }

            $value[$key2] = $value2;
         }

         $datos->$key = $value;

      } else
      {
         $datos->$key = addslashes($value);
      }
  }

   $Respuesta = array();
   $Respuesta['Error'] = "";
   

   $sql = "INSERT INTO Formatos(idLogin, Formato, Datos) VALUES (
               '" . $idUsuario . "',
               '" . $datos->Formato . "',
               '" . str_replace("\\\\", "\\", json_encode($datos)) . "'
            ) ON DUPLICATE KEY UPDATE Datos = VALUES(Datos)";

   $link->query(utf8_decode($sql));

   if ( $link->error <> "")
   {
      $Respuesta['Error'] .= "\n Hubo un error desconocido " . $link->error;
   } else
   {
      $nuevoId = $link->insert_id;
      $Respuesta['datos'] = $nuevoId;

      $obj = notificarMensaje($datos, $idUsuario, $nuevoId);
   }

   echo json_encode($Respuesta);

   function notificarMensaje($datos, $idUsuario, $idFormato)
   {
      $Usuario = datosUsuario($idUsuario);
      if ($Usuario <> 0)
      {
         $link = Conectar();
         $sql = "SELECT Nombre, html from confFormatos WHERE id = '" . $datos->Formato . "';";
         $result = $link->query($sql);
         $fila =  $result->fetch_array(MYSQLI_ASSOC);
         
         $mensaje = "Buen Día " . $Usuario['Nombre'] . "<br><br>";

         $mensaje .= "Hemos registrado el diligenciamiento de " . $fila['Nombre'] . "<br><br>";

         $mensaje .= "Podrá consultar la información diligenciada en el siguiente link:<br><a href='" . $fila['html'] . $idFormato . "'>" . $fila['html'] . $idFormato . "</a><br><br>";

         $mensaje .= 'Gracias por su participación';

         $obj = EnviarCorreo($Usuario['Correo'] , "Diligenciamiento de " . $fila['Nombre'], $mensaje);

         return $obj;
      }
   }
?>