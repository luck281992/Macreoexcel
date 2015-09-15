<?php
	include("../../include/conexion.php");
	include("../../include/funciones.php");
	include("../../include/formadatos.php");

//
	$numpan=0;
	$regxpan=100;
	$liminf = $numpan*$regxpan;
	if($liminf>0)$liminf++;
	$limsup = 100;
//fin configuración
	$Filtro="";
	if(isset($_GET['buque'])){
	if($_GET["buque"]!=""){
		$Filtro = "buque.nombre LIKE '%".$_GET["buque"]."%'";	
	}
	}
	
	
	if($Filtro!=""){
		$Filtro=" WHERE ".$Filtro;
	}
	if(isset($_GET["idorden"])) $idorden=$_GET["idorden"];
	else $idorden=1;
	if($idorden<1||$idorden>5)$idorden=1;

	if($_GET["regtot"]==-1){
		$consulta = "SELECT COUNT(booking.clave) AS cuenta FROM booking  
					LEFT JOIN clientealias ON clientealias.clave = booking.clientealias 
					LEFT JOIN buque ON buque.clave = booking.buque
					LEFT JOIN ciudadalias ON ciudadalias.clave = booking.origenalias".$Filtro;
		$tabla = mysql_query($consulta,$enlace) or die("Error al General la consulta.");
		if ($datos = mysql_fetch_assoc($tabla))
			$regtot		=$datos["cuenta"];
	}else 	$regtot		=$_GET["regtot"];


	$consulta = "SELECT booking.clave AS id, booking.booking AS booboo, booking.fecha AS boofec, buque.nombre AS buqnom, clientealias.nombre AS clinom,
	contenedormed.abreviacion AS medida,contenedortip.abreviacion AS tipo,bookingdetalle.cantidad AS cantidad, ciudadalias.nombre AS ciunom, 
	booking.observacion AS obser, booking.estado AS booest, booking.clave AS boocla,booking.identificador AS ident
	FROM booking 
	INNER JOIN bookingdetalle ON bookingdetalle.booking=booking.clave
	LEFT JOIN clientealias ON clientealias.clave = booking.clientealias 
	LEFT JOIN buque ON buque.clave = booking.buque 
	LEFT JOIN ciudadalias ON ciudadalias.clave = booking.destinoalias
	LEFT JOIN contenedortip ON contenedortip.clave=bookingdetalle.contip
	LEFT JOIN contenedormed ON contenedormed.clave=bookingdetalle.conmed".$Filtro." ORDER BY ".$idorden." LIMIT ".$liminf.",".$regxpan;
	$tabla = mysql_query($consulta,$enlace) OR die("error al generar consulta.");
?>

<script type="text/javascript">
var ocultar=0;
var variable=0;
</script>

<style type="text/css">
<!--
.style1 {font-family: Verdana, Arial, Helvetica, sans-serif}
.style2 {font-size: 14px}
.style5 {font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 14px; }
.style6 {font-size: 12px}
-->
</style>

<div id="tablacont" style="margin-right:10px;float:right;">
	<table  width="995px" height="17" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCEEFF" >
	  <tr>
		<td id="enc3" width="100px"class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(3);" onmouseout="tablaselencout(3);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,3)">Buque</a></div>
		</div></td>
		<td id="enc4" width="150px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(4);" onmouseout="tablaselencout(4);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,4)">Cliente</a></div>
		</div></td>
		<td id="enc2" width="100px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(2);" onmouseout="tablaselencout(2);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,2)">Clave</a></div>
		</div></TD>
		<td id="enc1" width="90px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(1);" onmouseout="tablaselencout(1);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,1)">Booking</a></div>
		</div></td>
		<td id="enc5" width="70px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(5);" onmouseout="tablaselencout(5);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,5)">Medida</a></div>
		</div></td>
		<td id="enc6" width="70px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(6);" onmouseout="tablaselencout(6);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,6)">Cantidad</a></div>
		</div></td>
		<td id="enc9" width="70px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(9);" onmouseout="tablaselencout(9);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,9)">Tipo</a></div>
		</div></td>
		<td id="enc8" width="150px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(8);" onmouseout="tablaselencout(8);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,8)">Observacion</a></div>
		</div></td>
		<td id="enc7" width="150px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(7);" onmouseout="tablaselencout(7);">
		  <div align="left"><a style="cursor:pointer;" onclick="buscar2pro(<?php echo $_GET['idVentana'] ?>,7)">Destino</a></div>
		</div></td>
		<td width="22px" ><div id="estiloselec3" class="cant1" style="float:left;">&nbsp;<img  src='imagen/barramenupro/add.png' width='22ox' height="17px" id="addrow" title="Agregar Fila[Shift + Insert]" onclick="addtabla(<?php echo $_GET['idVentana']?>)" style="cursor:pointer;" /></div></td>

	   </tr>
	</table>
