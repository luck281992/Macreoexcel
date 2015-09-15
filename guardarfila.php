<?php 
	include("../../include/conexion.php");
	include("../../include/funciones.php");
	include("../../include/bookingact.php");

	//Extrayendo informaci�n a almacenar

	$booking = $_GET["book"];
	$ident = $_GET["identi"];
	$buque = $_GET["buq"];
	$cliente = $_GET["cli"];
	$medida = $_GET["med"];
	$cantidad = $_GET["cant"];
	$obser=$_GET["obser"];
	$destino = $_GET["desti"];
	$tipo=$_GET["tipo"];
	$nuevo=$_GET["nuevo"];
	$idbook=$_GET['idbook'];
	
	$idcli=0;
	$idciu=0;
	$idbuque=0;
	$clavebookdet=0;
	$clavetipo=0;
	$clavebook=0;
	$consul2="";
	$consu="";
		//observacion o nota
		if(empty($obser)){
			$obser="";
		}
		//fecha
		if(empty($ident)){
			$ident="";
		}
		//datos tabla booking
		//cliente
	   if(empty($cliente)){
	   	die("-1");
	   }
			$cons=mysql_query("SELECT clave FROM clientealias WHERE nombre LIKE '$cliente%'",$enlace)OR die("-2");
			$dato=mysql_fetch_array($cons);
			$idclialias=$dato['clave'];

			$cons1=mysql_query("SELECT clave FROM cliente WHERE nombre LIKE '$cliente%'",$enlace)OR die("-2");
			$dato1=mysql_fetch_array($cons1);
			$idcli=$dato1['clave'];

			if(empty($idclialias)||empty($idcli)){
				die("-2");
			}
		
		//destino
		if(empty($destino)){
			$idciu="";
			$idciuali="";
		}else{
			$cons2=mysql_query("SELECT clave FROM ciudadalias WHERE nombre LIKE '$destino%'",$enlace)OR die("-3");
			$dato2=mysql_fetch_array($cons2);
			$idciuali=$dato2['clave'];

			$cons3=mysql_query("SELECT clave FROM ciudad WHERE nombre LIKE '$destino%'",$enlace)OR die("-3");
			$dato3=mysql_fetch_array($cons3);
			$idciu=$dato3['clave'];
			if(empty($idciu)||empty($idciuali)){
				die("-3");
			}
		}
		//buque
		if(empty($buque)){
		  die("-4");
		}
			$consu4=mysql_query("SELECT clave  FROM buque WHERE nombre LIKE '$buque%'",$enlace)OR die("-5");
			$dato4=mysql_fetch_array($consu4);
			$idbuque=$dato4['clave'];
			if(!isset($idbuque)){
				die("-5 ");
			}
		
		
		//contenedor tipo
		if(empty($tipo)){
			$clavetipo="1";
		}
		else{
			$consul6=mysql_query("SELECT clave  FROM contenedortip WHERE abreviacion LIKE '$tipo%'",$enlace)OR die("-6");
			$datos6=mysql_fetch_array($consul6);
			$clavetipo=$datos6['clave'];
			if(empty($clavetipo)){
				die("-6");
			}

		}

			//datos para bookingdetale
			//medida
			if (!empty($medida)) {
				if($medida==20){
					$medida=1;
				}else{$medida=2;} 
			}else{
			   die("-7");	
			}
			//booking
			if(empty($booking)){
				$booking="";
			}
			
			
			//checamos si es un booking nuevo o si actualizaron registro
			// tabla booking
			if($nuevo==0){
				//consulta donde buscaremos el registro booking por medio de los parametros que recibimos de buque,cliente,booking,destino y tipo de contenedores
				$consulta=mysql_query("SELECT booking.clave AS id,clientealias,destinoalias,booking.booking AS booking,identificador ,conmed,contip,cantidad 
				FROM booking INNER JOIN bookingdetalle ON bookingdetalle.booking=booking.clave  
				WHERE booking.clave='".$idbook."'",$enlace)OR die("-8".mysql_error());	
				$datos5=mysql_fetch_array($consulta);

				$consu=mysql_query("UPDATE booking SET booking='$booking',buque='$idbuque',destino='$idciu',destinoalias='$idciuali',observacion='$obser',cliente='$idcli',clientealias='$idclialias',identificador='$ident' WHERE clave='$idbook'",$enlace)OR die('-9' . mysql_error());		
				//modifico la tabla bookingdetalle
				$consul2=mysql_query("UPDATE bookingdetalle SET booking='$idbook',contip='$clavetipo',conmed='$medida',cantidad='$cantidad' WHERE booking='$idbook'",$enlace)OR die('-10' . mysql_error());	
				echo 1;
			}
			if($nuevo==1){
				//consulta donde buscaremos el registro booking por medio de los parametros que recibimos de buque,cliente,booking,destino y tipo de contenedores
				$consulta=mysql_query("SELECT booking.clave AS id,clientealias,destinoalias,booking.booking AS booking,identificador ,conmed,contip,cantidad 
				FROM booking INNER JOIN bookingdetalle ON bookingdetalle.booking=booking.clave  
				WHERE booking.buque='".$idbuque."' AND booking.clientealias='".$idclialias."' AND booking.destinoalias='".$idciuali."' 
				AND booking.booking='".$booking."' AND contip='".$clavetipo."'",$enlace)OR die("-11 ".mysql_error());	
				//insertar solamente los registros que no existan o no sean los mismo
				$datos5=mysql_fetch_array($consulta);
			   	
				if($datos5['clientealias']!=$idclialias){
					$consu=mysql_query("INSERT INTO booking (booking,buque,destino,destinoalias,observacion,cliente,clientealias,identificador) VALUES ('$booking','$idbuque','$idciu','$idciuali','$obser','$idcli','$idclialias','$ident')",$enlace)OR die('-12' . mysql_error());
				 	//saco el id de mi ultima consulta
				 	$idbook=mysql_insert_id();

				 	$consul2=mysql_query("INSERT INTO bookingdetalle (booking,contip,conmed,cantidad) VALUES ('$idbook','$clavetipo','$medida','$cantidad')",$enlace)OR die('-13' . mysql_error());
					echo 1;
				}else{
					die("-15");
				}
			}

?>