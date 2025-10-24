<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<?php 
		$CNPJ=$_POST['CNPJ'];
		$cargo=$_POST['cargo'];
		$requisito=$_POST['requisito'];
		$descricao=$_POST['descricao'];

			require("bdconnecta.php");

			$sql = 'insert into ta_vagas (CNPJ, cargo, requisito, descricao) values (?,?,?,?)';
			$stmt = mysqli_prepare($conn,$sql);

			if (!mysqli_stmt_bind_param($stmt,"ssss", $CNPJ, $cargo, $requisito, $descricao)) {
				die("Não foi possível vincular parâmetros!");
			}
			if (!mysqli_stmt_execute($stmt)) {
				echo("Erro ao cadastrar vaga (Vaga já cadastrada!)");
				die("<a href='perfil.php'>voltar</button>");
			}
			//echo("Pessoa cadastrada!");
			if (!mysqli_stmt_close($stmt)) {
				echo ("Não foi possivel eliminar a conexão. Avise o setor de T.I.");
			// Prepara o SQL 
			}
		 ?>
		<span>Vaga cadastrada com sucesso!</span><br>
		Vá para a:<br>
		<a href="perfil.php">Perfil</a>
	?>

</body>
</html>