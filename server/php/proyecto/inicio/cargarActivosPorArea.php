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

   $sql = "SELECT Nombre, SUM(Ingresos) AS Ingresos, SUM(Salidas) AS Salidas FROM (SELECT 
               areas.Nombre,
               SUM(activos.ValorRazonable) AS Ingresos,
               0 AS Salidas
            FROM
               activos
               INNER JOIN areas ON areas.id = activos.idArea
               $where 
            GROUP BY areas.id
            
            UNION ALL
            
            SELECT 
               areas.Nombre,
               0 AS Ingresos,
               SUM(activos.Cantidad) AS Salidas
            FROM
               activos
               INNER JOIN areas ON areas.id = activos.idArea
               $where 
               GROUP BY areas.id) AS datos GROUP BY Nombre ORDER BY 2 DESC;";


   $result = $link->query($sql);

   $idx = 0;
   $CantidadPositiva = 0;
   $CantidadNegativa = 0;

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
         
         $CantidadPositiva += $row['Ingresos'];
         $CantidadNegativa += $row['Salidas'];

         $idx++;
      }

      $Resultado[($idx - 1)]['TotalIngresos'] = $CantidadPositiva;
      
      $Resultado[($idx - 1)]['TotalSalidas'] = $CantidadNegativa ;
      
      

         mysqli_free_result($result);
         echo json_encode($Resultado);
   } else
   {
      echo 0;
   }
?>