<div id="mosdat" style="overflow-y:auto; height:380px;">
<?php
	$fila=0;
	$estilo=20;
	$j=0;
	$a=0;
	if (mysql_num_rows($tabla) != 0 ) {
		for ($i = 0; $i < mysql_num_rows($tabla); $i++) {
			if (!mysql_data_seek($tabla, $i)) {
				echo "Cannot seek to row $i: " . mysql_error() . "\n";
				continue;
			}
			if (!($datos = mysql_fetch_assoc($tabla))) {
				continue;
			}

$j++;
?>
<div id="filaocultar<?php echo $j; ?>" class="DivVisible">

		<table id="contenidotabla" width="709px" border="0" cellpadding="0" cellspacing="0">
		<tr>
	<?php  if($datos["booest"]==-1){	//Si la boleta está cancelada imprime el texto de cancelado  ?>
		<td width="90px"><div align="center" class="style1 style5">
		  <div align="left">
			<input id="txtidbook-<?php echo $j; ?>" style="display:none;" value="<?php echo $datos['id']; ?>"  />
		</div></td>

		<td width="90px" ><div align="center" class="style1 style5">
		  <div align="left">
			<input id="txtcol1-<?php echo $j; ?>" class="filaout" name="txtcancelado<?php echo $j; ?>" type="inputbox" readonly maxlength="255" style="width:90px;  text-align:right; border:1px solid #CCCCCC;"value="<?php echo $datos['booboo']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';"  onfocus="foco(15,<?php echo $j; ?>);" onblur="nofoco(15,<?php echo $j; ?>);"  onmouseout="tablaselfilout(<?php echo $j; ?>);"  onkeypress="gridenter(event);" />
		  </div>
		</div></td>
		<td colspan=4 style="text-align:center;" >	
			<input id="txtcancelado-<?php echo $j; ?>" class="filaout" name="txtcancelado2-<?php echo $j; ?>" type="inputbox" readonly maxlength="255" style="width:570px; text-align:center; border:1px solid #CCCCCC;" onmouseover="tablaselfilcanover(<?php echo $j; ?>);" onmouseout="tablaselfilcanout(<?php echo $j; ?>);" value="CANCELADO" /> 
		</td>
		<td width="26px" ><div align="center"><img id="botoneditar<?php echo $_GET['idVentana']; ?>" title="Editar: No disponible(El registro esta cancelado)." onMouseOut="this.src='imagen/barracat/editar3.png'; tablaselfilcanout(<?php echo $j; ?>);" onMouseOver="this.src='imagen/barracat/editar3.png'; tablaselfilcanover(<?php echo $j; ?>);" onMouseUp="this.src='imagen/barracat/editar3.png';" onMouseDown="this.src='imagen/barracat/editar3.png'; "src='imagen/barracat/editar3.png' width='20' height='20' value='Editar'  onclick="" /></div></td>
	<?php
		}else{ //Si la boleta no está cancelada
	?>
		<td width="90px"><div align="center" class="style1 style5">
		  <div align="left">
			<input id="txtidbook-<?php echo $j; ?>" style="display:none;" value="<?php echo $datos['id']; ?>"  />
		</div></td>
		<td width="100px" ><div align="center" class="style5">
		  <div align="left">
			<input id="txtcol3-<?php echo $j; ?>" style="width:100px; border:1px solid #CCCCCC;" class="filaout" name="txtcol3-<?php echo $j; ?>" type="text"  maxlength="255px" value="<?php echo $datos['buqnom']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';"  onfocus="new AutoSuggest(document.getElementById('txtcol3-<?php echo $j; ?>'),cadenaautocom('buque')); foco(3,<?php echo $j; ?>);" onblur="nofoco(3,<?php echo $j; ?>);"   onkeypress="gridenter(event);" />
		  </div>
		</div></td>
		<td width="150px"><div align="center" class="style5">
		  <div align="left">
			<input id="txtcol4-<?php echo $j; ?>"  class="filaout" name="txtcol4-<?php echo $j; ?>" style="width:150px; border:1px solid #CCCCCC;" type="text"  maxlength="255" value="<?php echo $datos['clinom']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';"  onfocus="new AutoSuggest(document.getElementById('txtcol4-<?php echo $j; ?>'),cadenaautocom('cliente')); foco(4,<?php echo $j; ?>);" onblur="nofoco(4,<?php echo $j; ?>);" onkeypress="gridenter(event);" />
		  </div>
		</div></td>
		<td width="100px" ><input id="txtcol2-<?php echo $j; ?>" style="width:100px; border:1px solid #CCCCCC; " class="filaout" name="txtcol2-<?php echo $j; ?>" type="text"  maxlength="255" value="<?php echo $datos['ident']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';"   onblur="nofoco(2,<?php echo $j; ?>);" /></td>
		<td width="90px"><div align="center" class="style1 style5">
		  <div align="left">
			<input id="txtcol1-<?php echo $j; ?>"  style="border:1px solid #CCCCCC; width:90px; text-align:right;" name="txtcol1-<?php echo $j; ?>" class="filaout" type="text"  maxlength="255px" value="<?php echo $datos['booboo']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';"   onblur="nofoco(1,<?php echo $j; ?>);"  onkeypress="gridenter(event);" />
		</div></td>
		<td width="70px"><div align="center" class="style5">
		  <div align="left">
		  <input id="txtcol5-<?php echo $j; ?>"  class="filaout" name="txtcol5-<?php echo $j; ?>" type="text"  maxlength="255px" value="<?php echo $datos['medida'];  ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';" onfocus="new AutoSuggest(document.getElementById('txtcol5-<?php echo $j; ?>'),cadenaautocom('medida'));" onblur="nofoco(5,<?php echo $j; ?>);"  style="width:70px; border:1px solid #CCCCCC;"   onkeypress="gridenter(event);" />
		  </div>
		</div></td>
		<td width="70px"><div align="center" class="style5">
		  <div align="left">
		  <input id="txtcol6-<?php echo $j; ?>"  class="filaout" name="txtcol6-<?php echo $j; ?>" type="text"  maxlength="255px" style="width:70px; border:1px solid #CCCCCC;" value="<?php echo $datos['cantidad']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';"   onblur="nofoco(6,<?php echo $j; ?>);"  onkeypress="gridenter(event);" />
		  </div>
		</div></td>
		<td width="70"><div align="center" class="style5">
		  <div align="left">
		  <input id="txtcol9-<?php echo $j; ?>"  class="filaout" name="txtcol9-<?php echo $j; ?>" type="inputbox"  maxlength="255px" style="width:70px; border:1px solid #CCCCCC;" value="<?php echo $datos['tipo']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';" onfocus="new AutoSuggest(document.getElementById('txtcol9-<?php echo $j; ?>'),cadenaautocom('tipo'));"  onblur="nofoco(7,<?php echo $j; ?>);"  onkeypress="gridenter(event);" />
		  </div>
		</div></td>
		<td width="150"><div align="center" class="style5">
		  <div align="left">
		  <input id="txtcol8-<?php echo $j; ?>"  class="filaout" name="txtcol8-<?php echo $j; ?>" type="text"  maxlength="255px" style="width:150px; border:1px solid #CCCCCC;" value="<?php echo $datos['obser']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';"  onblur="nofoco(8,<?php echo $j; ?>);"   onkeypress="gridenter(event);" />
		  </div>
		</div></td>
		<td width="150px"><div align="center" class="style5">
		  <div align="left">
		  <input id="txtcol7-<?php echo $j; ?>"  class="filaout" name="txtcol7-<?php echo $j; ?>" type="text"  maxlength="255px" style="width:150px; border:1px solid #CCCCCC;" value="<?php echo $datos['ciunom']; ?>" onchange="document.getElementById('botguafil<?php echo $j?>').style.display ='inline'; document.getElementById('divbgua<?php echo $_GET['idVentana']?>').style.display ='inline';"  onfocus="new AutoSuggest(document.getElementById('txtcol7-<?php echo $j; ?>'),cadenaautocom('ciudad'));" onblur="nofoco(7,<?php echo $j; ?>);" onkeypress="gridenter(event);" />
		  </div>
		</div></td>
		<td><img  src='imagen/cerrar.png' onclick="borrarfila(<?php echo $_GET['idVentana']; ?>,<?php echo $datos['id'];?>,<?php echo $j; ?>); " width='15px' height='15px'/></td>
           
		<td><div class="divocultage">
		<img id="botguafil<?php echo $j; ?>" style="display:none;" src='imagen/barracat/guardar.png' name="botoneditar<?php echo $_GET['idVentana']; ?>" width='20' height='20' title="Guardar: de click aqu&iacute; para guardar las modificaciones realizadas." onclick="guardarfila(<?php echo $_GET['idVentana'];?>,<?php echo $j ?>)" style="cursor:pointer;" onmouseover="this.src='imagen/barracat/guardar2.png';" onMouseDown="this.src='imagen/barracat/guardar3.png';" onmouseout="this.src='imagen/barracat/guardar.png';" value='Guardar Fila' />	
		</div></td>
	<?php
		}
	?>
	  </tr>
	</table>
</div>
<?PHP
		$estilo = $estilo + 1;
		}
	}

	?> 
	<table border="0" cellpadding="0" cellspacing="0"  bgcolor="#0099cc"> 
			  <tbody id="tabla">

			  </tbody>
		</table>
