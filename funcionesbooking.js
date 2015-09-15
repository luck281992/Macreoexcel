function guardarpro(tidVentana, tmodo){

	alert("funcion todavia no implementada");
	if(!(gEstado==5 || gEstado==4)){
		return;
	}
	//preparamos pantalla para guardar
	gEstadot=gEstado;
	gEstado = 6;
	botonespro(tidVentana);
	document.getElementById("divpro" + tidVentana).className = "DivProVisible";

	var clave  = 0;

	//Validar el almacenado del encabezado de boleta
	tDatosbol=document.getElementById('txtfolio').value;
	tDatoscliente = document.getElementById('txtclientealias').value;
	tDatosorigen = document.getElementById('txtdestinoalias').value;
	tDatosbuque = document.getElementById('txtbuque').value;
	tDatosnota = document.getElementById('txtnotabol').value;
	tDatosfecha = document.getElementById('txtfechabol').value;
	if(tDatosbol==0)tNuevo=1; else tNuevo=0;
	sFec=new Date();
	jQuery.ajax({
	  type: "GET",
	  url: "php/clase/"+MostrarDatosPro(tidVentana)+"/guardarbol.php",
	  data: "idVentana="+tidVentana+"&dato="+sFec+"&clientealias='"+tDatoscliente+"'&origenalias='"+tDatosorigen+"'&buque='"+tDatosbuque+"'&nota="+tDatosnota+"&fecha='"+guardarfecha(tDatosfecha)+"'&clave="+tDatosbol,
	  async: false,	  
	  success: function(msg){
		  clave = parseInt(msg);
		  if (clave<=0) {
		  	gEstado=gEstadot;
		  	document.getElementById("divcaptura" + tidVentana).className = "DivVisible";
		  	document.getElementById("divfiltro" + tidVentana).className = "DivInvisible";
		  	botonespro(tidVentana);
		  	document.getElementById("divpro" + tidVentana).className = "DivInvisible";
			document.getElementById("msgbolexp").innerHTML = "&nbsp;&nbsp;"+msg;
			document.getElementById("msgbolexp").style.display = "inline";
			document.getElementById("divbguardarcomo"+tidVentana).className = "BotonInvisible";
		  } else {
		  	guardarbolexpveh(tidVentana,clave,tNuevo,tmodo);
		  }
		  if(clave>0){
			document.getElementById('txtfolio').value=clave;
		  }
		  tem = clave;
		}
	});
	GC = 0;
	document.getElementById("divcontenido-boleta").style.background = "#FFF";
	return tem;
}

//usuario: jesus javier sanchez guerrero
//fecha: 29/08/2014
//resumen
//esta funcion tiene dos parametros 1 es el nombre del buque 
//el 2 es para saber que ventana es, le mando esos datos junto con valor en una pestaña nueva
//valor es igual a 2 para indentificar que la funncion a llamar en el archivo funcionesexcel 
//es la de generar
function expoxls(buque,tidVentana){
	if(buque==""){
	  alert("Es necesario informacion de buque!");
	}else{
	valor=2;
	sFec=new Date();
	window.open("php/clase/"+MostrarDatosPro(tidVentana)+"/funcionesexcel.php?idVentana="+tidVentana+"&buq="+buque+"&valor="+valor+"&dato="+sFec);
	}
}

function ocultarfila(fila){

	document.getElementById('filaocultar'+fila).className = "DivInvisible";

}
//borrar registro escogido
function borrarfila(tidVentana,id,fila){
	document.getElementById("divmsg" + tidVentana).className="DivInvisible";
	document.getElementById("divmsgbien" + tidVentana).className="DivInvisible";

	if (window.confirm("Seguro que desea borrar este registro?") == true) 
	{
		sFec=new Date();
		jQuery.ajax({
		  type: "GET",
		  url: "php/clase/"+MostrarDatosPro(tidVentana)+"/borrarbooking.php",
		  data: "idVentana="+tidVentana+"&dato="+sFec+"&noregistro="+id,
		  async: false,	  
		  success: function(msg){
		  		clave = parseInt(msg);
			  if(clave==1){
			     document.getElementById('filaocultar'+fila).className = "DivInvisible";
			  }
			  else{
			  	
			  		alert("Error"+msg);
			  }
			}
		});
	} 
}

function guardarfilasmod(tidVentana){

	for (var i = 1; i <= parseInt(document.getElementById("nfilas").value); i++) {
		//nfila = "";
		if (document.getElementById('botguafil'+i).style.display == "inline") 
		{
			guardarfila(tidVentana, i);
		}
		
	}
}


