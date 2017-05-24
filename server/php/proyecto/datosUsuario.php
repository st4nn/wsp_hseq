<?php

   function datosUsuario($idUsuario)
   {
      $link = Conectar();
    
      $sql = "SELECT 
               Login.idLogin,
               Login.Usuario,
               Login.Estado,
               Login.idPerfil,
               DatosUsuarios.Nombre,
               DatosUsuarios.Correo
            FROM 
               Login AS Login
               INNER JOIN datosUsuarios AS DatosUsuarios ON Login.idLogin = DatosUsuarios.idLogin
            WHERE 
               Login.idLogin = '$idUsuario'
            GROUP BY
               Login.idLogin";
      
      $result = $link->query($sql);

      if ( $result->num_rows > 0)
      {
         $idx = 0;
            $Usuarios = array();
            while ($row = mysqli_fetch_assoc($result))
            { 
               foreach ($row as $key => $value) 
               {
                  $Usuarios[$key] = utf8_encode($value);
               }

               $idx++;
            }
            
               mysqli_free_result($result);  
               return $Usuarios;
      } else
      {
         return 0;
      }
   }
?>