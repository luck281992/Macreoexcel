<?php 

//funcion para que me muestre el tipo de error 
function imprimirerror($error){
switch ($error) {

	case 1:
		echo "!No se cargo archivo o no se selecciono¡";
		break;
	case 2:
	  echo "!Se necesita buque en la tabla general¡";
		break;
	case 3:
	  echo "!Se necesita informacion de cliente en la hoja de excel¡";
		break;
	case 4:
	  echo "!Se necesita informacion de cantidad en la hoja de excel¡";
		break;
	case 5:
	  echo "!No existe informacion de buque en la bd";
		break;
	case 6:
	  echo "!No existe informacion de cliente en la bd";
		break;
	case 7:
	  echo "!No existe informacion de destino en la bd";
		break;
	case 8:
	  echo "!No existe informacion de tipo en la bd";
		break;
  }
}
//funcion para guardar los datos de un archivo excel a la base de datos
//usuario: Jesus Javier Sanchez Guerrero
//Fecha: 29/08/2014
//resumen
//para la tabla normal
//parametros: Se envia el id del usuario para al momento de insertar los registros en la base de datos
//sepamos que usuario lo hizo.
//copio el archivo  a uno temporal, checo que hayamos seleccionado 
//un archivo, leemos el archivo y comparamos los datos del archivo de excel con los de 
//mi base de datos con validaciones comunes por medio de un ciclo si esta todo bien 
//guardamos en nuestra base de datos 
// ahora comparar los datos de mi base de datos con los de excel, esto por si borran
//o eliminan un registro, tambien para actualizar dicho registro
// para que tambien la eliminemos en nuestra base de datos para la tabla resumen
//leemos si hay datos por medio de un ciclo, y hacemos validaciones comunes 
//despues comparo si los totales de contenedores de la tabla resumenes son los mismos que los de mi 
//tabla normal si son los mismos queda asi, si es menor se le agrega la cantidad restante
//a los totales, si es mayor se le agrega a la base de datos un nuevo registro en blanco 
//con la cantidad restante
//Por ultimo se hace un reporte de los registros que se efectuaron e ingresandolos en la base de datos
function upload($idusu){
	include("../../include/conexion.php");

	//el script se ejecutara hasta terminar el proceso, por si el proceso es muy largo 
 		 set_time_limit(0);
				
				$archivo = $_FILES['excel']['name'];
				$destino = "bak_".$archivo;
					 //checo que exista o este seleccionado el archivo para copiarlo 
				if(!empty($archivo)){
					copy($_FILES['excel']['tmp_name'],$destino);
				}else{
				  imprimirerror(1);
				  die(1);
				}

			require_once("../../include/excel/PHPExcel.php");
			require_once("../../include/excel/PHPExcel/Reader/Excel2007.php");

			// Cargando la hoja de cálculo
		    $objReader = new PHPExcel_Reader_Excel2007();
			$objPHPExcel = $objReader->load($destino);
			$objFecha = new PHPExcel_Shared_Date();
			// Asignar hoja de excel activa
			$objPHPExcel->setActiveSheetIndex(0);

	    //checamos que tenga informacion del buque ya que es informacion necesaria   
		$buque = $objPHPExcel->getActiveSheet()->getCell('H4')->getCalculatedValue();
	    if(empty($buque))
	    {	
	    	unlink($destino);
	    	imprimirerror(2);
			die(1);
	    }
	    	//hacemos na consulta para encontrar el buque que nos da excel e imprimirlo 
			// por clave y  sacamos valor de buque para compararlo con el de excel mas adelante
			$buque= strtoupper($buque);
			$consulta=mysql_query("SELECT nombre,clave FROM buque WHERE nombre = '$buque' ",$enlace)OR die("-1 buque".mysql_error());
			$dato=mysql_fetch_array($consulta);
			if(!empty($dato['clave'])){
			$idbuque=$dato['clave'];
			}else{
				unlink($destino);
				imprimirerror(5);
				die(1);
			}
					// Llenamos el arreglo con los datos  del archivo xlsx  
					     $datos=array();
					     $i=9;
					     $cliente="";
					     $desti="";
					     $tipo=0;
					     $pos=0;
					     $total20=0;
						 $total40=0;
						 $campoobligatorio=0;
					
						//hago el ciclo hasta que ya no haya datos en mi hoja de excel
				while($objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue()!=NULL)
				{
					$pos++;
						//guardo en el arreglo lo que imprimo en la tabla booking
						$datos[$pos]['booking'] = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
						if(empty($datos[$pos]['booking']))
						$datos[$pos]['booking']="";

						$datos[$pos]['buque'] = $idbuque;
				    	//ocupamos saber la clave del destino, y para saber la ciudad de la base de datos
				    	$desti = $objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
				    	if(!empty($desti)){
	
					    	$desti= strtoupper($desti);
					    	$consuldest=mysql_query("SELECT clave, nombre FROM ciudad WHERE nombre = '$desti' ",$enlace)OR die("-2 tabla general".mysql_error());
					        $datodest=mysql_fetch_array($consuldest);
					       
					        if(!empty($datodest['clave'])){
					    	$datos[$pos]['destino'] = $datodest['clave'];	
					    	}else {
					    		unlink($destino);
	    						imprimirerror(7);
	    						die(1);
	    					}     
					    	//ocupamos saber la clave del destinoalias , y para saber destino de la base de datos
					    	$conl=mysql_query("SELECT clave, nombre FROM ciudadalias WHERE clave ='".$datodest['clave']."' ",$enlace)OR die("-3 tabla general".mysql_error());
					        $das1=mysql_fetch_array($conl);
					        //checar si existe si no imprimir error
					        if(!empty($das1['clave'])){
					        $datos[$pos]['destinoalias'] = $das1['clave'];
	    					}else {
	    						unlink($destino);
	    						imprimirerror(7);
	    						die(1);
	    					} 
    					}else{
    					   $datos[$pos]['destino']=0;
    					   $datos[$pos]['destinoalias']=0;
    					}

				   	 	$datos[$pos]['observacion'] = $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue(); 
				 		if(empty($datos[$pos]['observacion']))
						$datos[$pos]['observacion']="";

				    	//ocupamos saber la clave del cliente y para saber cliente de la base de datos
				    	$cliente = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
				        if(empty($cliente)){
				        	unlink($destino);
							imprimirerror(3);
							die(1);
				     	}
				     	
				        $cliente= strtoupper($cliente);
				    	$consul=mysql_query("SELECT clave,nombre FROM cliente WHERE nombre = '$cliente'",$enlace)OR die("-4 tabla general".mysql_error());
				        $dat2=mysql_fetch_array($consul);
				        if(!empty($dat2['clave'])){
				         $datos[$pos]['cliente'] =$dat2['clave'];
				        }
				        else {
				        	unlink($destino);
    						imprimirerror(6);
    						die(1);
    					} 
				        $consuls=mysql_query("SELECT clave,nombre FROM clientealias WHERE clave ='".$dat2['clave']."' ",$enlace)OR die("-5 tabla general".mysql_error());
				        $dats2=mysql_fetch_array($consuls);
				    	if(!empty($dats2['clave'])){
				    	 $datos[$pos]['clienteali'] =$dats2['clave'];
				        }
				        else {
				        	unlink($destino);
    						imprimirerror(6);
    						die(1);
    					} 
				     		   
				    	$datos[$pos]['identificador'] = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
						if(empty($datos[$pos]['identificador']))
							$datos[$pos]['identificador']="";

						$tipo= $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
						if(empty($tipo)){
						 //contenedor tipo por defaul D 
						 $tipo ="d";
						}
						$tipo= strtoupper($tipo);
						$query=mysql_query("SELECT abreviacion,clave FROM contenedortip WHERE abreviacion = '$tipo'",$enlace)OR die("-6 tabla general".mysql_error()); 
						$dats=mysql_fetch_array($query);
						if(!empty($dats['clave']))
						$datos[$pos]['contip'] = $dats['clave'];
						else {
							unlink($destino);
							imprimirerror(8);
							die(1);
						}
						
						//checo cantidad de contenedores
						if($objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue()!=NULL)
						{
							$datos[$pos]['conmed20'] = 1;
							$datos[$pos]['cant20']=$objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
							if(empty($datos[$pos]['cant20'])){
								unlink($destino);
							  	imprimirerror(4);
								die(1);
							}
						  //saco total de 20 de mi tabla general
							$total20+=$datos[$pos]['cant20'];
						}
					   if($objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue()!=NULL)
						{

							$datos[$pos]['conmed40'] = 2;
							$datos[$pos]['cant40']=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
							if(empty($datos[$pos]['cant40']))
							 {
							 	unlink($destino);
							 	imprimirerror(4);
								die(1);
							 }	

							//saco total de 40 de mi tabla general
						    $total40+=$datos[$pos]['cant40'];
						}
					$i++;
				}
				$insertbd=0;
				$actnum=0;
				$band=0;
				//insertar en la tabla booking y bookindetalle
				for ($a=1; $a <=count($datos) ; $a++)
				{ 
				    	//comparo los datos de excel con los de mi base de datos
					    $consulta="SELECT booking.clave AS idbok,clientealias,destinoalias,booking.booking AS booking,identificador ,conmed,contip,cantidad 
						FROM booking INNER JOIN bookingdetalle ON bookingdetalle.booking=booking.clave  
						WHERE booking.buque='".$idbuque."' AND clientealias='".$datos[$a]['clienteali']."' 
						AND booking.booking ='".$datos[$a]['booking']."' AND destinoalias='".$datos[$a]['destinoalias']."' 
						AND contip='".$datos[$a]['contip']."'";
						if($datos[$a]['conmed20']==1){
							$consulta2="AND conmed='".$datos[$a]['conmed20']."'";
						}
						else if($datos[$a]['conmed40']==2){
							$consulta2="AND conmed='".$datos[$a]['conmed40']."'";
						}				
							$consulta=$consulta.$consulta2;
						    $tabla = mysql_query($consulta,$enlace) or die("7 Error al General la consulta.".mysql_error());
							$datobd2=mysql_fetch_array($tabla);
							$idbok=$datobd2['idbok'];
				
						//si es el mismo registro de la hoja de excel con el de la base de datos actualizamos registro
						if(mysql_num_rows($tabla)>0){
								if($datos[$a]['conmed20']==1)
								{

									$actnum++;
									$upda=mysql_query("UPDATE booking SET booking='".$datos[$a]['booking']."',buque='".$datos[$a]['buque']."',destino='".$datos[$a]['destino']."',destinoalias='".$datos[$a]['destinoalias']."',observacion='".$datos[$a]['observacion']."',cliente='".$datos[$a]['cliente']."',clientealias='".$datos[$a]['clienteali']."',identificador='".$datos[$a]['identificador']."' WHERE clave='".$idbok."'",$enlace)OR die("-8 update tabla general".mysql_error());
					   			    //si es el mismo cliente y tiene la misma cantidad
					   			    $upda=mysql_query("UPDATE bookingdetalle SET booking='".$idbok."',contip='".$datos[$a]['contip']."',conmed='".$datos[$a]['conmed20']."',cantidad='".$datos[$a]['cant20']."' WHERE booking='".$idbok."'",$enlace)OR die("-9 update tabla general".mysql_error());
					   			 
								}
								else if($datos[$a]['conmed40']==2){

									$actnum++;
									$upda=mysql_query("UPDATE booking SET booking='".$datos[$a]['booking']."',buque='".$datos[$a]['buque']."',destino='".$datos[$a]['destino']."',destinoalias='".$datos[$a]['destinoalias']."',observacion='".$datos[$a]['observacion']."',cliente='".$datos[$a]['cliente']."',clientealias='".$datos[$a]['clienteali']."',identificador='".$datos[$a]['identificador']."' WHERE clave='".$idbok."'",$enlace)OR die("-8 update tabla general".mysql_error());
				   			    	//si es el mismo cliente y tiene la misma cantidad
				   				    $upda=mysql_query("UPDATE bookingdetalle SET booking='".$idbok."',contip='".$datos[$a]['contip']."',conmed='".$datos[$a]['conmed40']."',cantidad='".$datos[$a]['cant40']."' WHERE booking='".$idbok."'",$enlace)OR die("-9 update tabla general".mysql_error());
				   				}
						}else{
							//si no es el mismo cliente insertar 
								 //si medida es 20
								if($datos[$a]['conmed20']==1)
								{
									   $insertbd++;
									   $insert1=mysql_query("INSERT INTO booking (booking,buque,destino,destinoalias,observacion,cliente,clientealias,identificador) VALUES ('".$datos[$a]['booking']."','".$datos[$a]['buque']."','".$datos[$a]['destino']."','".$datos[$a]['destinoalias']."','".$datos[$a]['observacion']."','".$datos[$a]['cliente']."','".$datos[$a]['clienteali']."','".$datos[$a]['identificador']."')",$enlace)OR die("-10 insert tabla general".mysql_error()); 		
										//saco el ultimo id que se inserto para agregarlo al bookingdetalle
							           $idbok=mysql_insert_id();
							         
							           $insert2=mysql_query("INSERT INTO bookingdetalle (booking,contip,conmed,cantidad) VALUES ('".$idbok."','".$datos[$a]['contip']."','".$datos[$a]['conmed20']."','".$datos[$a]['cant20']."')",$enlace)or die("-11 insert tabla general".mysql_error());			 						
								}
								//si medida es 40
        						if($datos[$a]['conmed40']==2){
        							  $insertbd++;
									  $insert1=mysql_query("INSERT INTO booking (booking,buque,destino,destinoalias,observacion,cliente,clientealias,identificador) VALUES ('".$datos[$a]['booking']."','".$datos[$a]['buque']."','".$datos[$a]['destino']."','".$datos[$a]['destinoalias']."','".$datos[$a]['observacion']."','".$datos[$a]['cliente']."','".$datos[$a]['clienteali']."','".$datos[$a]['identificador']."')",$enlace)OR die("-10 insert tabla general".mysql_error()); 		
									  //saco el ultimo id que se inserto para agregarlo al bookingdetalle
							          $idbok=mysql_insert_id();
							          
							          $insert2=mysql_query("INSERT INTO bookingdetalle (booking,contip,conmed,cantidad) VALUES ('".$idbok."','".$datos[$a]['contip']."','".$datos[$a]['conmed40']."','".$datos[$a]['cant40']."')",$enlace)or die("-11 insert tabla general".mysql_error());			 						
							
        						}
        					
						 }
			   }

			   //Compara los datos de mi base de datos con los de excel 
			   //esto por si borrar un registro en excel tambien lo borremos en nuestra base de datos
			$cont=0;
			$registrodb=mysql_query("SELECT booking.clave AS idbok,buque,booking.booking AS booking,destino,cliente,clientealias,observacion,destinoalias,identificador,conmed,contip,cantidad 
				FROM booking INNER JOIN bookingdetalle ON bookingdetalle.booking=booking.clave  
				WHERE buque = '".$idbuque."' ORDER BY clientealias, destinoalias,contip",$enlace)OR die("-12 consulta tabla general ".mysql_error());	
			   for ($a=1; $a<=mysql_num_rows($registrodb); $a++) 
			   {
			   	   $encontro=0;
			       $datosbd=mysql_fetch_array($registrodb);
			       $idbook=$datosbd['idbok']; 
			    	//busco cada registro de mi consulta en todos los datos de mi excel 
			   		for ($i=1;$i<=count($datos); $i++) 
			   		{   
			   			//si lo encuentro variable encontro es igual a 1 se borra
			   			//si es el mismo pero con cantidad diferente de 20	  
			   			if($datosbd['conmed']==1){ 				
				   			if(isset($datos[$i]['cant20'])){
					   			
					   			if($datos[$i]['clienteali']==$datosbd['clientealias']&&$datos[$i]['contip']==$datosbd['contip']&&
					   			$datos[$i]['booking']==$datosbd['booking']&&$datos[$i]['destinoalias']==$datosbd['destinoalias']
					   			&&$datos[$i]['cant20']==$datosbd['cantidad'])
					   			{
					   				$encontro=1;	
					   			}
					   		  }
				   		}else{
				   			if(isset($datos[$i]['cant40'])){
					   		//si es el mismo pero con cantidad diferente de 40	
					   			if($datos[$i]['clienteali']==$datosbd['clientealias']&&$datos[$i]['contip']==$datosbd['contip']&&
					   			$datos[$i]['booking']==$datosbd['booking']&&$datos[$i]['destinoalias']==$datosbd['destinoalias']
					   			&&$datos[$i]['cant40']==$datosbd['cantidad'])
					   			{
					   				$encontro=1;	
					   			}
				   			}
				   		}
			   			
			   		}
			   		if($encontro==0){
			   		 	$cont++;
						$borrar=mysql_query("DELETE FROM bookingdetalle WHERE booking='".$idbook."'",$enlace)OR die("-13 delete tabla general".mysql_error());		
			   			//consulto si no quedan registros de ese booking en la tabla booking detalle
			   			$consulta=mysql_query("SELECT * FROM bookingdetalle WHERE booking='".$idbook."' ",$enlace)OR die("-14  tabla general".mysql_error());			
			   			if(mysql_num_rows($consulta)==0){
			   			 //si ya no hay registros en la tabla booking detalle elimino el el registro en la tabla booking
			   			 $borrar=mysql_query("DELETE FROM booking WHERE clave='".$idbook."'",$enlace)OR die("-15 delete tabla general".mysql_error());				
			   			}
			 		
			   		}
			   }

					//leemos tabla resumen
					$a=4;
					$minicli="";
					$minitotal=0;
				    $minitotal2=0;
				    $minitipo="";
					$miniresumen=array();
					$pos=0;
					//saco los datos de mi hoja de excel los datos de la tabla resumen, 
					//y los voy validando para ver si hay informacion necesaria, si existen 
					//busco que existan en sus tablas de la base de datos
					while($objPHPExcel->getActiveSheet()->getCell('A'.$a)->getCalculatedValue()!=NULL)
					{
				       $pos++;
						//cliente
						$minicli = $objPHPExcel->getActiveSheet()->getCell('A'.$a)->getCalculatedValue();
				       if(empty($minicli)){
				       	unlink($destino);
				       	imprimirerror(3);
						die(1);
				       }

				        $minicli= strtoupper($minicli);
				    	$consuldb1=mysql_query("SELECT clave,nombre FROM cliente WHERE nombre = '$minicli' ",$enlace)OR die("-1 tabla resumen".mysql_error()); 
				        $dato1=mysql_fetch_array($consuldb1);
				 
				        if(!empty($dato1['clave'])){
				     	$miniresumen[$pos]['cliente']=$dato1['clave'];
				     	}else{
				     		unlink($destino);
				     		imprimirerror(6);
				     		die(1);
				     	}
				     	//clientealias
				     	
				     	$condb=mysql_query("SELECT clave,nombre FROM clientealias WHERE clave = '".$dato1['clave']."' ",$enlace)OR die("-2 tabla resumen".mysql_error()); 
				        $datodb3=mysql_fetch_array($condb);
				       
				        if(!empty($datodb3['clave'])){
				    	$miniresumen[$pos]['clientealias']=$datodb3['clave'];
				    	}else{
				    		unlink($destino);
				     		imprimirerror(6);
				     		die(1);
				     	}
					    //contenedores
					    $minitipo=$objPHPExcel->getActiveSheet()->getCell('D'.$a)->getCalculatedValue();
					    if(empty($minitipo)){
				       	$minitipo="d";
				       }
					    $minitipo=strtoupper($minitipo);
					    $constipo=mysql_query("SELECT abreviacion,clave FROM contenedortip WHERE abreviacion = '$minitipo'",$enlace)OR die("-3 tabla resumen".mysql_error()); 						
				    	$datbd=mysql_fetch_array($constipo);
				    	if(!empty($datbd['clave'])){
				    	$miniresumen[$pos]['contip']=$datbd['clave'];
				    	}
				    	else{
				    		unlink($destino);
				    		echo "clave".$datbd['clave'];
				     		imprimirerror(8);
				     		die(1);
				     	}

					    $miniresumen[$pos]['conmed']=0;
				        $miniresumen[$pos]['cont20']=$objPHPExcel->getActiveSheet()->getCell('B'.$a)->getCalculatedValue();
				    	$miniresumen[$pos]['cont40']=$objPHPExcel->getActiveSheet()->getCell('C'.$a)->getCalculatedValue();		
						if(empty($miniresumen[$pos]['cont20'])&&empty($miniresumen[$pos]['cont40'])){
							unlink($destino);
							imprimirerror(4);
							die(1);
						}
								
						$a++;
					}
					
					$cantdiferencia=0;
					//ya tengo los datos en mi arreglo empiezo a leer mi arreglo 
					for ($i=1; $i <=count($miniresumen) ; $i++) {
								//sacamos el id del booking 
							$consulta=mysql_query("SELECT booking.booking AS clave, clientealias,contip,cantidad
							FROM booking INNER JOIN bookingdetalle ON bookingdetalle.booking=booking.clave
						    WHERE clientealias='".$miniresumen[$i]['clientealias']."' AND contip='".$miniresumen[$i]['contip']."'",$enlace)OR die("-4 consulta tabla resumen".mysql_error()); 
							$datosb=mysql_fetch_array($consulta);
							//saco los totales
							$minitotal+=$miniresumen[$i]['cont20'];	
							$minitotal2+=$miniresumen[$i]['cont40'];
								//comparar las cantidades totales de contenedores de excel con las cantidades totales de la base de datos 
								//si la tabla resumen es mayor que la tabla general insertar un registro con los datos de tabla resumen 
								//esto para que tengan los mismo totales en la bd
								if($minitotal>$total20){
									$insertbd++;
									//checamos cual cantidad ingreso para insertarla en la tabla bookingdetalle
									if(!empty($miniresumen[$i]['cont20'])){
									 $insert1=mysql_query("INSERT INTO booking (buque,cliente,clientealias) VALUES ('".$idbuque."','".$miniresumen[$i]['cliente']."','".$miniresumen[$i]['clientealias']."')",$enlace)OR die("-9 insert tabla resumen".mysql_error()); 		
									//sacamos el id de la ultima consulta para insertarlo en bookingdetalle
									$idboki=mysql_insert_id();
							
									//saco la diferencia y la inserto para que sean iguales ya 
									$cantdiferencia=$minitotal-$total20;
									$miniresumen[$i]['conmed']=1;
									//insertamos en la tabla bookingdetalle
									
									$insermini=mysql_query("INSERT INTO bookingdetalle (booking,contip,conmed,cantidad) VALUES ('".$idboki."','".$miniresumen[$i]['conmed']."','".$miniresumen[$i]['conmed']."','".$cantdiferencia."')",$enlace)or die("-10 insert tabla resumen".mysql_error());			 						
									}
								}
								if($minitotal2>$total40){
									if(!empty($miniresumen[$i]['cont40'])){
									$insertbd++;
									$insert1=mysql_query("INSERT INTO booking (buque,cliente,clientealias) VALUES ('".$idbuque."','".$miniresumen[$i]['cliente']."','".$miniresumen[$i]['clientealias']."')",$enlace)OR die("-9 insert tabla resumen".mysql_error()); 		
									//sacamos el id de la ultima consulta para insertarlo en bookingdetalle
									$idboki=mysql_insert_id();
									//saco la diferencia y la inserto para que sean iguales ya 
									$cantdiferencia=$minitotal2-$total40;
									$miniresumen[$i]['conmed']=2;
									$insermini=mysql_query("INSERT INTO bookingdetalle (booking,contip,conmed,cantidad) VALUES ('".$idboki."','".$miniresumen[$i]['contip']."','".$miniresumen[$i]['conmed']."','".$cantdiferencia."')",$enlace)or die("-11 insert tabla resumen".mysql_error());			 						
									}
								}
					}
	//insertar en tabla registro 
	$sql=mysql_query("INSERT INTO registro_operacion(id_usuario,detalle,agregar,actualizar,borrar)values('".$idusu."','".$sql."','".$insertbd."','".$actnum."','".$cont."');",$enlace)or die("-12 insert registro".mysql_error());
	//reporte de lo que se registro
	echo "<h3> Reporte de registros</h3>";
	echo "Se importo \n $insertbd registros correctamente";
	echo "<br/> Se Actualizaron \n $actnum registros correctamente";
	echo "<br/> Se borraron \n $cont registros correctamente";

	//eliminamos el archivo temporal	
	unlink($destino);
}

//funcion comparar para ver si ya esta enbarcado, si ya esta lleno , si esta vacio el contenedor etc..
//Usuario: Jesus Javier Sanchez Guerrero
//Fecha: 29/08/2014
//resumen
//parametros:
//Buque, cliente, Destino, Tipo Contenedor,Booking, cantidad.
//Realizará una consulta entre las tablas de booking comparándolas con los contenedores embarcados.
//Regresá uno de los siguientes valores(estado de booking):
//Comparando las cantidades de contenedores en booking contra las cantidades de contenedores en embaques
//0 Si son iguales (Booking lleno)
//1 Si son mayores (Booking parcialmente lleno)
//3 Si son menores (Booking sobre embarcado)
//4 Si la cantidad de embarcado es 0 (Booking Vacio)
function comparar($buq,$cli,$book,$tipo,$destino,$cantidad)							
{
	//el script se ejecutara hasta terminar el proceso, por si el proceso es muy largo 
 		 set_time_limit(0);
	include("../../include/conexion.php");
	$a=array();
	$Filtro="";
	//lo comparas con lo que tienes en embarque 
  if(isset($buq))
	{
		if($buq!=""){
			if($Filtro!="") $Filtro=$Filtro." AND ";
			$Filtro = $Filtro."buq.nombre LIKE '%".$buq."%'";	
		}
	}
	if(isset($cli))
	{
		if($cli!=""){
			if($Filtro!="") $Filtro=$Filtro." AND ";
			$Filtro = $Filtro."cli.nombre LIKE '%".$cli."%'";	
		}
	}
	if(isset($tipo))
	{
		if($tipo!=""){
			if($Filtro!="") $Filtro=$Filtro." AND ";
			$Filtro = $Filtro."contip.abreviacion LIKE '%".$tipo."%'";	
		}
	}
	if(isset($destino))
	{
		if($destino!=""){
			if($Filtro!="") $Filtro=$Filtro." AND ";
			$Filtro = $Filtro."ciudes.nombre LIKE '%".$destino."%'";	
		}
	}
	
	if($Filtro!=""){
		$Filtro=" WHERE ".$Filtro;
	}
   //consulta de embarque
   $consulta = "SELECT COUNT(contip.nombre) AS cantcont, cli.nombre AS clinom,buq.nombre AS buqnom, boldet.booking AS boldetbook,
				ciudes.nombre AS ciudenom,contest.nombre AS contesnom,contip.abreviacion AS contipnom
				FROM boletadetalle AS boldet 
					INNER JOIN boleta AS bol ON boldet.boleta = bol.clave
					INNER JOIN boletaaplicacion AS bolapl ON boldet.clave = bolapl.boletadet
					LEFT JOIN buque AS buq ON boldet.buque = buq.clave
					LEFT JOIN cliente AS cli ON bol.cliente = cli.clave
					LEFT JOIN producto AS pro ON boldet.producto = pro.clave
					LEFT JOIN productovar AS provar ON boldet.variedad = provar.clave
					LEFT JOIN productocalalias AS procalali ON boldet.calibrealias = procalali.clave
					LEFT JOIN productoemp AS proemp ON boldet.empaque = proemp.clave
					LEFT JOIN contenedor AS con ON boldet.contenedor = con.clave
					LEFT JOIN contenedorest AS contest ON boldet.contenedorest = contest.clave
					LEFT JOIN contenedortip AS contip ON con.tipo = contip.clave
					LEFT JOIN contenedormed AS conmed ON con.medida = conmed.clave
					LEFT JOIN consignadoalias AS conali ON boldet.consignadoalias = conali.clave
					LEFT JOIN ciudad AS ciudes ON boldet.destino = ciudes.clave
					LEFT JOIN flete AS fle ON bolapl.flete = fle.clave	
					
					 $Filtro AND boldet.estado>0 AND bol.tipo=0 
					GROUP BY contip.nombre, boldetbook
					ORDER BY buqnom,clinom,boldetbook";
		
		//saber a que comparar para terminar 
	    $tabla = mysql_query($consulta,$enlace) or die("Error al General la consulta.\n$consulta");
			 $data=mysql_fetch_array($tabla);
		    //comparamos las cantidades de contenedores de booking con las cantidades de contenedores de embarque
		        //Booking lleno
			    if($data['cantcont']==$cantidad){
			    	$a[0]=0;
			    	$a[1]="Booking lleno";
			    }
			    //parcialmente lleno
			    if($cantidad<$data['cantcont']){
			    	$a[0]=1;
			    	$a[1]="Booking parcialmente lleno";
			    }else{
			    //Sobre embarcado
			    	$a[0]=3;
			    	$a[1]="Booking Sobre Embarcado";
			    }	  
			    	//Booking Vacio
			    if($data['cantcont']==0){
			    	$a[0]=4;
			    	$a[1]="Booking Vacio";
			    }
 
  return $a;
}
//funcion para generar archivos excel de la base de datos mediante buque
//usuario: Jesus Javier Sanchez Guerrero
//Fecha: 29/08/2014
//resumen 
//esta funcion recibe un parametro, el nombre del buque con el cual buscara
//todos los registros que tiene ese buque en la tabla booking, si no tiene registros te descargara
//un archivo excel en blanco unicamente con el nombre de ese buque y su fecha.
//abrimos el archivo base de excel que tenemos en temporal e imprimimos
//los datos de la tabla resumen y de la tabla normal por medio de ciclos
//para la tabla resumen cree una consulta donde agrupo por nombre,cantidad de contenedor y por tipo de contenedor
//para la tabla general creo otra consulta y al imprimir los datos
//en cada registro mando a llamar la funcion de comparar, que me devuelve
//un arreglo con el estado del booking   
function generar($buq)
{
  //conectamos con la base de datos
  include("../../include/conexion.php");
  //el script se ejecutara hasta terminar el proceso, por si el proceso es muy largo 
  set_time_limit(0);
  $status=array();

  $Filtro="";
	if (isset($buq)) {
		if($buq!=""){
			if($Filtro!="") $Filtro=$Filtro." AND ";
			$Filtro = $Filtro."buq.nombre LIKE '%".$buq."%'";	
		}
	}

	$Filtro=" WHERE ".$Filtro;
				//consulta de los datos de booking que guardamos en nuestra base de datos
				$consulta="SELECT book.clave AS id,cli.nombre AS clinom,contenedortip.abreviacion AS contip, book.booking AS booking, buq.nombre AS buqnom, book.identificador AS bookiden,
				book.observacion AS bookobser,ciudes.nombre AS ciudenom, bookdet.conmed AS bookdetmed,bookdet.cantidad AS bookdetcan";
	
				$consultaf=" FROM booking AS book
					INNER JOIN bookingdetalle AS bookdet ON bookdet.booking=book.clave
					LEFT JOIN contenedortip  ON bookdet.contip = contenedortip.clave 
					LEFT JOIN buque AS buq ON book.buque = buq.clave
					LEFT JOIN cliente AS cli ON book.clientealias = cli.clave
					LEFT JOIN ciudad AS ciudes ON book.destinoalias = ciudes.clave";

			    	$consulta=$consulta.$consultaf.$Filtro."
					ORDER BY id,buqnom,clinom,booking";
	    $tabla = mysql_query($consulta,$enlace) or die("Error al General la consulta.$consulta");
		// si no existe el buque en nuestra tabla booking y bookingdetalle no te genera archivo
							//clase de excel
							require_once("../../include/excel/PHPExcel.php");
							require_once("../../include/excel/PHPExcel/Writer/Excel2007.php");

							$objPHPExcel = new PHPExcel();
							// Cargando la hoja de cálculo para meter los datos
							require_once("../../include/excel/PHPExcel/Reader/Excel2007.php");
						    $objReader = new PHPExcel_Reader_Excel2007();
						    $objFecha = new PHPExcel_Shared_Date();
							$objPHPExcel = $objReader->load("..\..\..\\temporal\agencia.xlsx");
							//trabajamos con la primera hoja de exel
							$objPHPExcel->setActiveSheetIndex(0);
							 
							//algunos datos sobre autor
							$objPHPExcel->getProperties()->setCreator("Agencias");
							$objPHPExcel->getProperties()->setLastModifiedBy("Agencias");
							$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX tabla excel");
							$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX tabla excel");
							$objPHPExcel->getProperties()->setDescription("tabla excel Office 2007 XLSX, Usando PHPExcel.");
					
					//consulta para la mini tabla
						$consul="SELECT  booking.clave AS id, cliente.nombre AS clinomb, contenedortip.abreviacion AS contip,bookingdetalle.cantidad AS cantidad,bookingdetalle.conmed AS medida
						FROM booking 
						INNER JOIN bookingdetalle ON bookingdetalle.booking=booking.clave 
						LEFT JOIN contenedortip  ON bookingdetalle.contip = contenedortip.clave
						LEFT JOIN buque AS buq ON booking.buque = buq.clave
						LEFT JOIN cliente ON booking.cliente = cliente.clave  ".$Filtro."  ORDER BY id,clinomb";
						$tabla2 = mysql_query($consul,$enlace) OR die("error al generar consulta\n $consul");
						
						//saber fecha
						date_default_timezone_set('America/Mazatlan');
        		  		$fecha = date("Y-w-j H:i:s ");
        		
        		  		$cliente="";
		 				$clientei=1;
		 				$tip=1;
		 				$tipco="";
						//imprimir mini tabla
						$a=4;
						$pos=0;
						
						while ($data2=mysql_fetch_array($tabla2)) {
						   //datos para la minitabla
							 $cli=$data2['clinomb'];
							 $conmed=$data2['medida'];
							 $cantidad=$data2['cantidad'];
							 $tipo=$data2['contip'];
							
							 $status=comparar($buq,$cli,"",$tipo,"",$cantidad);

							if($cliente!=$cli|| $clientei==1||$tipco!=$tipo||$tip==1) //para saber si se repite el cliente
							{ 
							   //tenemos 13 filas en la minitabla 
							   //si se ocupan mas filas de las que tenemos crearlas y seguir imprimiendo
							   if($a>=12){
							   	$objPHPExcel->getActiveSheet()->insertNewRowBefore($a, 1);	
								}

								$clientei=0;
								$cliente=$cli;
								$pos=$a;
								$objPHPExcel->getActiveSheet()->SetCellValue("A".$a,$cli);
								   	
							}  
								$tip=0;
								$tipco=$tipo;
								$a=$pos;
								$objPHPExcel->getActiveSheet()->SetCellValue("D".$a,$tipo); 
									
						  	  //si no es el mismo cliente 
								$a=$pos;
								if($conmed==1){
								$objPHPExcel->getActiveSheet()->SetCellValue("B".$a,$cantidad);
								}else{
							    $objPHPExcel->getActiveSheet()->SetCellValue("C".$a,$cantidad);
								}
								     //status del booking tabla resumen
							 //imprimir Booking Lleno
							 if($status[0]==0){
							  $objPHPExcel->getActiveSheet()->SetCellValue("E".$a,$status[1]);
							  $objPHPExcel->getActiveSheet()->getStyle('E'.$a)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'00FFFF')));			
							 }
							  //parcialmente lleno
				    	     if($status[0]==1){
				    	      $objPHPExcel->getActiveSheet()->SetCellValue("E".$a,$status[1]);
					          $objPHPExcel->getActiveSheet()->getStyle('E'.$a)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'00FFFF')));				
				    	     }
				    	     //Sobre embarcado
							 if($status[0]==3){
							  $objPHPExcel->getActiveSheet()->SetCellValue("E".$a,$status[1]);
							  $objPHPExcel->getActiveSheet()->getStyle('E'.$a)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'00FFFF')));				
							 }

							  //Booking Vacio
							 if($status[0]==4){
							  $objPHPExcel->getActiveSheet()->SetCellValue("E".$a,$status[1]);
							  $objPHPExcel->getActiveSheet()->getStyle('E'.$a)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'00FFFF')));				
							 }
										
							 $a++;
						}
				
						//imprimir tabla

						$i=9;
						//imprimir fuera del ciclo el buque y la fecha en que se genero archivo
						 $objPHPExcel->getActiveSheet()->SetCellValue("H4",$buq);
						 $objPHPExcel->getActiveSheet()->SetCellValue("M4",$fecha);
						while($data=mysql_fetch_array($tabla))
						{
					 		//datos tabla
						   
						  	$cliente=$data['clinom'];
							$book=$data['booking'];
							$med=$data['bookdetmed'];
							$obser=$data['bookobser'];
							$destino=$data['ciudenom'];
							$identificador=$data['bookiden'];
							$cant=$data['bookdetcan'];
							$tipocont=$data['contip'];
							//tenemos 46 filas en la tabla 
							//si se ocupan mas filas de las que tenemos crearlas y seguir imprimiendo
							if($i>=22){
							 $objPHPExcel->getActiveSheet()->insertNewRowBefore($i, 1);	
							 }
						
							 //mandar llamar la funcion comparar te devuelve el status ya validado 
							$status=comparar($buq,$cliente,$book,$tipocont,$destino,$cantidad);
						
						   $objPHPExcel->getActiveSheet()->SetCellValue("G".$i,$cliente);
						   $objPHPExcel->getActiveSheet()->SetCellValue("H".$i,$identificador);
						   $objPHPExcel->getActiveSheet()->SetCellValue("I".$i,$book);
						   if($med==1)
						   {	   	
						    $objPHPExcel->getActiveSheet()->SetCellValue("J".$i,$cant);
						   }else
						   {
						    $objPHPExcel->getActiveSheet()->SetCellValue("K".$i,$cant);
						   }
						   $objPHPExcel->getActiveSheet()->SetCellValue("L".$i,$tipocont);
						   $objPHPExcel->getActiveSheet()->SetCellValue("M".$i,$obser); 
						   $objPHPExcel->getActiveSheet()->SetCellValue("N".$i,$destino); 
						   
							     //status del booking
							 //imprimir Booking Lleno
							 if($status[0]==0){
							  $objPHPExcel->getActiveSheet()->SetCellValue("O".$i,$status[1]);
							  $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'00FFFF')));			
							 }
							  //parcialmente lleno
				    	     if($status[0]==1){
				    	      $objPHPExcel->getActiveSheet()->SetCellValue("O".$i,$status[1]);
					          $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'00FFFF')));				
				    	     }
				    	     //Sobre embarcado
							 if($status[0]==3){
							  $objPHPExcel->getActiveSheet()->SetCellValue("O".$i,$status[1]);
							  $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'00FFFF')));				
							 }
							  //Booking Vacio
							 if($status[0]==4){
							  $objPHPExcel->getActiveSheet()->SetCellValue("O".$i,$status[1]);
							  $objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'00FFFF')));				
							 }
						
						   $i++;
						}						
							//titulo  y seguridad
							$objPHPExcel->getActiveSheet()->setTitle('Agencias');
							$objPHPExcel->getSecurity()->setLockWindows(true);
							$objPHPExcel->getSecurity()->setLockStructure(true);
							//se modifica http

							header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
							header('Content-Disposition: attachment;filename="excel.xlsx"');
							header('Cache-Control: max-age=0');
							$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
							$objWriter = new PHPExcel_Writer_Excel2007 ($objPHPExcel);
							ob_end_clean ();
							$objWriter->save('php://output');
}


if ($_POST["valor"]==1)
	{
		//si preciono upload mando la funcion upload para importar datos
		upload($_GET['idusuario']);
	}
	else if($_GET["valor"]==2){
	//si preciono generar 
	  
	  generar($_GET['buq']);
	}


?>