function actualizarpro(tidVentana) {
   
	if(document.getElementById('ftxtbuque').value == ''){
		alert('¡Favor de seleccionar un buque!');
	}else{
		tFiltro= document.getElementById('ftxtbuque').value;
	}

	var tnumpan=document.getElementById("txtnumpan"+tidVentana).value;
	var tregtot=document.getElementById("txtregtot"+tidVentana).value;
    document.getElementById("divpro"+tidVentana).className = "DivProvisible";	  
	document.getElementById("divmsg"+tidVentana).className = "DivInvisible";
	sFec=new Date();
	jQuery.ajax({
	  type: "GET",
	  url: "php/clase/"+MostrarDatosPro(tidVentana)+"/mostrardatos.php",
	  data: "idVentana="+tidVentana+"&buque="+tFiltro+"&dato="+sFec+"&numpan="+tnumpan+"&regtot="+tregtot,
	  async: true,	  
	  success: function(msg){
	  	document.getElementById("contedetalle"+tidVentana).innerHTML=msg;
		document.getElementById("contedetalle"+tidVentana).className = "DivVisible";
		gEstado = 1;
	  }
	});
	document.getElementById("divpro"+tidVentana).className = "DivInvisible";


}

function guardarfila(tidVentana, nfila){
    
	document.getElementById("divpro" + tidVentana).className = "DivProVisible";
	document.getElementById("divmsg" + tidVentana).className="DivVisible";
	document.getElementById("divmsgbien" + tidVentana).className="DivVisible";
	
	tDidbook=document.getElementById('txtidbook-'+nfila).value;
	tDatosbook = document.getElementById('txtcol1-'+nfila).value; //alert('tDatospeso= '+tDatospeso);
	tDatosident=document.getElementById('txtcol2-'+nfila).value;		
	tDatosbuq = document.getElementById('txtcol3-'+nfila).value;
	tDatoscli = document.getElementById('txtcol4-'+nfila).value; //alert('tDatosbol= '+tDatosbol);
	tDatosmed = document.getElementById('txtcol5-'+nfila).value; //alert('tDatosapli= '+tDatosapli);
	tDatoscant = document.getElementById('txtcol6-'+nfila).value; //alert('Datoscant= '+tDatoscant);
	tDatosdest = document.getElementById('txtcol7-'+nfila).value; //alert('tDatosprod= '+tDatosprod);
	tDatosobser = document.getElementById('txtcol8-'+nfila).value; //alert('tDatosprod= '+tDatosprod);
	tDatostipo = document.getElementById('txtcol9-'+nfila).value; //alert('tDatosprod= '+tDatosprod);
	
	if(tDatoscli!=""&&tDatoscant!=""&&tDatosbuq!=""&&tDatosmed!="")
	{
		if(tDidbook==0) tDnuevo=1; else tDnuevo=0;
		sFec=new Date(); 
		jQuery.ajax({
		  type: "GET",
		  url: "php/clase/"+ MostrarDatosPro(tidVentana) +"/guardarfila.php",
		  data: "idVentana="+tidVentana+"&dato="+sFec+"&idbook="+tDidbook+"&book="+tDatosbook+"&identi="+tDatosident+"&buq="+tDatosbuq+"&nuevo="+tDnuevo+"&cli="+tDatoscli+"&med="+tDatosmed+"&cant="+tDatoscant+"&desti="+tDatosdest+"&obser="+tDatosobser+"&tipo="+tDatostipo,
		  async: true,	  
			  success: function(msg){
			  clave = parseInt(msg); 
			  if (clave < 0) {
			  	if(clave==-15){
			  		msgerror="Se repite registro";
			  	}
			  	if(clave==-2||clave==-3||clave==-5||clave==-6){
			  		msgerror="No existe informacion en la bd";
			  	}
			  	document.getElementById("divcaptura" + tidVentana).className = "DivVisible";
			  	document.getElementById("divfiltro" + tidVentana).className = "DivVisible";
				//document.getElementById("divbguarda" + tidVentana).className = "DivInvisible";
			  	document.getElementById("divmsg" + tidVentana).innerHTML = "&nbsp;&nbsp;noError("+msg+"),MsgError("+msgerror+") al intentar almacenar informaci&oacute;n del detalle";
				document.getElementById('filaocultar'+nfila).style.background = "#FF0000";
				document.getElementById("divmsg" + tidVentana).className="DivVisible";
				//document.getElementById("msgbolexp").style.display = "inline"
			  } else {
			 	document.getElementById("divpro" + tidVentana).className = "DivInvisible";
			 	//document.getElementById("divbguarda" + tidVentana).className = "DivInvisible";
				document.getElementById("botguafil" + nfila).style.display = "none";
				document.getElementById("divmsgbien" + tidVentana).innerHTML = "&nbsp;&nbsp;Los cambios se han realizado correctamente";
				document.getElementById('filaocultar'+nfila).style.background = "#52cd6f";
				document.getElementById("divmsgbien" + tidVentana).style.display = "DivVisible";
				
			  }
			  
		  }
		});
	}else{
	  alert("!necesita informacion de buque,cliente, medida y cantidad¡");
	}
}

