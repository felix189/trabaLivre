<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Cadastro</title>
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
	</head>
	<body class="fundo2">
		<button class="btnVlt" onclick="cadastro2()"></button><br>
		<div class="boxEmp">
		<form name="form2" class="cad" onsubmit="return validacaoCad()" method="POST" action="ex2.php">
				<div class="cadTitulo">
					<h1>Cadastro</h1>
				</div>
			<?php 
				$nome=$_POST['nome'];
				$CNPJ=$_POST['CNPJ'];
				$DDD=$_POST['DDD'];
				$Tel=$_POST['Tel'];
			?>
			<br>
			<br>
			<input type="password" class="cadText" name="senha" placeholder="Senha"><br>
			<div id="erroSenha" class="erro"></div><br>
			<input type="password" class="cadText" name="confirmSenha" placeholder="Confirme a senha"><br>
			<div id="erroConfSenha" class="erro"></div>
			<input type="submit" name="enviador" class="btnProx" value="Efetuar cadastro">
		</form>
		</div>
	</body>
</html>