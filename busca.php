<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="base2.css">
		<link rel="icon" type="image/x-icon" href="imagens/LogoPNG.png">
		<script type="text/javascript" src="base.js"></script>
		<title>Busca de Perfil</title>
	</head>
	<body>
		<form action="busca.php" name="formu1" method="POST">
			<?php
				require ('SessionStart.php');
				require ('bdconnecta.php');
				$tamPagina = 5;
				if(isset($_POST['tamPaginaz'])){
					$tamPagina = $_POST['tamPaginaz'];
				}
				$regInicial = 0;
				if (isset($_POST['pag'])) {
					$regInicial = ($_POST['pag'] - 1) * $tamPagina;
				}
				if ($_SESSION['tipoPerfil'] == "C"  || $_SESSION['tipoPerfil'] == "A") {
					echo "<ul>
						<li><a class='active' href='menu.php'>Início</a></li>
						<li><a href='busca.php'>Buscar</a></li>
				  		<li><a href='quemSomos.php'>Quem Somos?</a></li>
				  		<li><a href='perfil.php'>Perfil</a></li>
				  		<li><a href='curriculo.php'>Currículo</a></li>
				  		<li class='sair'><a href='sair.php'>Sair</a></li>
						</ul>";
						$sql = 'SELECT * FROM ta_vagas';
						$dataSet = mysqli_query($conn, $sql);
						$qtdeRegistros = mysqli_num_rows($dataSet);
						$sql = "SELECT * FROM ta_vagas ORDER BY CNPJ LIMIT $tamPagina OFFSET $regInicial ";
						//die($sql);
						$dataSet = mysqli_query($conn, $sql);
						echo ("<br><br><br>");
						echo ("<table>");
						echo ("<th> CNPJ </th>");
						echo ("<th> Nome Empresa </th>");
						echo ("<th> Cargos </th>");
						echo ("<th> Requisito </th>");
						echo ("<th> Descrição </th>");
						echo ("<th> Perfil Da Empresa </th>");
						$i = 0;
						while ($linhaBD=mysqli_fetch_assoc($dataSet)) {
							echo ("<tr> <td>".$CNPJ = $linhaBD['CNPJ']."</td>");
							echo ("<td> ".$nome = $linhaBD['nome']." </td>");
							echo ("<td> ".$cargo = $linhaBD['cargo']." </td>");
							echo ("<td>".$requisito = $linhaBD['requisito']."</td>");
							echo ("<td> ".$descricao = $linhaBD['descricao']." </td>");
							echo ("<td> ".$descricao = $linhaBD['descricao']." </td> </tr>");
							$i.'1';
						}
						echo ("</table>");
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
						$sql = 'SELECT * FROM ta_usuarios';
						$dataSet = mysqli_query($conn, $sql);
						$qtdeRegistros = mysqli_num_rows($dataSet);
						$sql = "SELECT * FROM ta_usuarios ORDER BY CPF LIMIT $tamPagina OFFSET $regInicial ";
						//die($sql);
						$dataSet = mysqli_query($conn, $sql);
						echo ("<br><br><br>");
						echo ("<table>");
						echo ("<th> Nome </th>");
						echo ("<th> Email </th>");
						echo ("<th> Numero </th>");
						echo ("<th> Currículo </th>");
						$i = 0;
						while ($linhaBD=mysqli_fetch_assoc($dataSet)) {
							echo ("<tr> <td>".$nome = $linhaBD['nome']."</td>");
							echo ("<td> ".$email = $linhaBD['email']." </td>");
							echo ("<td>".$numero = $linhaBD['numero']."</td>");
							echo ("<td> ".$perfilUsuario = $linhaBD['data']." </td> </tr>");
							$i.'1';
						}
						echo ("</table>");
				}

			?>
			<br><div class="Controle_Populacional">
				Quantidade de registros visíveis:
				<select name="tamPaginaz">
					<option <?php if ($tamPagina == 5) { echo "selected"; }?>>5</option>
					<option <?php if ($tamPagina == 10) { echo "selected"; }?>>10</option>
					<option <?php if ($tamPagina == 15) { echo "selected"; }?>>15</option>
					<option <?php if ($tamPagina == 20) { echo "selected"; }?>>20</option>
				</select><br>
				<input type="submit" name="bah" value="Aumentar/Diminuir página"><br>
				<?php 
					$qtdePaginas = $qtdeRegistros/$tamPagina;
					if ($qtdeRegistros%$tamPagina!=0) {
						$qtdePaginas ++;
					}
					for ($pag=1; $pag <= $qtdePaginas; $pag++) { 
						echo("<input type='submit' name='pag' value='$pag'>");
					}
				 ?>
			</div>
		</form>
	</body>
</html>