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
		<button class="btnVlt" onclick="cadastro1()"></button><br>
		<br>
		<div class="boxEmp">
			<form name="form2" class="cad" onsubmit="return validacaoCad()" method="POST" action="ex.php">
					<div class="cadTitulo">
						<h1>Cadastro</h1>
					</div>
				<?php 
					$nome=$_POST['nome'];
					$email=$_POST['email'];
				?>
				<br>
				<input type="password" name="senha" placeholder="Senha" class="cadText" onblur="senhaTam()" required oninvalid="this.setCustomValidity('Coloque sua senha aqui')"><br>
				<div id="erroSenha" class="erro"></div><br>
				<input type="password" name="confirmSenha" placeholder="Confirme a senha" class="cadText" onblur="igual()" required oninvalid="this.setCustomValidity('Confirme sua senha')"><br>
				<div id="erroConfSenha" class="erro"></div>
				<input type="submit" name="enviador" class="btnProx" value="Efetuar cadastro">
			</form>
		</div>
	</body>
</html>