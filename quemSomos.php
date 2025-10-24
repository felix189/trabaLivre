<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
		<title>Trabalivre - Quem Somos</title>
	</head>
	<body class="genBod">
		<?php 
			require ('bdconnecta.php');
			require ('SessionStart.php');
			$nome = $_SESSION ['nomeOperador'];
			if ($_SESSION['tipoPerfil'] == "C" || $_SESSION['tipoPerfil'] == "A") {
					echo "<ul>
						<li><a class='active' href='menu.php'>Início</a></li>
						<li><a href='busca.php'>Buscar</a></li>
				  		<li><a href='quemSomos.php'>Quem Somos?</a></li>
				  		<li><a href='perfil.php'>Perfil</a></li>
				  		<li><a href='curriculo.php'>Currículo</a></li>
				  		<li class='sair'><a href='sair.php'>Sair</a></li>
					</ul>";
				}
				else if ($_SESSION['tipoPerfil'] == "E") {
					echo "<ul>
						<li><a class='active' href='menu.php'>Início</a></li>
						<li><a href='busca.php'>Buscar</a></li>
				  		<li><a href='quemSomos.php'>Quem Somos?</a></li>
				  		<li><a href='perfil.php'>Perfil</a></li>
				  		<li><a href='vaga.php'>Anunciar Vaga</a></li>
				  		<li class='sair'><a href='sair.php'>Sair</a></li>
					</ul>";
				}
		?>
    <div class="quemH">
        <h2>Quem somos?</h2>
    </div>
    <div class="quemDiv">
        <div class="criador">
            <img src="">
            <h3>Abraão F.</h3>
            <p>(pequeno texto)</p>
            <p class="Forgot">Rede social</p>
        </div>
        <div class="criador">
            <img src="">
            <h3>Lilliath A.</h3>
            <p>(pequeno texto)</p>
            <p class="Forgot">Rede social</p>
        </div>
        <div class="criador">
            <img src="">
            <h3>Emanuelly C.</h3>
            <p>(pequeno texto)</p>
            <p class="Forgot">Rede social</p>
        </div>
        <div class="criador">
            <img src="">
            <h3>João Henrique V.</h3>
            <p>(pequeno texto)</p>
            <p class="Forgot">Rede social</p>
        </div>
    </div>
    <div class="contato">
        <h2>Entre em contato conosco</h2>
        <div class="contatoForm">
            <input type="text" class="contatoIn" placeholder="Nome Completo">
            <input type="text" class="contatoIn" placeholder="Assunto">
            <textarea class="contatoIn" placeholder="Mensagem"></textarea>
            <button class="btnProx">Enviar e-mail</button>
        </div>
    </div>
</div>
	</body>
</html>