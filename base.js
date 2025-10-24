//alert('gay');
//function senhaTam() {
//	
//}
function valida(){
	var perfil;
	perfil = document.getElementById('tipoPerfil');
	if(perfil == " "){
		alert("filho da puta!!");
	}
}

function validacaoCad(){
	return true;
}

function cadastro1(){
	window.location = "cadastroEmpregado.php";
}

function cadastro2(){
	window.location = "cadastroEmpresa.php";
}

function login(){
	window.location = "index.php";
}

function telaCad(){
	window.location = "cadastro1.php";
}

function telaCad2(){
	window.location = "cadastroEmpregado2.php"
}

function telaCad3(){
	window.location = "cadastroEmpresa2.php"
}

function enviar() {
    enviarNome = document.getElementById("enviarNome").value;
    enviarEmail = document.getElementById("enviarEmail").value;
  
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
    //document.getElementById("produto"+prod).value = this.responseText;
    txt = this.responseText;
    document.getElementById("respostaUpdate").innerHTML = txt;
}
    xhttp.open("PUT", "ex.php?nome="+enviarNome+"&email="+enviarEmail);
    xhttp.send();
}