$(document).ready(arranque);
function arranque()
{
	if(localStorage.wsp_hseq)
	{

		  var objDate = 16;
		  
		    var objUser = JSON.parse(localStorage.getItem('wsp_hseq'));
		    var cDate = new Date();
		  
		    var pDate = new Date(objUser.cDate);
		  
		    objDate = cDate - pDate;  

		  
		  if (Math.round((objDate/1000)/60) < 30)
		  {
		    objUser.cDate = cDate;
		    localStorage.setItem("wsp_hseq", JSON.stringify(objUser));    
		    window.location.replace("home.html");
		  } else
		  {
		    delete localStorage.wsp_hseq;    
		  }
	}



	$("#Login").submit(Login_Submit);
}
function btnMouseOver () 
{
	var obj = document.getElementById('imgLogo');
	obj.src = "img/wsplogo_negro.png";
	$(".colorRojo").css('color', '#1e242b');
}
function btnMouseOut () 
{
	var obj = document.getElementById('imgLogo');
	obj.src = "img/wsplogo.png";
	$(".colorRojo").css('color', '#ff4337');
}
function Login_Submit(evento)
{
	evento.preventDefault();
	if (validar("#Login"))
	{
		var cDate = new Date();
		$.post("server/php/proyecto/login/validarUsuario.php", 
	    {
	      pUsuario : $("#txtLogin_Usuario").val(),
	      pClave : $("#txtLogin_Clave").val(),
	      pCorreo : $("#txtLogin_Correo").val(),
	      pFecha : cDate
	    }, function (data)
	    {
	      if (data != 0)
	      {
	      	if (typeof(data) == "object")
	      	{
	        	localStorage.setItem("wsp_hseq", JSON.stringify(data));  
	        	window.location.replace("home.html");
	      	}
	      } else
	      {
	        $(".alert").html("<strong>Error!</strong> Acceso denegado.");
	        $(".alert").fadeIn(300).delay(2600).fadeOut(600);
	      }
	      
	    }, 'json').fail(function()
	    {
	      $(".alert").html("<strong>Error!</strong> No hay conexión.");
	      $(".alert").fadeIn(300).delay(2600).fadeOut(600);
	    });
	} 
}
function validar(elemento)
{
	var obj = $(elemento + ' [required]');
	var bandera = true;
	$.each(obj, function(index, val) 
	{
		 if (($(val).prop("tagName") == "SELECT" && $(val).val() == 0) || $(val).val() == "")
		 {
		 	$(val).focus();
		 	bandera = false;
			return false;
		 }
	});
	return bandera;
}