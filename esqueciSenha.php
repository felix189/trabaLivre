<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Recuperação de senha</title>
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
	</head>
	<body>
		<form name="form3" class="recSenha" onsubmit="return validacaoRS()" method="POST" action="enviaEmail.php">
			<h1>Recuperar senha</h1><br>
			Email: <input type="text" name="email" placeholder="seuemail@gmail.com"> <br>
			<div id="erroEmail" class="erro"></div><br>
			<input type="submit" name="envia" value="Enviar email">
		</form>
	</body>
</html>