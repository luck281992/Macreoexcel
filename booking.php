<?PHP
/**
 * cargabooking.php
 *
 * Nueva interfaz para el manejo de booking en el sistema de tráfico
 * - Carga información al sistema trafico de los booking de cuardo a un archivo de excel especialmente diseñado.
 * - Exporta la info de bookings almacenada a un archivo de excel especialmente diseñado.
 * - Muestra la información de los booking especificada por el usuario y la compara con la información actual del embarque, generando una lista comparativa
 *
 * Ok Computer, Jun 2014.
 * Ultima Actualización:
 */
include("php/include/session.php");
include("php/include/conexion.php");
include("php/include/cookie.php");
include("php/include/funciones.php");

//Control de Sesi�n
if(!$session->logged_in){
	$retval = $session->login($_POST['user'], $_POST['pass'], true, $_POST['lstsis']);
	
	if(!$retval){
	   header("Location: ".$session->referrer);
	   return;
	}
}else{if(isset($_GET["sistema"])) $session->cambioSistema($_GET["sistema"]);  }

if(isset($_GET["idVentana"])) $idVentana=$_GET["idVentana"];

//	accesando tabla configuración
	if(isset($_GET['calauto']))
		$calauto=$_GET['calauto'];
	else {
		$calauto = 0;
		$tablacon = mysql_query("SELECT calendarioauto FROM configuracion",$enlace) or die("Error en la consulta a tabla Configuracion.");
		if ($datos = mysql_fetch_assoc($tablacon)) {
			$calauto	=$datos["calendarioauto"];
		}
	}

//consulta para sacar la clave del usuario
$consulta=mysql_query("SELECT clave FROM usuario WHERE username='".$session->username."'",$enlace)or die("Error en la consulta.".mysql_error());
$dataid=mysql_fetch_array($consulta);
$idusu=$dataid['clave']; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<title>Sistema Agencias 2009 - Cargar Bookings</title>
	<link rel="shortcut icon" href="imagen/agencias.ico"/>

	<!----Estilo de la interfase principal---->
	<link href="css/menu.css" rel="stylesheet" type="text/css">				<!----Barra de Menús---->
	<link href="css/estilocat.css" rel="stylesheet" type="text/css">		<!----Catálogos---->
	<link href="css/estiloauto.css" rel="stylesheet" type="text/css">		<!----Autocompletado---->
	<link href="css/jqueryslidemenu.css" rel="stylesheet" type="text/css"> 	<!----Menús Desplegables---->
	<!----Estilo Propios del proceso---->
	<link href="css/estilocargaboo.css" rel="stylesheet" type="text/css">

	<!----librerias de la interfase principal---->
	<!----Calendario de ayuda---->
	<link href="js/calendario/calendar-blue.css" rel="stylesheet" type="text/css">	
	<script type="text/javascript" src="js/calendario/calendar.js"></script>		
	<script type="text/javascript" src="js/calendario/calendar-setup.js"></script>
	<script type="text/javascript" src="js/calendario/calendar-en.js"></script>

	<!----Librerias de interfase general---->
	<script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="js/jqueryslidemenu.js"></script>
	<script type="text/javascript" src="js/funcionesgenerales.js"></script>
	<script type="text/javascript" src="js/funcionesdatos.js"></script>
	<script type="text/javascript" src="js/barramenu.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/prototype.js"></script>
	<script type="text/javascript" src="js/effects.js"></script>
	<script type="text/javascript" src="js/dragdrop.js"></script>
	<script type="text/javascript" src="js/movimiento.js"></script>
	<script type="text/javascript" src="js/abrirventanaautocom.js"></script>
	<script type="text/javascript" src="js/autocompletado.js"></script>
	<script type="text/javascript" src="js/autosuggest.js"></script>
	<script type="text/javascript" src="js/cookie.js"></script>
	<script type="text/javascript" src="js/shortcut.js"></script>

	<!----Librerias propias del proceso---->
	<script type="text/javascript" src="js/procesos/funcionesbooking.js"></script>

	<!----Declaración de variables---->
	<script>
		var propiedadesactivado=<?php echo $calauto; ?>;
		var filtrosactivaodos=0;
	</script>

	<!----Inicio de la interfase---->
	<!----Interfase General---->
