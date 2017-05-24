<?php

	require('../../vendors/fpdf/fpdf.php');
	require('../php/conectar.php');
	$link = Conectar();

	$idFormato = $_GET['i'];

	$tamanioFuente_11 = 11;
	$tamanioFuente_8 = 8;
	$tamanioFuente_6 = 6;
	$tamanioFuente_4 = 5;
	$tamanioFuente_4 = 4.5;
	

	$sql = "SELECT * FROM Formatos WHERE id = '$idFormato'";
	$result = $link->query($sql);
	if ($result->num_rows == 0)
	{
		echo "No hay archivo";
	} else
	{
		$row = $result->fetch_assoc();
		$datos = json_decode($row['Datos']);

		class PDF extends FPDF
		{
			function Header()
			{
			    // Logo
			    $this->Image('logo_wsp.png',0,10,50);
			}

			function Footer()
			{
			    // Posición: a 1,5 cm del final
			    $this->SetY(-15);
			    
			    $this->SetFont('Arial','B',8);

			    $this->Cell(0,10,'Página '.$this->PageNo().' de {nb}',0,0,'R');
			}
		}

		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->SetMargins(10, 10, 10);
		$pdf->AddPage();

		$posX = 0;
		$posY = 10;


		$pdf->SetFont('Arial','B',$tamanioFuente_11);

		$pdf->Cell(0,5,'Evaluación y Aprobación de Viajes',0,1,'C');
		
		$pdf->SetFont('Arial','',$tamanioFuente_8);
		
		$pdf->Ln();

        $pdf->Multicell(0,4, 'Este reporte debe ser completado antes del viaje, basado en el procedimiento local, para evaluar el nivel de riesgo. Debe ser aprobado basado en el nivel de riesgo (ver abajo). Se requiere un solo formato si el viaje es realizado en convoy.' ,0,'L');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',$tamanioFuente_8);
		$pdf->Cell(0,5,'A. Generalidades del viaje',0,1,'C');
		$pdf->SetFont('Arial','',$tamanioFuente_8);

		$pdf->Ln();

		$pdf->SetFillColor(217, 217, 214);

		$pdf->Cell(45,5,'Motivo del Viaje',1,0,'L', 1);
		$pdf->Cell(55,5,$datos->P1 ,1,0,'L');

		$pdf->Cell(45,5,'N° Proyecto:',1,0,'L', 1);
		$pdf->Cell(45,5,$datos->P2 ,1,1,'L');
		
		$pdf->Cell(45,5,'Area:',1,0,'L', 1);
		$pdf->Cell(145,5,$datos->P65 ,1,1,'L');

		$pdf->Cell(45,5,'Locación responsable viaje:',1,0,'L', 1);
		$pdf->Cell(145,5,$datos->P3 ,1,1,'L');

		$pdf->Cell(45,5,'Nombre del Gerente del Viaje /PM:',1,0,'L', 1);
		$pdf->Cell(145,5,$datos->P4 ,1,1,'L');

		$pdf->Cell(45,5,'Lugar de Salida:',1,0,'L', 1);
		$pdf->Cell(145,5,$datos->P5 ,1,1,'L');

		$pdf->Cell(45,5,'Fecha de Salida:',1,0,'L', 1);
		$pdf->Cell(50,5,$datos->P6 ,1,0,'L');

		$pdf->Cell(45,5,'Hora de Salida:',1,0,'L', 1);
		$pdf->Cell(50,5,$datos->P7 ,1,1,'L');

		$pdf->Cell(45,5,'Nombre del Liden Viaje/Convoy:',1,0,'L', 1);
		$pdf->Cell(145,5,$datos->P10 ,1,1,'L');

		$pdf->Cell(45,5,'Lugar de Destino:',1,0,'L', 1);
		$pdf->Cell(145,5,$datos->P11 ,1,1,'L');

		$pdf->Cell(45,5,'Fecha estimada de Lllegada:',1,0,'L', 1);
		$pdf->Cell(50,5,$datos->P12 ,1,0,'L');

		$pdf->Cell(45,5,'Hora estimada de Lllegada:',1,0,'L', 1);
		$pdf->Cell(50,5,$datos->P13 ,1,1,'L');

		$pdf->Ln();

		$pdf->Cell(48,5,'Distancia Estimada:',1,0,'L', 1);
		$pdf->Cell(47,5,'Total Vehículos:',1,0,'L', 1);
		$pdf->Cell(47,5,'N° Conductores:',1,0,'L', 1);
		$pdf->Cell(48,5,'N° Pasajeros:',1,1,'L', 1);

		$pdf->Cell(48,5, $datos->P14,1,0,'L');
		$pdf->Cell(47,5, $datos->P15,1,0,'L');
		$pdf->Cell(47,5, $datos->P16,1,0,'L');
		$pdf->Cell(48,5, $datos->P17,1,1,'L');

		$pdf->Ln();

		$pdf->Cell(150,5, 'Responda estas preguntas',0,0,'L');
		$pdf->Cell(20,5,'Si',1,0,'C', 1);
		$pdf->Cell(20,5,'No',1,1,'C', 1);

		$Cajon = ($datos->P8 == 'Si');
		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(150,5, 'Es el Viaje necesario?',0,0,'R');
		$pdf->Cell(20,5,'',1,0,'C', $Cajon);
		$pdf->Cell(20,5,'',1,1,'C', !$Cajon);

		$Cajon = ($datos->P9 == 'Si');
		$pdf->Cell(150,5, 'Se realizó la reunión de planificación pre-viaje?',0,0,'R');
		$pdf->Cell(20,5,'',1,0,'C', $Cajon);
		$pdf->Cell(20,5,'',1,1,'C', !$Cajon);

		$pdf->Ln();

		$pdf->SetFont('Arial','B',$tamanioFuente_11);
		$pdf->Cell(0,5,'B. Cuestionario Pre viaje',0,1,'C');
		$pdf->SetFont('Arial','B',$tamanioFuente_8);

		$pdf->Ln();

		$pdf->Multicell(0,4, 'Esta información debe ser revisada durante la reunión Pre-viaje y evaluada por el Gerente/Supervisor responsable del viaje.' ,0,'L');
		$pdf->SetFont('Arial','',$tamanioFuente_8);

		$pdf->SetFillColor(217, 217, 214);
		$pdf->Cell(150,5, '',0,0,'L');
		$pdf->Cell(20,5,'Si',1,0,'C', 1);
		$pdf->Cell(20,5,'No',1,1,'C', 1);

		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(150,7, '1) Los vehículos cumplen con los requerimientos establecidos en la inspección de vehículo?',0,0,'L');
		$Cajon = ($datos->P18 == 'Si');
		$pdf->Cell(20,7,'',1,0,'C', $Cajon);
		$pdf->Cell(20,7,'',1,1,'C', !$Cajon);

		$pdf->Cell(150,7, '2) Ha revisado el Análisis de Peligros de la ruta "RHA" para identificar las áreas de alto riesgo, horas de contacto?',0,0,'L');
		$Cajon = ($datos->P19 == 'Si');
		$pdf->Cell(20,7,'',1,0,'C', $Cajon);
		$pdf->Cell(20,7,'',1,1,'C', !$Cajon);

		$pdf->Multicell(150,3, '3) Se ha revisado estado de fatiga y condiciones  (paradas planificadas para comer y descansar, horas de trabajo, horas de  manejo, puntos críticos de la vía)',0,'L');
		$Cajon = ($datos->P20 == 'Si');
		$pdf->SetXY(160,160);
		$pdf->Cell(20,7,'',1,0,'C', $Cajon);
		$pdf->Cell(20,7,'',1,1,'C', !$Cajon);

		$pdf->Multicell(150,3, '4) Conocen los conductores y pasajeros el protocolo de notificación en caso de emergencia y se ha notificado este viaje al personal en el destino?', 0,'L');
		$Cajon = ($datos->P21 == 'Si');
		$pdf->SetXY(160,167);
		$pdf->Cell(20,7,'',1,0,'C', $Cajon);
		$pdf->Cell(20,7,'',1,1,'C', !$Cajon);
		

		$pdf->SetFont('Arial','B',$tamanioFuente_8);
		$pdf->Multicell(0,4, 'No iniciar viaje en caso de que alguna respuesta sea respondida con un "NO", a  menos que se disminuya el riesgo o se desarrolle un proceso de Exención.' ,0,'L');
		$pdf->SetFont('Arial','',$tamanioFuente_8);

		$pdf->SetFillColor(217, 217, 214);

		$pdf->Ln();
		$pdf->SetFont('Arial','B',$tamanioFuente_11);
		$pdf->Cell(0,5,'C. Evaluación de Riesgos',0,1,'C');
		$pdf->SetFont('Arial','',$tamanioFuente_8);

		$pdf->Ln();


		$pdf->Cell(62,5, 'FACTORES FÍSICOS',1,0,'C', 1);
		$pdf->Cell(62,5, 'FACTORES HUMANOS',1,0,'C', 1);
		$pdf->Cell(62,5, 'OTROS FACTORES DE RIESGO',1,1,'C', 1);
		
		$pdf->SetFont('Arial','',$tamanioFuente_6);

		$pdf->Cell(62,4, '1 - Distancia desde la Salida / Horas',1,0,'C', 1);
		$pdf->Cell(62,4, '6 - Evaluación de condiciones de fatiga',1,0,'C', 1);
		$pdf->Cell(62,4, '7. Horario de viaje',1,1,'C', 1);

		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,0,'C', 1);
		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,0,'C', 1);
		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,1,'C', 1);

		

		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(54,4, 'De 1 Hr a 2 Hr',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P22);

		$pdf->SetFillColor(217, 217, 214);

		$pdf->Cell(62,4, 'Sub ITEM: Con más de 8 hs. Dormidas',1,0,'L', 1);

		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(54,4, 'Manejo desde 06:31 hrs. hasta 18:29 hrs.',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P48);

		$pdf->Cell(54,4, 'De 2 Hr a 4 Hr',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P23);

		$pdf->Cell(54,4, 'Hrs. trabajadas + hrs. previstas de viaje < 12 hs.',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P42);

		$pdf->Cell(54,4, 'Manejo desde 18:30 hrs. hasta 23:00 hrs.',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P49);


		$pdf->Cell(54,4, 'De 4 Hr a 6 Hr ',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P24);

		$pdf->Cell(54,4, 'Hrs. trabajadas + hrs. previstas de viaje < 14 hs.',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P43);

		$pdf->Cell(54,4, 'Manejo desde 23:01 hrs. hasta 06:30 hrs.',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P50);


		$pdf->Cell(54,4, 'Mayor a 6 Hr',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P25);

		$pdf->Cell(54,4, 'Hrs. trabajadas + hrs. previstas de viaje < 16 hs.',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P44);

		$pdf->SetFillColor(217, 217, 214);
		$pdf->Cell(62,4, '8- Motocicletas,ciclistas y Peatones transitando sobre la ruta',1,1,'C', 1);

		
		$pdf->Cell(62,4, '2 - Clima - Terreno',1,0,'L', 1);
		$pdf->Cell(62,4, 'Sub ITEM: Con menos de 8 hs. Dormidas',1,0,'C', 1);
		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,1,'C', 1);

		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,0,'C', 1);

		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(54,4, 'Hrs. trabajadas + hrs. previstas de viaje < 12 hs.',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P45);

		$pdf->SetFont('Arial','',$tamanioFuente_4);
		$pdf->Cell(54,4, 'No hay Evidencia de Transito de Motociclistas y Peatones sobre la ruta',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P51);
		$pdf->SetFont('Arial','',$tamanioFuente_6);


		$pdf->Cell(54,4, 'Seco',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P26);

		$pdf->Cell(54,4, 'Hrs. trabajadas + hrs. previstas de viaje < 14 hs.',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P46);

		$pdf->SetFont('Arial','',$tamanioFuente_4);
		$pdf->Cell(54,4, 'Hay un transito medio de Motociclistas y Peatones en la Ruta',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P52);
		$pdf->SetFont('Arial','',$tamanioFuente_6);


		$pdf->Cell(54,4, 'Viento',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P27);

		$pdf->Cell(54,4, 'Hrs. trabajadas + hs. previstas de viaje < 16 hs.',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P47);
		
		$pdf->Cell(54,4, 'Hay un alto transito de Motociclistas, Ciclistas en la ruta',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P53);


		$pdf->Cell(54,4, 'Lluvia',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P28);

		$pdf->SetFont('Arial','B',$tamanioFuente_6);
		$pdf->Cell(62,4, 'Trabajo + viaje > 16 hs. ? Si se cumple esta condición el ',0,0,'L');//conductor debera descansar
		$pdf->SetFont('Arial','',$tamanioFuente_6);

		$pdf->SetFillColor(217, 217, 214);
		
		$pdf->Cell(62,4, '9- Numero de Vehículos y Pasajeros',1,1,'C', 1);

		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(54,4, 'Niebla, Grava, barro ',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P29);

		$pdf->SetFont('Arial','B',$tamanioFuente_6);
		$pdf->Cell(62,4, 'conductor debera descansar',0,0,'L');//
		$pdf->SetFont('Arial','',$tamanioFuente_6);

		$pdf->SetFillColor(217, 217, 214);
		
		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,1,'C', 1);


		$pdf->Cell(62,4, '3 - Condiciones de la Vía',1,0,'C', 1);

		$pdf->Cell(62,0, '',1,0,'C');

		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(54,4, '1 vehículo con 1 persona ',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P54);

		$pdf->SetFillColor(217, 217, 214);
		
		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,0,'C', 1);

		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(62,4, '',0,0,'C');

		$pdf->Cell(54,4, '1 vehículo con 2 ó más personas',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P55);


		$pdf->Cell(54,4, 'Vía Principal Pavimentada',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P30);

		$pdf->Cell(62,4, '',0,0,'C');

		$pdf->Cell(54,4, '2 ó + vehículos con 1 persona por vehículos ',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P56);


		$pdf->Cell(54,4, 'Vía Secundaria Pavimentada',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P31);

		$pdf->Cell(62,4, '',0,0,'C');

		$pdf->Cell(54,4, '2 ó + vehículos con 2 ó + personas por vehículos',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P57);


		$pdf->Cell(54,4, 'Vía terciaria Pavimentada',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P32);

		$pdf->Cell(124,4, '',0,1,'C');


		$pdf->Cell(54,4, 'Vía terciaria sin pavimento',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P33);

		$pdf->Cell(124,4, '',0,1,'C');

		$pdf->AddPage();
		$pdf->SetFillColor(217, 217, 214);
		$pdf->SetFont('Arial','',$tamanioFuente_8);
		$pdf->Cell(62,5, 'FACTORES FÍSICOS',1,0,'C', 1);
		//$pdf->Cell(62,5, 'FACTORES HUMANOS',1,0,'C', 1);
		$pdf->Cell(62,5, '',0,0,'C', 0);
		$pdf->Cell(62,5, 'OTROS FACTORES DE RIESGO',1,1,'C', 1);
		
		$pdf->SetFont('Arial','',$tamanioFuente_6);

		$pdf->Cell(62,4, '4 - Comunicaciones',1,0,'C', 1);
		$pdf->Cell(62,4, '',0,0,'C');
		$pdf->Cell(62,4, '10 - Aprobación Conductor y Vehículo',1,1,'C', 1);


		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,0,'C', 1);

		$pdf->Cell(62,4, '',0,0,'C');

		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,1,'C', 1);

		$pdf->SetFillColor(249, 66, 58);


		$pdf->Cell(54,4, 'Con señal de teléfono celular o satelital/radio',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P34);

		$pdf->Cell(62,4, '',0,0,'C');

		$pdf->Cell(54,4, 'Cumple a cabalidad con el proceso de validación ',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P58);

		$pdf->SetFont('Arial','',$tamanioFuente_4);
		$pdf->Cell(54,4, 'Con señal de teléfono celular  intermitente o zonas muertas ',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P35);
		$pdf->SetFont('Arial','',$tamanioFuente_6);

		$pdf->Cell(62,4, '',0,0,'C');

		$pdf->Cell(54,4, 'Cumple parcialmente con la validación ',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P59);


		$pdf->Cell(54,4, 'Sin comunicación, pero en convoy o caravana ',1,0,'L');
		$pdf->Cell(8,4, '',1,0,'C', $datos->P36);

		$pdf->Cell(62,4, '',0,0,'C');

		$pdf->Cell(54,4, 'No fue validado ',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P60);

		$pdf->Cell(54,4, 'Sin comunicación, sin convoy o caravana ',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P37);

		$pdf->SetFillColor(217, 217, 214);
		$pdf->Cell(62,4, '5 - Condiciones Publicas',1,1,'C', 1);
		$pdf->Cell(54,4, 'ITEM',1,0,'C', 1);
		$pdf->Cell(8,4, 'V',1,1,'C', 1);
		$pdf->SetFillColor(249, 66, 58);

		$pdf->Cell(54,4, 'Presencia de Autoridades sobre la ruta',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P38);

		$pdf->Cell(54,4, 'Incidentes de Orden Publico en la ruta',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P39);

		$pdf->Cell(54,4, 'Bloqueos, paros y/o protestas en la ruta',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P40);

		$pdf->Cell(54,4, 'Presencia de Bandas Criminales sobre la ruta',1,0,'L');
		$pdf->Cell(8,4, '',1,1,'C', $datos->P41);

		$pdf->Ln();

		$pdf->SetFillColor(217, 217, 214);

		$pdf->Cell(0,4, 'CRITERIOS DE APROBACIÓN DEL VIAJE',1,1,'C', 1);

		$pdf->SetFont('Arial','',$tamanioFuente_11);

		$pdf->SetFillColor(0, 204, 0);
		$pdf->Cell(48,8, 'Nivel 1 - Riesgo Bajo',1,0,'C', 1);

		$pdf->SetFillColor(255, 255, 51);
		$pdf->Cell(47,8, 'Nivel 2 - Riesgo Medio',1,0,'C', 1);

		$pdf->SetFillColor(204, 0, 0);
		$pdf->Cell(47,8, 'Nivel 3 - Riesgo Alto',1,0,'C', 1);

		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Cell(48,8, 'Nivel 4 - Riesgo Muy Alto',1,0,'C', 1);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFillColor(217, 217, 214);
		$pdf->SetFont('Arial','',$tamanioFuente_8);

		$pdf->SetXY(80, 45);

		$pdf->Cell(0,4, 'VALORACIÓN DEL RIESGO DEL VIAJE',1,1,'C', 1);
		$pdf->SetFont('Arial','',$tamanioFuente_4);
		$pdf->SetX(80);
		$pdf->Cell(30,4, 'FACTORES FISICOS',1,0,'C', 1);
		$pdf->Cell(30,4, 'FACTORES HUMANOS',1,0,'C', 1);
		$pdf->Cell(30,4, 'OTROS FACTORES DE RIESGO',1,0,'C', 1);
		$pdf->SetFont('Arial','',$tamanioFuente_6);
		$pdf->Cell(30,4, 'TOTAL',1,1,'C', 1);

		$pdf->SetFont('Arial','',$tamanioFuente_11);
		$pdf->SetX(80);
		$pdf->Cell(30,10, $datos->Valoracion_F1,1,0,'C', 0);
		$pdf->Cell(30,10, $datos->Valoracion_F2,1,0,'C', 0);
		$pdf->Cell(30,10, $datos->Valoracion_F3,1,0,'C', 0);

		$pdf->SetTextColor(199, 199, 199);
		if ($datos->Valoracion_Total < 12)
		{
			$pdf->SetFillColor(0, 204, 0);
		} else
		{
			if ($datos->Valoracion_Total < 16)
			{
				$pdf->SetFillColor(255, 255, 51);
			} else
			{
				if ($datos->Valoracion_Total < 25)
				{
					$pdf->SetFillColor(204, 0, 0);
				} else
				{
					$pdf->SetFillColor(0, 0, 0);
				}
			}
		}
		$pdf->Cell(30,10, $datos->Valoracion_Total,1,0,'C', 1);

		$pdf->SetXY(10, 79);
		$pdf->SetFont('Arial','',$tamanioFuente_6);
		$pdf->SetTextColor(0, 0, 0);
		
		$pdf->Cell(48,3, '',0,0,'C');
		$pdf->Cell(48,3, 'Evaluación entre 12 y 15',0,0,'C');
		$pdf->Cell(48,3, 'Evaluación entre 16 - 24',0,0,'C');
		$pdf->Cell(48,3, 'Evaluación >=25',0,1,'C');

		$pdf->Cell(48,3, 'Evaluación  < 12',0,0,'C');
		$pdf->Cell(48,3, 'Aprobación como mínimo del',0,0,'C');
		$pdf->Cell(48,3, 'Aprobación como mínimo del',0,0,'C');
		$pdf->Cell(48,3, 'Gerente de Operaciones del País',0,1,'C');

		$pdf->Cell(48,3, 'Aprobación como mínimo del Supervisor',0,0,'C');
		$pdf->Cell(48,3, 'Gerente del proyecto',0,0,'C');
		$pdf->Cell(48,3, 'Director de Área o Gerente Operaciones País',0,0,'C');
		$pdf->SetTextColor(204, 0, 0);
		$pdf->Cell(48,3, 'SOLO EMERGENCIAS/CONTINGENCIAS',0,1,'C');
		$pdf->SetTextColor(0, 0, 0);

		$pdf->SetDrawColor(0, 0, 0);

		$pdf->Line(10, 89, 200, 89);
		$pdf->Line(10, 79, 10, 89);
		$pdf->Line(200, 79, 200, 89);
		$pdf->Line(58, 79, 58, 89);
		$pdf->Line(105, 79, 105, 89);
		$pdf->Line(152, 79, 152, 89);

		$pdf->Ln();

		$pdf->SetFillColor(217, 217, 214);

		$pdf->SetFont('Arial','B',$tamanioFuente_11);
		$pdf->Cell(0,5,'D. Información Básica de conductores y vehículos',0,1,'C');
		$pdf->SetFont('Arial','',$tamanioFuente_6);

		$pdf->Cell(110,4, 'Nombre del Conductor(es)',1,0,'C', 1);
		$pdf->Cell(40,4, 'Tipo de Vehículo / GPS',1,0,'C', 1);
		$pdf->Cell(40,4, 'Numero de Placas',1,1,'C', 1);

		foreach ($datos->Conductores as $index => $Conductor) 
		{
			if (isset($Conductor->Nombre_del_Conductor))
			{
				if (trim($Conductor->Nombre_del_Conductor) <> "")
				{
					$pdf->Cell(110,4, $Conductor->Nombre_del_Conductor,1,0,'L');
					$pdf->Cell(40,4, $Conductor->Tipo_de_Vehiculo_o_GPS,1,0,'L');
					$pdf->Cell(40,4, $Conductor->Numero_de_Placa,1,1,'L');
				}
			}
		}

		$pdf->Ln();

		$pdf->Cell(140,4, 'Nombre del Pasajero(s)',1,0,'C', 1);
		$pdf->Cell(50,4, 'Número de Contacto',1,1,'C', 1);
		
		foreach ($datos->Pasajeros as $index => $Pasajero) 
		{
			if (isset($Pasajero->Nombre_del_Pasajero))
			{
				if (trim($Pasajero->Nombre_del_Pasajero) <> "")
				{
					$pdf->Cell(140,4, $Pasajero->Nombre_del_Pasajero,1,0,'L');
					$pdf->Cell(50,4, $Pasajero->Numero_de_Contacto,1,1,'L');
				}
			}
		}

		$pdf->Ln();

		$pdf->Cell(30,4, 'Ruta y Puntos de Contacto',1,0,'C', 1);
		$pdf->Cell(30,4, 'Contacto',1,0,'C', 1);
		$pdf->Cell(20,4, 'Hora Estimada',1,0,'C', 1);
		$pdf->Cell(30,4, 'Hora Rea de Contacto',1,0,'C', 1);
		$pdf->Cell(30,4, 'Responsable Control',1,0,'C', 1);
		$pdf->Cell(50,4, 'Comentarios',1,1,'C', 1);

		foreach ($datos->PuntosDeContacto as $index => $PuntoDeContacto) 
		{
			if (isset($PuntoDeContacto->Ruta_y_punto_de_Contacto))
			{
				if (trim($PuntoDeContacto->Ruta_y_punto_de_Contacto) <> "")
				{
					$pdf->Cell(30,4, $PuntoDeContacto->Ruta_y_punto_de_Contacto,1,0,'L');
					$pdf->Cell(30,4, $PuntoDeContacto->Contacto,1,0,'L');
					$pdf->Cell(20,4, $PuntoDeContacto->Hora_Estimada,1,0,'L');
					$pdf->Cell(30,4, $PuntoDeContacto->Hora_Real_de_Contacto,1,0,'L');
					$pdf->Cell(30,4, $PuntoDeContacto->Responsable_Control,1,0,'L');
					$pdf->Cell(50,4, $PuntoDeContacto->Comentarios,1,1,'L');
				}
			}
		}

		$pdf->Cell(0,4, 'Observaciones / Recomendaciones:',1,1,'L', 1);

		$pdf->Multicell(0,4, $datos->P64 ,1,'L');

		$pdf->SetFont('Arial','B',$tamanioFuente_11);
		$pdf->Cell(0,5,'E. Aprobaciones y responsables del viaje',0,1,'C');
		$pdf->SetFont('Arial','B',$tamanioFuente_6);
		

		$pdf->SetX(20);
		$pdf->Cell(80,4, 'APROBACIÓN DEL VIAJE',0,0,'L', 0);
		$pdf->SetX(110);
		$pdf->Cell(80,4, 'CONDUCTOR DEL VIAJE',0,1,'L', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Line(30, $posY, 90, $posY);
		$pdf->Line(120, $posY, 180, $posY);

		$pdf->SetX(30);
		$pdf->Cell(80,4, 'NOMBRE',0,0,'L', 0);
		$pdf->SetX(120);
		$pdf->Cell(80,4, 'NOMBRE',0,1,'L', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Line(30, $posY, 90, $posY);
		$pdf->Line(120, $posY, 180, $posY);

		$pdf->SetX(30);
		$pdf->Cell(80,4, 'N° CONTACTO',0,0,'L', 0);
		$pdf->SetX(120);
		$pdf->Cell(80,4, 'N° CONTACTO',0,1,'L', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Line(30, $posY, 90, $posY);
		$pdf->Line(120, $posY, 180, $posY);

		$pdf->SetX(30);
		$pdf->Cell(80,4, 'FIRMA',0,0,'L', 0);
		$pdf->SetX(120);
		$pdf->Cell(80,4, 'FIRMA',0,1,'L', 0);

		$pdf->Ln();

		$pdf->SetX(20);
		$pdf->Cell(80,4, 'APROBACIÓN DEL VIAJE VÍA TELEFÓNICA',0,0,'L', 0);
		$pdf->SetX(110);
		$pdf->Cell(80,4, 'MONITOR DEL VIAJE',0,1,'L', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Line(30, $posY, 90, $posY);
		$pdf->Line(120, $posY, 180, $posY);

		$pdf->SetX(30);
		$pdf->Cell(80,4, 'NOMBRE',0,0,'L', 0);
		$pdf->SetX(120);
		$pdf->Cell(80,4, 'NOMBRE',0,1,'L', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Line(30, $posY, 90, $posY);
		$pdf->Line(120, $posY, 180, $posY);

		$pdf->SetX(30);
		$pdf->Cell(80,4, 'N° CONTACTO',0,0,'L', 0);
		$pdf->SetX(120);
		$pdf->Cell(80,4, 'N° CONTACTO',0,1,'L', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Line(30, $posY, 90, $posY);
		$pdf->Line(120, $posY, 180, $posY);

		$pdf->SetX(30);
		$pdf->Cell(80,4, 'FIRMA',0,0,'L', 0);
		$pdf->SetX(120);
		$pdf->Cell(80,4, 'FIRMA',0,1,'L', 0);

		$pdf->Ln();

		$pdf->Cell(0,4, 'CIERRE DEL VIAJE',0,1,'C', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Line(30, $posY, 90, $posY);
		$pdf->Line(120, $posY, 180, $posY);

		$pdf->SetX(30);
		$pdf->Cell(80,4, 'HORA',0,0,'L', 0);
		$pdf->SetX(120);
		$pdf->Cell(80,4, 'NOMBRE',0,1,'L', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Line(30, $posY, 90, $posY);
		$pdf->Line(120, $posY, 180, $posY);

		$pdf->SetX(30);
		$pdf->Cell(80,4, 'FECHA',0,0,'L', 0);
		$pdf->SetX(120);
		$pdf->Cell(80,4, 'FIRMA',0,1,'L', 0);

		$pdf->Ln();
		$pdf->Ln();

		$posY = $pdf->GetY();

		$pdf->Image('logo_wsp.png',80,$posY,50);
	}

	$pdf->Output();
?>
	
