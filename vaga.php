<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
		<title>Anunciar Vagas</title>
	</head>
	<body>
		<ul>
			<li><a class="active" href="menu.php">Início</a></li>
			<li><a href="busca.php">Buscar</a></li>
	  		<li><a href="quemSomos.php">Quem Somos?</a></li>
	  		<li><a href="perfil.php">Perfil</a></li>
	  		<li><a href="vaga.php">Anunciar Vaga</a></li>
	  		<li class="sair"><a href="sair.php">Sair</a></li>
		</ul>
		<form name="vaga" action="ex4.php" method="POST" onsubmit="validaCur()">
		CNPJ:
		<input type="text" name="CNPJ" maxlength="14"><br>
		Cargos:
		<input type="text" name="cargo" maxlength="50"><br>
		Requisito:
		<input type="text" name="requisito" maxlength="150"><br>
		Descrição:
		<input type="text" name="descricao" maxlength="250"><br>
		<input type="submit" name="enviador" value="Enviar currículo">
		</form>
	</body>
</html>