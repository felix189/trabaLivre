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
		//CADASTRO DE EMPRESA
			$nome=$_POST['nome'];
			$CNPJ=$_POST['CNPJ'];
			$senha=$_POST['senha'];
			$confirmSenha=$_POST['confirmSenha'];
			$DDD=$_POST['DDD'];
			$Tel=$_POST['Tel'];
			$tipoPerfil = "E";

			//echo("O formulario enviou as seguintes variaveis<br>");
			//echo("CNPJ: $CNPJ<br>");
			//echo("Nome: $nome<br>");
			//echo("DDD: $DDD<br>");
			//echo("Tel: $Tel<br>");
			//echo("Senha: $senha<br>");
			//echo("Confirmar senha: $confirmSenha<br>");

			if (empty($senha)) {
				die("Senha precisa ser preenchida!");
			}
			if (empty($confirmSenha)) {
				die("Confirmação de senha precisa ser preenchida!");
			}
			if (empty($nome)) {
				die("Nome precisa ser preenchido!");
			}
			if (empty($CNPJ)) {
				die("E-Mail precisa ser preenchido!");
			}

			require("bdconnecta.php");
			require("cryp2graph2.php");
			$numero2 = $DDD.$tel;

			$senhaCrip = Fazsenha($nome, $confirmSenha);

			$sql = 'insert into ta_empresa (nome, CNPJ, senha, numero, tipoPerfil) values (?,?,?,?,?)';
			$stmt = mysqli_prepare($conn,$sql);

			if (!mysqli_stmt_bind_param($stmt,"ssssss", $nome, $CNPJ, $senhaCrip, $numero2, $tipoPerfil)) {
				die("Não foi possível vincular parâmetros!");
			}
			if (!mysqli_stmt_execute($stmt)) {
				echo("Erro ao cadastrar Empresa (CNPJ já cadastrado!)");
				die("<a href='cadastro.php'>voltar</button>");
			}
			//echo("Empresa cadastrada!");
			if (!mysqli_stmt_close($stmt)) {
				echo ("Não foi possivel eliminar a conexão. Avise o setor de T.I.");
			}
		 ?>
		<span>Empresa cadastrada com sucesso!</span><br>
		Vá para a:<br>
		<a href="index.php">Página de Login</a>
			?>
	</body>
</html>