<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Recuperação desenha</title>
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
	</head>
	<body>
		<?php
		$email=$_POST['email'];
		require("bdconnecta.php");
		require("cryp2graph2.php");
		require("email.php");

		$sql="SELECT nome,senha,email FROM ta_usuarios WHERE email=? ";
			// Prepara o SQL  
			$stmt = mysqli_prepare($conn,$sql);
			if (!$stmt) {
				die("Não foi possível preparar a consulta!");
			}
			if (!mysqli_stmt_bind_param($stmt,"s",$CPF)) {
				die("Não foi possível vincular parâmetros!");
			}
			if (!mysqli_stmt_execute($stmt)) {
				die("Não foi possível executar busca no Banco de Dados!");
			}
			// SÓ PARA SELECT
			if (!mysqli_stmt_bind_result($stmt, $nomeCompleto,$senhaCAD,$emailCAD)) {
				die("Não foi possível vincular resultados");
			}
			$fetch=mysqli_stmt_fetch($stmt);
			if (!$fetch) {
				die("Não foi possível recuperar dados");
			}
			if (!mysqli_stmt_close($stmt)) {
					echo("Não foi possível efetuar limpeza da conexão. Avise o setor de TI");
					// Mandar email/sms/alerta para o Programador
			}
			// Agora vamos ver o que voltou :D
			if ($fetch==null) {
				echo("CPF não foi localizado!<br>");
				echo("Retorne para a <a href='login01.php'>página de login</a>!");
			}else {
				$senhaNova=CriaAlgo(8); // L5H2G7J4
				$mensagem="Sua nova senha é: $senhaNova";
				// Criptografia a senha para salvar no BD
				$senhaNova=FazSenha($email,$senhaNova);
				$sql2="UPDATE ta_usuarios set senha='$senhaNova' where email='$email'";
				$resultado=mysqli_query($conn,$sql2);
				if (!$resultado) {
					Die("Não foi possivel registrar senha nova no BD");
				}
				$resultadoEmail=mandarEmail($nomeCompleto,$emailCAD,"Recuperação de Senha", $mensagem);
				if (!$resultadoEmail) {
					die("Não foi possivel enviar email com a nova senha");
				}
				if (!mysqli_stmt_close($stmt)) {
					echo("Não foi possível efetuar limpeza da conexão. Avise o setor de TI");
					// Mandar email/sms/alerta para o Programador
				}
			}
		  ?>
		  Nova senha enviada com sucesso! Verifique a sua caixa de emails (e também o SPAM, etc)!<br>
	</body>
</html>