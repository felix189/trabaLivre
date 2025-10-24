<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">
	<title>Cadastro</title>
	<link rel="stylesheet" type="text/css" href="base2.css">
	<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
	<script type="text/javascript" src="base.js">
		function insert() {
				enviarNome = document.getElementById("nome").value;
				enviarCNPJ = document.getElementById("CNPJ").value;
				enviarDDD = document.getElementById("DDD").value;
				enviarTel = document.getElementById("Tel").value;

				const xhttp = new XMLHttpRequest();
				xhttp.onload = function() {
				//document.getElementById("produto"+prod).value = this.responseText;
				}
				xhttp.open("POST", "ex2.php", true);
			  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			  xhttp.send("&nome="+enviarNome+"&CNPJ="+enviarCNPJ+"&DDD="+enviarDDD+"&Tel="+enviarTel);
			}
	</script>
	</head>
		<body class="fundo2">
		<button class="btnVlt" onclick="telaCad()"></button><br>
		<br>
		<div class="boxEmp">
			<form name="form2" class="cad" onsubmit="return validacaoCad()" method="POST" action="cadastroEmpresa2.php">
					<div class="cadTitulo">
					<h1>Cadastro</h1>
					</div><br>
				<input type="text" class="cadText" id="nome" name="nome" placeholder="Nome da Empresa"><br>
				<div id="erroNome" class="erro"></div>
				<input type="text" class="cadText" id="CNPJ" name="CNPJ" maxlength="14" placeholder="CNPJ"><br>
				<div id="erroCNPJ" class="erro"></div>
				<div class="telefone">
				<input type="text" class="cadDDD" id="DDD" name="DDD" maxlength="2" placeholder="DDD" size="2">
				<input type="text" class="cadText" id="Tel" name="Tel" maxlength="9" placeholder="Telefone">
				</div>
				<div id="erroTel" class="erro"></div>
				<br>
				<button class="btnProx" onclick="telaCad3()">Proximo</button>
			</form>
		</div>
		</body>
</html>