function addtabla(tidVentana)
{
	document.getElementById("divmsg" + tidVentana).className="DivInvisible";
	document.getElementById("divmsgbien" + tidVentana).className="DivInvisible";

	  n = parseInt(document.getElementById('nfilas').value)+1;
	  var1 = document.getElementById('tabla');
	  fila = document.createElement('tr'); 
	  fila.id='filaocultar'+n;
	  celda0 = document.createElement('td');
	  campoid =document.createElement('input'); 
	  campoid.style.display="none";  
	  campoid.id='txtidbook-'+n;
	  campoid.value=0;

	  celda0.appendChild(campoid);
	  fila.appendChild(celda0);

	  celda3=document.createElement('td'); 
      celda3.style.width="100px"; 
	  code3=document.createElement('input'); 
	  code3.type='text';
	  code3.value=''; 
	  code3.name='txtcol3-'+n; 
	  code3.id='txtcol3-'+n;
	  code3.setAttribute("onfocus","new AutoSuggest(document.getElementById('txtcol3-'+n),cadenaautocom('buque')); foco('txtcol3-"+n+"',3,"+n+");");   
	  code3.setAttribute("onBlur","no_foco('txtcol3-"+n+"',3,"+n+");");   
	  code3.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");
	  code3.style.width='100px'; 
	  code3.style.border="1px solid #CCCCCC";
	  code3.style.background="#FFFFFF";
	  code3.maxlength='255';
	  celda3.appendChild(code3);
	  fila.appendChild(celda3);

	  celda4=document.createElement('td'); 
      celda4.style.width="150px"; 
	  code4=document.createElement('input'); 
	  code4.type='text'; 
	  code4.value='';
	  code4.name='txtcol4-'+n; 
	  code4.id='txtcol4-'+n; 
	  code4.setAttribute("onfocus","new AutoSuggest(document.getElementById('txtcol4-'+n),cadenaautocom('cliente')); foco('txtcol4-"+n+"',4,"+n+");");   
	  code4.setAttribute("onBlur"," no_foco('txtcol4-"+n+"',4,"+n+");");  
	  code4.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");
	  code4.style.width='150px'; 
	  code4.style.border="1px solid #CCCCCC";
	  code4.style.background="#FFFFFF";
	  code4.maxlength='255';
	  celda4.appendChild(code4);
	  fila.appendChild(celda4);  

      celda2=document.createElement('td'); 
      celda2.style.width="100px";
	  code2=document.createElement('input'); 
	  code2.type='text';
	  code2.value=''; 
	  code2.name='txtcol2-'+n;  
	  code2.id='txtcol2-'+n;
	  code2.setAttribute("onfocus"," foco('txtcol2-"+n+"',2,"+n+");");  
	  code2.setAttribute("onBlur"," no_foco('txtcol2-"+n+"',2,"+n+");"); 
	  code2.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");
	  code2.style.width='100px'; 
	  code2.style.border="1px solid #CCCCCC";
	  code2.style.background="#FFFFFF";
	  code2.maxlength='255';
	  celda2.appendChild(code2);
	  fila.appendChild(celda2);    

	  celda1 = document.createElement('td'); 
	  celda1.style.width="90px";
	  code=document.createElement('input'); 
	  code.type='text';
	  code.name='txtcol1-'+n; 
	  code.id='txtcol1-'+n;  
	  code.setAttribute("onfocus"," foco('txtcol1-"+n+"',1,"+n+");");   
	  code.setAttribute("onBlur"," no_foco('txtcol1-"+n+"',1,"+n+");");
	  code.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");
	  code.style.width='90px';
	  code.style.border="1px solid #CCCCCC";
	  code.style.background="#FFFFFF";
	 // code.style.align='right';
	  code.maxlength='255';
	  code.value='';	  
	  celda1.appendChild(code); 
	  fila.appendChild(celda1); 
	
	  celda5=document.createElement('td'); 
      celda5.style.width="70px";  
	  code5=document.createElement('input'); 
	  code5.type='text'; 
	  code5.value='';
	  code5.name='txtcol5-'+n; 
	  code5.id='txtcol5-'+n;
	  //code5.setAttribute("id","'textpeso'+n");
	  code5.setAttribute("onfocus"," new AutoSuggest(document.getElementById('txtcol5-'+n),cadenaautocom('medida')); foco('txtcol5-"+n+"',5,"+n+");");   
	  code5.setAttribute("onBlur","no_foco('txtcol5-"+n+"',5,"+n+");");    
	  code5.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");
	  code5.style.width='70px'; 
	  code5.style.border="1px solid #CCCCCC";
	  code5.style.background="#FFFFFF";
	 // code5.style.align='right';
	  code5.maxlength='255';
	  celda5.appendChild(code5);
	  fila.appendChild(celda5);  
	
	  celda6=document.createElement('td'); 
      celda6.style.width="70px"; 
	  code6=document.createElement('input'); 
	  code6.type='text'; 
	  code6.value='';
	  code6.name='txtcol6-'+n; 
	  code6.id='txtcol6-'+n; 
	  code6.setAttribute("onfocus","foco('txtcol6-"+n+"',6,"+n+");");   
	  code6.setAttribute("onBlur"," no_foco('txtcol6-"+n+"',6,"+n+");"); 
	  code6.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");
	  code6.style.width='70px'; 
	  code6.style.border="1px solid #CCCCCC";
	  code6.style.background="#FFFFFF";
	  code6.style.textAlign="left";
	  code6.maxlength='255';
	  celda6.appendChild(code6);
	  fila.appendChild(celda6);  

      celda9=document.createElement('td'); 
      celda9.style.width="70px"; 
	  code9=document.createElement('input'); 
	  code9.type='text'; 
	  code9.value='';
	  code9.name='txtcol9-'+n; 
	  code9.id='txtcol9-'+n;  
	  code9.setAttribute("onfocus","AutoSuggest(document.getElementById('txtcol9-'+n),cadenaautocom('tipo'));foco('txtcol9-"+n+"',9,"+n+");");   
	  code9.setAttribute("onBlur","no_foco('txtcol9-"+n+"',9,"+n+");");  
	  code9.style.width='70px'; 
	  code9.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");
	  code9.style.border="1px solid #CCCCCC";
	  code9.style.background="#FFFFFF";
	  code9.maxlength='255';
	  celda9.appendChild(code9);
	  fila.appendChild(celda9);

	      
	  celda8=document.createElement('td'); 
      celda8.style.width="150px";    
	  code8=document.createElement('input'); 
	  code8.type='text';
	  code8.value=''; 
	  code8.name='txtcol8-'+n; 
	  code8.id='txtcol8-'+n;  
	  code8.setAttribute("onfocus","foco('txtcol8-"+n+"',8,"+n+");");   
	  code8.setAttribute("onBlur","no_foco('txtcol8-"+n+"',8,"+n+");"); 
	  code8.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");

	  code8.style.width='150px'; 
	  code8.style.border="1px solid #CCCCCC";
	  code8.style.background="#FFFFFF";
	  code8.maxlength='255';
	  celda8.appendChild(code8);
	  fila.appendChild(celda8);

	  celda7=document.createElement('td'); 
      celda7.style.width="150px"; 	 
	  code7=document.createElement('input'); 
	  code7.type='text'; 
	  code7.value='';
	  code7.name='txtcol7-'+n; 
	  code7.id='txtcol7-'+n; 
	  code7.setAttribute("onfocus","AutoSuggest(document.getElementById('txtcol7-'+n),cadenaautocom('ciudad')); foco('txtcol7-"+n+"',7,"+n+");");   
	  code7.setAttribute("onBlur","no_foco('txtcol7-"+n+"',7,"+n+");");  
	  code7.setAttribute("onchange","document.getElementById('botguafil"+n+"').style.display ='inline'; document.getElementById('divbgua"+tidVentana+"').style.display ='inline';");

	  code7.style.width='150px'; 
	  code7.style.border="1px solid #CCCCCC";
	  code7.style.background="#FFFFFF";
	  code7.maxlength='255';
	  celda7.appendChild(code7);
	  fila.appendChild(celda7); 

	  celda10 = document.createElement('td');
	  celda10.style.width="22px";
	  code10 = document.createElement('img');
	  code10.src="imagen/cerrar.png";
	  code10.style.width="15px";
	  code10.style.height="15px";
	  code10.setAttribute("onclick","ocultarfila("+n+");");  
	  code10.title="Eliminar Fila";
	  celda10.appendChild(code10);
	  fila.appendChild(celda10);  

	  celda11=document.createElement('td');
	  celda11.style.width="20px";
	  code11=document.createElement('img');
	  code11.src="imagen/barracat/guardar.png";
	  code11.style.width="20px";
	  code11.style.height="20px";
	  code11.id="botguafil"+n;
	  code11.value="Guardar Fila";
	  code11.style.display="none";
	  code11.name="botoneditar"+tidVentana;
	  code11.setAttribute("onclick","guardarfila("+tidVentana+","+n+");");
	  code11.setAttribute("onmouseover","this.src='imagen/barracat/guardar2.png';");
	  code11.setAttribute("onMouseDown","this.src='imagen/barracat/guardar3.png';");
	  code11.setAttribute("onmouseout","this.src='imagen/barracat/guardar.png';");
	  celda11.appendChild(code11);
	  fila.appendChild(celda11);  
	  
	  
	  var1.appendChild(fila);  
	  document.getElementById('nfilas').value = n;

}