</div>
  	
	<input id="nfilas"  style="display:none;" value="<?php echo  $j; ?>"/>
		  
</div>


<?php $consul="SELECT booking.clave AS id, cliente.nombre AS clinom, CASE  WHEN bookingdetalle.conmed= 1 THEN SUM(bookingdetalle.cantidad) END AS cant20,
			CASE  WHEN bookingdetalle.conmed= 2 THEN SUM(bookingdetalle.cantidad) END AS cant40,bookingdetalle.conmed AS medida,contenedortip.abreviacion AS contipnom
			FROM booking 
			INNER JOIN bookingdetalle ON bookingdetalle.booking=booking.clave 
			LEFT JOIN contenedortip ON contenedortip.clave = bookingdetalle.contip
			LEFT JOIN buque ON booking.buque = buque.clave
			LEFT JOIN cliente ON booking.cliente = cliente.clave ".$Filtro." GROUP BY clinom,conmed";
		$tabla2 = mysql_query($consul,$enlace) OR die("error al generar consulta\n $consul");

?>
<div id="filamini">
	<table  width="280px" height="17px" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCEEFF" >
	   <tr>
		<td id="e3" width="120px"class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(10);" onmouseout="tablaselencout(10);">
		  <div align="left"><a style="cursor:pointer;" >cliente</a></div>
		</div></td>
		<td id="e4" width="50px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(11);" onmouseout="tablaselencout(11);">
		  <div align="left"><a style="cursor:pointer;" >20</a></div>
		</div></td>
		<td id="e5" width="50px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(12);" onmouseout="tablaselencout(12);">
		  <div align="left"><a style="cursor:pointer;" >40</a></div>
		</div></td>
		<td id="e6" width="50px" class="trTitulo"><div align="center" class="style5" onmouseover="tablaselencover(13);" onmouseout="tablaselencout(13);">
		  <div align="left"><a style="cursor:pointer;" >Tipo</a></div>
		</div></td>
		 </tr>
	</table>	

