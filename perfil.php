<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
		<title>Página de Perfil</title>
	</head>
	<body>
		<?php 
			require("bdconnecta.php");
			require("SessionStart.php");
			$nome = $_SESSION ['nomeOperador'];
			if ($_SESSION['tipoPerfil'] == "C"  || $_SESSION['tipoPerfil'] == "A") {
				echo "<ul>
			<li><a class='active' href='menu.php'>Início</a></li>
			<li><a href='busca.php'>Buscar</a></li>
	  		<li><a href='quemSomos.php'>Quem Somos?</a></li>
	  		<li><a href='perfil.php'>Perfil</a></li>
	  		<li><a href='curriculo.php'>Currículo</a></li>
	  		<li class='sair'><a href='sair.php'>Sair</a></li>
		</ul>";
				echo ("<h1>$nome</h1><br>");
				echo ("<img class='ftPerfil' src='imagens/pessoinha.png'>");
				echo ("-colocar email<br>");
				echo ("-colocar telefone<br>");
				echo ("-colocar informações do currículo, ou o próprio currículo");
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
				echo ("<h1>$nome</h1><br>");
				echo ("<img class='ftPerfil' src='imagens/pessoinha.png'>");
				echo ("-colocar CNPJ<br>");
				echo ("-colocar Contato<br>");
				echo ("-colocar descrição");
			}
			else {
				
			}
		?>
	</body>
</html>