<?php
  include("../../conectar.php"); 
  include("../datosUsuario.php"); 
   $link = Conectar();

   $idUsuario = addslashes($_POST['Usuario']);
   $Desde = addslashes($_POST['Desde']);
   $Hasta = addslashes($_POST['Hasta']);

   $where = "activos.idEstadoActivo = 1";

   if ($Desde <> "")
   {
      if ($where <> "")
      {
         $where .= " AND ";
      }
      $where .= " activos.fechaLevantamiento >= '$Desde 00:00:00' ";
   }

   if ($Hasta <> "")
   {
      if ($where <> "")
      {
         $where .= " AND ";
      }
      $where .= " activos.fechaLevantamiento <= '$Hasta 23:59:59' ";
   }

   if ($where <> "")
   {
      $where = " WHERE " . $where;
   }

   $Usuario = datosUsuario($idUsuario);

   $sql = "SELECT 
               confTipoInventario.Nombre AS Producto, 
               SUM(activos.Cantidad) AS Cantidad
         FROM 
            activos  
            INNER JOIN confTipoInventario ON confTipoInventario.id = activos.idTipoInventario 
         $where
         GROUP BY 
            confTipoInventario.id
         ORDER BY confTipoInventario.Nombre DESC ;";

   $result = $link->query($sql);

   $idx = 0;
   $Cantidad = 0;
   if ( $result->num_rows > 0)
   {
      $Resultado = array();
      while ($row = mysqli_fetch_assoc($result))
      {
         $Resultado[$idx] = array();
         foreach ($row as $key => $value) 
         {
            $Resultado[$idx][$key] = utf8_encode($value);
         }
         $Cantidad += $row['Cantidad'];

         $idx++;
      }
      $Resultado[($idx - 1)]['Total'] = $Cantidad;

         mysqli_free_result($result);
         echo json_encode($Resultado);
   } else
   {
      echo 0;
   }
?>