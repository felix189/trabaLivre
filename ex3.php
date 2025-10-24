<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
	</head>
	<body>
		
		<?php
			$nome=$_POST['nome'];
			$email=$_POST['email'];
			$numero=$_POST['numero'];
			$competencia=$_POST['competencia'];
			$experiencia=$_POST['experiencia'];
			$formacao=$_POST['formacao'];
			$descricao=$_POST['descricao'];

			require("bdconnecta.php");

			$sql = 'insert into ta_curriculo (nome, email, numero, formacao, experiencia, competencia, descricao) values (?,?,?,?,?,?,?)';
			$stmt = mysqli_prepare($conn,$sql);

			if (!mysqli_stmt_bind_param($stmt,"sssssss", $nome, $email, $numero, $formacao, $experiencia, $competencia, $descricao)) {
				die("Não foi possível vincular parâmetros!");
			}
			if (!mysqli_stmt_execute($stmt)) {
				echo("Erro ao cadastrar pessoa (Email já cadastrado!)");
				die("<a href='perfil.php'>voltar</button>");
			}
			//echo("Pessoa cadastrada!");
			if (!mysqli_stmt_close($stmt)) {
				echo ("Não foi possivel eliminar a conexão. Avise o setor de T.I.");
			// Prepara o SQL 
			}
		 ?>
		<span>Currículo cadastrada com sucesso!</span><br>
		Vá para a:<br>
		<a href="perfil.php">Perfil</a>
	</body>
</html>