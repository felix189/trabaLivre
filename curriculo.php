<!-- Fazer o resto das telas (formaçao, exp, competencias, e a revisao)-->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Currículo</title>
    <link rel="stylesheet" type="text/css" href="base2.css">
    <link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
</head>
<body class="genBod">
    <ul>
        <li><a class="active" href="menu.php">Início</a></li>
        <li><a href="busca.php">Buscar</a></li>
        <li><a href="quemSomos.php">Quem Somos?</a></li>
        <li><a href="perfil.php">Perfil</a></li>
        <li><a href="curriculo.php">Currículo</a></li>
        <li class="sair"><a href="sair.php">Sair</a></li>
    </ul>

    <div class="curriculo">
        <form name="cur" action="ex3.php" method="POST" onsubmit="validaCur()">
                <h2>Informações Pessoais</h2><br>
                <input type="text" name="nome" placeholder="Nome"><br><br>
                <input type="text" name="email" placeholder="E-mail"><br><br>
                <input type="text" name="numero" placeholder="Número" maxlength="11"><br><br>
                <button type="button">Próximo</button>
            </div>
        </body>
</html>