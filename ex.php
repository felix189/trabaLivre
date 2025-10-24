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
		//CADASTRO DE CANDIDATO
			$formOK=true;
			$nome=$_POST['nome'];
			$email=$_POST['email'];
			$senha=$_POST['senha'];
			$confirmSenha=$_POST['confirmSenha'];
			$tipoPerfil = "C";

			if (empty($senha)) {
				die("Senha precisa ser preenchida!");
			}
			if (empty($confirmSenha)) {
				die("Confirmação de senha precisa ser preenchida!");
			}
			if (empty($data)) {
				die("Data precisa ser preenchida!");
			}
			if (empty($nome)) {
				die("Nome precisa ser preenchido!");
			}
			if (empty($email)) {
				die("E-Mail precisa ser preenchido!");
			}

			require("bdconnecta.php");
			require("cryp2graph2.php");

			$senhaCrip = Fazsenha($nome, $confirmSenha);
			$numero2 = $DDD.$numero;

			$sql = 'insert into ta_usuarios (CPF, nome, email, senha, numero, tipoPerfil) values (?,?,?,?,?,?)';
			$stmt = mysqli_prepare($conn,$sql);

			if (!mysqli_stmt_bind_param($stmt,"ssssss", $CPF, $nome, $email, $senhaCrip, $numero2, $tipoPerfil)) {
				die("Não foi possível vincular parâmetros!");
			}
			if (!mysqli_stmt_execute($stmt)) {
				echo("Erro ao cadastrar pessoa (Email já cadastrado!)");
				die("<a href='cadastro.php'>voltar</button>");
			}
			//echo("Pessoa cadastrada!");
			if (!mysqli_stmt_close($stmt)) {
				echo ("Não foi possivel eliminar a conexão. Avise o setor de T.I.");
			// Prepara o SQL 
			}
		 ?>
		<span>Pessoa cadastrada com sucesso!</span><br>
		Vá para a:<br>
		<a href="index.php">Página de Login</a>
	</body>
</html>