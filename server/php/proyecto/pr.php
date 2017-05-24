<?php
   include("../conectar.php"); 
   include("../../../vendors/mensajes/correo.php");
   include("datosUsuario.php");

   date_default_timezone_set('America/Bogota');

   $link = Conectar();

   $sql = "SELECT * FROM `Formatos` WHERE id = 9;";

   $result = $link->query($sql);

      if ( $result->num_rows > 0)
      {
         while ($row = mysqli_fetch_assoc($result))
         { 
            notificarMensaje(1, $row['idLogin'], $row['id']);
         }
         
         mysqli_free_result($result);  
      } else
      {
         return 0;
      }


   function notificarMensaje($datos, $idUsuario, $idFormato)
   {
      $Usuario = datosUsuario($idUsuario);
      if ($Usuario <> 0)
      {
         $link = Conectar();
         $sql = "SELECT Nombre, html from confFormatos WHERE id = '" . $datos . "';";
         $result = $link->query($sql);
         $fila =  $result->fetch_array(MYSQLI_ASSOC);
         
         $mensaje = "Buen Día " . $Usuario['Nombre'] . "<br><br>";

         $mensaje .= "Hemos registrado el diligenciamiento de " . $fila['Nombre'] . "<br><br>";

         $mensaje .= "Podrá consultar la información diligenciada en el siguiente link:<br><a href='" . $fila['html'] . $idFormato . "'>" . $fila['html'] . $idFormato . "</a><br><br>";

         $mensaje .= 'Gracias por su participación';

         $obj = EnviarCorreo($Usuario['Correo'] . ', Miguel.Jimenez@wsp.com' , "Diligenciamiento de " . $fila['Nombre'], $mensaje);

         return $obj;
      }
   }
?>