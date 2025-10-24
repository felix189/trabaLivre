<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
	</head>
<body class="bodyL">
    <div class="loginL">
        <div class="logoL">
            <img src="imagens/LogoPNG.png">
        </div>
        <div class="login2">
            <form name="form1" class="login" onsubmit="return validacaoLog()" method="POST" action="verifLog.php">
                <h1>Bem-vindo(a)!</h1>
                <select name="tipoPerfil" id="tipoPerfil" required>
                    <option value=" " disabled selected>Selecione o perfil</option>
                    <option value="C">Candidato</option>
                    <option value="E">Empresa</option>
                </select><br><br>
                <input type="text" class="inText" name="email" placeholder="Email/CNPJ">
                <div id="erroEmail" class="erro"></div><br>
                <input type="password" class="inText" name="senha" placeholder="Senha">
                <div id="erroSenha" class="erro"></div><br>
                <a name="esq" onclick="esqueci()" class="Forgot">Esqueci minha senha</a>
                <input type="submit" name="envia" class="btnLog" value="Entrar">
                <p>Não tem uma conta? <a name="cad" href="cadastro1.php" class="crad">Cadastre-se</a></p>
            </form>
        </div>
    </div>
</body>
</html>