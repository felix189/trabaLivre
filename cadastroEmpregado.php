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
				enviarEmail = document.getElementById("email").value;

				const xhttp = new XMLHttpRequest();
				xhttp.onload = function() {
				//document.getElementById("produto"+prod).value = this.responseText;
				}
				xhttp.open("POST", "ex.php", true);
			  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			  xhttp.send("&nome="+enviarNome+"&email="+enviarEmail);
			}
		</script>
	</head>
	<body class="fundo2">
		<button class="btnVlt" onclick="telaCad()"></button><br>
		<br>
		<div class="boxEmp">
			<form name="form2" class="cad" onsubmit="return validacaoCad()" method="POST" action="cadastroEmpregrado2.php">
					<div class="cadTitulo">
						<h1>Cadastro</h1>
					</div>
				<br>
				<input placeholder="Nome" class="cadText" type="text" id="nome" name="Nome" required> <br>
				<div id="erroNome" class="erro"></div><br>
				<input type="mail" id="email" name="email" placeholder="E-Mail" class="cadText" required><br>
				<div id="erroEmail" class="erro"></div>
				<input type="submit" class="btnProx" name="Proximo">
			</form>
		</div>
	</body>
</html>