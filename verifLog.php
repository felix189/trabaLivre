<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login no Sistema</title>
	</head>
	<body>
		<h1>Login no Sistema</h1>
		<br>
		<?php 
			$email=$_POST['email'];
			$senha=$_POST['senha'];
			$tipoPerfil=$_POST['tipoPerfil'];
			if (empty($email)) {
				echo("email precisa ser preenchido!");
			}
			if (empty($senha)) {
				echo("Senha precisa ser preenchida!");
			}
			require("bdconnecta.php");
			
			if ($_POST['tipoPerfil'] == "C") {
				$sql="SELECT nome,senha,tipoPerfil FROM ta_usuarios WHERE email=?";
			}
			else {
				$sql="SELECT nome,senha,tipoPerfil FROM ta_empresa WHERE CNPJ=?";
			}
			// Prepara o SQL  
			$stmt = mysqli_prepare($conn,$sql);
			if (!$stmt) {
				die("Não foi possível preparar a consulta!");
			}
			if (!mysqli_stmt_bind_param($stmt,"s",$email)) {
				die("Não foi possível vincular parâmetros!");
			}
			if (!mysqli_stmt_execute($stmt)) {
				die("Não foi possível executar busca no Banco de Dados!");
			}
			// SÓ PARA SELECT
			if (!mysqli_stmt_bind_result($stmt, $nomeCompleto, $senhaCAD, $tipoPerfilBD) ) {
				die("Não foi possível vincular resultados");
			}
			$fetch=mysqli_stmt_fetch($stmt);
			if (!$fetch) {
				die("Não foi possível recuperar dados");
			}
			// Agora vamos ver o que voltou :D
			if ($fetch==null) {
				echo("Essa combinação de email/Senha não foi localizada!<br>");
				echo("Retorne para a <a href='index.php'>página de login</a>!");
			} else {
				// Neste ponto, estarão disponíveis as variáveis $nomeCompleto e $senhaCAD
				//    indicadas no bind_result
				// Agora O SEU PROGRAMA NORMAL
				require('cryp2graph2.php');
				$senha = ChecaSenha($senha,$senhaCAD);
				if ( $senhaCAD==$senha ) {
					// Usuário correto
					if (!session_start()) {
						die("Não foi possível iniciar sessão!");
					}
					$_SESSION['id']=session_id();
					$_SESSION['operador']=$email;
					$_SESSION['nomeOperador']=$nomeCompleto;
					$_SESSION['tipoPerfil']=$tipoPerfilBD;
					echo("Acesse o <a href='menu.php'>Menu do Sistema</a><br>");
					header("Location: menu.php");
					exit();
				} else {
					// Usuário errou login/senha
					echo("Essa combinação de CPF/Senha não foi localizada!");
					echo("Retorne para a <a href='index.php'>página de login</a>!");
				}
				if (!mysqli_stmt_close($stmt)) {
					echo("Não foi possível efetuar limpeza da conexão. Avise o setor de TI");
					// Mandar email/sms/alerta para o Programador
				}
			}
		?>
	</body>
</html>