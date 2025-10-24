<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Trabalivre - Menu</title>
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
	</head>
	<body>
		<?php
			require ('bdconnecta.php');
			require ('SessionStart.php');
			$nome = $_SESSION ['nomeOperador'];
			if ($_SESSION['tipoPerfil'] == "C") {
					echo "<ul>
						<li><a class='active' href='menu.php'>Início</a></li>
						<li><a href='busca.php'>Buscar</a></li>
				  		<li><a href='quemSomos.php'>Quem Somos?</a></li>
				  		<li><a href='perfil.php'>Perfil</a></li>
				  		<li><a href='curriculo.php'>Currículo</a></li>
				  		<li class='sair'><a href='sair.php'>Sair</a></li>
					</ul>";
				}
				else if ($_SESSION['tipoPerfil'] == "E" || $_SESSION['tipoPerfil'] == "A") {
					echo "<ul>
						<li><a class='active' href='menu.php'>Início</a></li>
						<li><a href='busca.php'>Buscar</a></li>
				  		<li><a href='quemSomos.php'>Quem Somos?</a></li>
				  		<li><a href='perfil.php'>Perfil</a></li>
				  		<li><a href='vaga.php'>Anunciar Vaga</a></li>
				  		<li class='sair'><a href='sair.php'>Sair</a></li>
					</ul>";
				}
			echo ("<h1>Bem-Vindo(a) $nome</h1><br>");
			echo ("<h2>Vagas recomendadas</h2><br><br>");
		?>
	</body>
</html>