<div id="minitablacont" style="overflow-y:auto; height:380px;">

   	<?php 
   $total=0;
   $total2=0;
 
 if (mysql_num_rows($tabla2) != 0 ) {

 	$cliente="";
 	$clientei=1;
 	$tipom="";
 	$tipoi=1;
 	$pos=0;
 	$band=0;
 	$a=0;
		for ($i = 0; $i < mysql_num_rows($tabla2); $i++) {
			if (!mysql_data_seek($tabla2, $i)) {
				echo "Cannot seek to row $i: " . mysql_error() . "\n";
				continue;
			}
			if (!($dato2 = mysql_fetch_assoc($tabla2))) {
				continue;
			}
			$total+=$dato2['cant20'];
		 	$total2+=$dato2['cant40'];
$a++;
	if($cliente!=$dato2['clinom'] || $clientei==1||$tipom!=$dato2['contipnom']||$tipoi=1) //para saber si se repite el cliente
	 { 
		$clientei=0;
		$tipoi=1;
		$tipom=$dato2['contipnom'];
		$cliente=$dato2['clinom'];
		$band=1;
	 ?>
	 <div id="fila2<?php echo $a; ?>"   class="divfilest<?php  ?>">

	 <table width="280px" border="0" cellpadding="0" cellspacing="0">
  	 <tr>
		<td width="120px" ><div align="center" class="style5">
			  <div align="left">
					<input id="dxtcol1-<?php echo $a; ?>"  name="dxtcol1-<?php echo $a; ?>" style="width:120px; border:1px solid #CCCCCC;" type="text"  maxlength="255" value="<?php echo $dato2['clinom']; ?>" onchange="document.getElementById('botguamini<?php echo $a?>').style.display ='inline'; document.getElementById('divbguarmini<?php echo $_GET['idVentana']?>').style.display ='inline';"  onfocus="new AutoSuggest(document.getElementById('dxtcol1-<?php echo $a; ?>'),cadenaautocom('cliente')); " onkeypress="gridenter(event);" />
			  </div>
			</div></td>
		<?php

	}
	?>
		<input id="dxtidbook-<?php echo $a; ?>"  name="dxtidbook-<?php echo $a; ?>" style="display:none;" type="text"  />
	<?php

	 	if($dato2['medida']==1){
		$band=1;
		?>
	       
	   <td width="50px" style="float:left;"><div align="center" class="style5" >
			  <div  align="left">
		  <input id="dxtcol2-<?php echo $a; ?>"  class="filaout" name="dxtcol2-<?php echo $a; ?>" type="text"  maxlength="255px" style="width:50px; border:1px solid #CCCCCC;" value="<?php echo $dato2['cant20']; ?>" onchange="document.getElementById('botguamini<?php echo $a?>').style.display ='inline'; document.getElementById('divbguarmini<?php echo $_GET['idVentana']?>').style.display ='inline';"  onkeypress="gridenter(event);" />
			  </div>
			</div></td>
	
		<?php 
		}
	
		
		if($dato2['medida']==2){
		$band=2;
		?>
	    <td  width="50px" style="float:left;"><div align="center" class="style5" >
				  <div align="left">
					  <input id="dxtcol3-<?php echo $a; ?>"  class="filaout" name="dxtcol3-<?php echo $a; ?>" type="text"  maxlength="255px" style="width:50px; border:1px solid #CCCCCC;" value="<?php echo $dato2['cant40']; ?>" onchange="document.getElementById('botguamini<?php echo $a?>').style.display ='inline'; document.getElementById('divbguarmini<?php echo $_GET['idVentana']?>').style.display ='inline';"    onkeypress="gridenter(event);" />
				  </div>
			</div></td> 
		<?php 
		}
		//proceso en donde termina el registro
		?><td width="50px" style="float:left;" ><div align="center" class="style5">
			  <div align="left">
					<input id="dxtcol4-<?php echo $a; ?>"  name="dxtcol3-<?php echo $a; ?>" style="width:50px; border:1px solid #CCCCCC;" type="text"  maxlength="255" value="<?php echo $dato2['contipnom']; ?>" onchange="document.getElementById('botguamini<?php echo $a?>').style.display ='inline'; document.getElementById('divbguarmini<?php echo $_GET['idVentana']?>').style.display ='inline';"  onfocus="new AutoSuggest(document.getElementById('dxtcol4-<?php echo $a; ?>'),cadenaautocom('tipo')); " onkeypress="gridenter(event);" />
			  </div>
		</div></td>

	</tr> 
  	</table>
	<?php
	
  ?> 
 	</div><?php
 	?>
 	<table border="0" cellpadding="0" cellspacing="0"  bgcolor="#0099cc"> 
			  <tbody id="tablaresumen"> 
			  </tbody>
		</table> <?php
	}
}

 ?>		 

	 <input name="txtfilas" id="txtfilas" type="text"  value="<?php echo $a;?>" maxlength="2px" style="width:390px; display:none;"/>

	  <table width="280px" 	 border="0" cellpadding="0" cellspacing="0">
	  	 <tr>
		</div></td>
	  	<td  width="117px"><div align="center" class="style5" >
		<div align="left">
		<h3 >totales</h3>
		</div>
		</div></td> 

		<td  width="40px"><div align="center" class="style5" >
		<div align="left">
		  <input id="total1" disabled class="filaout" name="total1" type="inputbox"  maxlength="255px" value="<?php echo $total;  ?>"  style="width:45px;" />
		</div>
		</div></td>

	  	<td  width="40px"><div align="center" class="style5" >
		<div align="left">
		  <input id="total2" disabled class="filaout" name="total2" type="inputbox"  maxlength="255px" value="<?php echo $total2;  ?>"  style="width:45px;" />
		</div>
		</div></td> 
		<td  width="50px"><div align="center" class="style5" >
		<div align="left">
		  <input style="width:50px;display:none;" />
		</div>
		</div></td> 
	   </tr>
	 </table>
	
</div>
</div>	


	 