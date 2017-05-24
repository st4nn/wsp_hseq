<?php
	include "init.php";

	function validarUsuario($usuario, $clave, $limite)
	{
		$ec = new ExchangeClient();
		$ec->init("corp\\$usuario", $clave, NULL, "https://mail.onepb.net/ews/Services.wsdl");

		$abjArr = $ec->get_messagesCorp($limite);

		if ($abjArr === false)
		{
			$ec->init("gcg\\$usuario", $clave, NULL, "https://oamail-ca.wspgroup.com/EWS/Services.wsdl");

			$abjArr = $ec->get_messages($limite);

			if ($abjArr === false)
			{
				return false;
			} else
			{
				if (count($abjArr) > 0)
				{
					$Resultado = array();
					$Nombre = ucwords(strtolower(str_replace(".", " ", $usuario)));
					$Resultado['Nombre'] = $Nombre;
					$Resultado['Correo'] = "$usuario@wspgroup.com";
					return $Resultado;
				} else
				{
					return false;
				}
			}
		} else
		{	
			if ($abjArr === false)
			{
				return false;
			} else
			{
				if ($abjArr != "")
				{
					$Resultado = array();
					
					$Nombre = ucwords(strtolower(str_replace(".", " ", $usuario)));
					$Resultado['Nombre'] = $Nombre;
					$Resultado['Correo'] = "$usuario@wspgroup.com";
					return $Resultado;
				} else
				{
					return false;
				}
			}
		}			
	}

?>