</head>
<body class="body<?php echo $session->sistema;  ?>">
	<?php	
		// necesita estar aqui formadatos porque debe estar dentro de html y que reconozca el servidor
		// la codificacion del utf-8 
		include("php/include/formadatos.php"); 
    ?>
	<div id="autosuggest" style="z-index:1000; font-family:Verdana, Arial, Helvetica, sans-serif;overflow-y:auto; height:280px; background:#AEE4FF; font-size:13px; text-align:left;"></code></p></div>
		<div id="divhead" >
			<?php 
				mostrarbarher($session->sistema);
			?>
		</div>
		<div id="fondobody">
			<div id="divbody" style="width:98.6%; float:left;" >
			<!----Interfase del proceso---->
				<input id="txtnumpan<?php echo $_GET['idVentana'] ?>" name="txtid" type="inputbox" value="0" class="InputInvisible">
				<input id="txtregtot<?php echo $_GET['idVentana'] ?>" name="txtid" type="inputbox" value="-1" class="InputInvisible">
				<div id="contenedor">
					<div id="divtitulo-carboo">
						<div style="width:97%; background:url(imagen/arriba-der-boleta.png);  height:39px; text-align:center; float:left; ">
						<div style=" width:95%; height:10px; text-align:center; float:left; " ></div>
							<div style=" float:right; width:19px; height:39px;">
								<div style=" width:95%; height:10px; text-align:center; float:right;" ></div>
								<img onMouseOut="this.src='imagen/barracat/cerrar.png';"  onMouseOver="this.src='imagen/barracat/cerrar2.png';" onMouseUp="this.src='imagen/barracat/cerrar2.png';" onMouseDown="this.src='imagen/barracat/cerrar3.png';" src="imagen/barracat/cerrar.png" width="16" height="16" onClick="window.location='index.php';" style="cursor:pointer;" />
							</div>
					   		Cargar Booking
						</div>
						
						<div id="findeencabezado-carboo" style=" float:left;" >
							<div style="width:31px; height:11px; text-align:center; float:left; " ></div>
						</div>
				    </div>
			  		<div id="divcontenido-carboo" style="width:98.8%;  float:left;">
				    	<div id="divfiltro<?php echo $idVentana; ?>" class="DivVisible"  style="float:left;">
				   			<div style="float:left;">
								<div style="width:81px; text-align:right; float:left;">Buque:</div>
								<div style=" width:200px; float:left;">
									<input id="nombrebuque" name="nombrebuque" type="text" value="buque" class="InputInvisible" />
									<input id="ftxtbuque" name="ftxtbuque" type="text" maxlength="255px" style="width:200px" onfocus="new AutoSuggest(document.getElementById('ftxtbuque'),cadenaautocom('buque'));"  />
								</div>
							</div>
						   	<div id="separador" style="width:1000px; height:20px; float:left;"></div>
					   		<div id="separador" style="width:1000px; height:10px; float:left;"></div>
							<div id="divmsg<?php echo $idVentana; ?>"  class="DivInvisible" style="float:left; background:url(imagen/menserror.png) no-repeat; width:99.9%;"></div>
							<div id="divmsgbien<?php echo $idVentana; ?>"  class="DivInvisible" style="float:left; background:url(imagen/mensajec.png) no-repeat; width:99.9%;"></div>
							<div id="contedetalle<?php echo $idVentana; ?>"  class="DivVisible" style="float:left; width:99.8%;"></div>	
							<div id="divpro<?php echo $idVentana; ?>" class="DivInvisible"  style="float:left;" >
								<span></span>		
								<img src="imagen/loading3.gif" />
							</div>	
							<div id="separador" style="width:1000px; height:10px; float:left;"></div>
							<iframe height="100" width="100%"  name="booking"  style="border:none;"></iframe>	
							<div id="separador" style="width:1000px; height:10px; float:left;"></div>
						</div>
						<div id="divcaptura<?php echo $idVentana; ?>" class="DivInvisible" ></div>			
						<div id="divpro<?php echo $idVentana; ?>" class="DivInvisible">
							<span></span>		
							<img src="imagen/loading3.gif" />
						</div>	
					</div>	
					<!----Barra de Herramientas del proceso---->
					<div id="divboton-izq-carboo" style=" width:97.8%;" >
						<div id="divbotones<?php echo $idVentana; ?>" >	
							<div id="divbgua<?php echo $idVentana; ?>"  class="BotonVisible"  align="center">
							<img id="botongua<?php echo $idVentana; ?>" title="Guardar: Guardar todos los cambios realizados." src='imagen/barracat/guardar.png' onMouseOut="this.src='imagen/barracat/guardar.png';" onMouseOver="this.src='this.src='imagen/barracat/guardar2.png';" onMouseUp="this.src='imagen/barracat/guardar2.png';" onMouseDown="this.src='imagen/barracat/guardar3.png';" width='32' height='32' value='Inicio' onclick="guardarfilasmod(<?php echo $idVentana; ?>);" style="cursor:pointer;"/>
							<div class="style1" id="divbcancelar" style="width:64px; text-align:center;">
								<span class="style1" style="width:64px; text-align:center"><img src='imagen/barracat/txtguardar.png' width='63' height='14' style="cursor:pointer;"/></span>
							</div>
						   </div>
						   
							<div id="divbactualizar<?php echo $idVentana; ?>" class="BotonVisible" align="center">
							 	<img id="botonactualizar<?php echo $idVentana; ?>" title="Actualizar: vuelve a cargar los datos en pantalla con la ultima consulta realizada. [F5]" src='imagen/barracat/actualizar.png' onMouseOut="this.src='imagen/barracat/actualizar.png';" onMouseOver="this.src='imagen/barracat/actualizar2.png';" onMouseUp="this.src='imagen/barracat/actualizar2.png';" onMouseDown="this.src='imagen/barracat/actualizar3.png';" width='32' height='32'  value='Actualizar' onClick="actualizarpro(<?php echo $idVentana; ?>)" style="cursor:pointer;"/>
							  	<div class="style1" id="divbactualizart" style="width:64px; text-align:center;">
									<img src='imagen/barracat/textactualizar.png' width='63' height='14' style="cursor:pointer;"/>
								</div>
							</div>
							<div id="divbexpoxls<?php echo $idVentana; ?>" class="BotonVisible"  align="center">
							  <div class="style1" id="divbcancelar" style="width:64px; text-align:center;">
									<span class="style1" style="width:64px; text-align:center">
									<img id="botonexpoxls<?php echo $idVentana; ?>" title="Exportar: Generar Archivo de Excel con la información de Bookings del buque seleccionado." src='imagen/barramenupro/impexelexp.png' onmouseout="this.src='imagen/barramenupro/impexelexp.png';" onmouseover="this.src='imagen/barramenupro/impexelexp2.png';" onmouseup="this.src='imagen/barramenupro/impexelexp2.png';" onmousedown="this.src='imagen/barramenupro/impexelexp3.png';" width='32' height='32' value='Inicio' onclick="expoxls(document.getElementById('ftxtbuque').value,<?php echo $idVentana; ?>);" style="cursor:pointer;"/>
									<img src='imagen/cataplicaciones/txtexportar.png' width='63' height='14' style="cursor:pointer;"/></span>
								</div>
							</div>
							<div id="divbexpoxls<?php echo $idVentana; ?>" class="BotonVisible"  align="center">
								<form action="php/clase/booking/funcionesexcel.php?idusuario=<?php echo $idusu; ?>" method="POST" enctype="multipart/form-data" target="booking" style="text-align:right;">
									
									<input type="hidden" id="valor" name="valor" />
									
									<div class="style1" id="divbcancelar" style="width:64px; text-align:center;float:right;">
										<span class="style1" style="width:64px; text-align:center">
										<input type="image" id="botonexpoxls<?php echo $idVentana; ?>" title="Importar archivo con información de bookings" src='imagen/barramenupro/impexelimp.png' onmouseout="this.src='imagen/barramenupro/impexelimp.png';" onmouseover="this.src='imagen/barramenupro/impexelimp2.png';" onmouseup="this.src='imagen/barramenupro/impexelimp2.png';" onmousedown="this.src='imagen/barramenupro/impexelimp3.png';" width='32' height='32' onclick="document.getElementById('valor').value=1" style="cursor:pointer;"/>
										<img src='imagen/cataplicaciones/txtimportar.png' width='63' height='14' style="cursor:pointer;"/></span>
									</div>
									<input type="file" name="excel" /></td>
								  
								</form>
							</div>
						</div>
					</div>
					<div id="divboton-der-carboo" ></div>
				</div>
			</div>
			<!----Calendario de Trabajo---->
			<div id="ocultarbarra">
				  <p>&nbsp;</p>
				  <p>&nbsp;</p>
				  <p>&nbsp;</p>
				  <p>&nbsp;</p>
				  <p>&nbsp;</p>
				  <p>&nbsp;</p>
				  <p>&nbsp;</p>
				  <p>&nbsp;</p>
				  <p><img src="imagen/calendario/ocultar.png" width="7" height="11" onClick="if(propiedadesactivado == 0) {Effect.SlideDown('propiedades'); propiedadesactivado=1;   } else{Effect.SlideUp('propiedades'); propiedadesactivado=0;}" /></p>
			</div>
			<div id="propiedades" style="display:none;" >
			<div id="calendario" align="center">
				<?php if($calauto==1) include("php/calendario.php"); else echo "<script type='text/javascript'> Effect.SlideUp('propiedades'); </script>"; ?>
			</div>
			</div>
		</div>
	</div>
	<?php 
		piegeneral();
	?>
</body>
</html>
