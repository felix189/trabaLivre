<?php 
	$conn = mysqli_connect('localhost', 'TrabaLivre', 'Traba1829', 'trabalivre');
	if (!$conn) {
		die("Erro ao conectar ao Banco de Dados");
	}
	date_default_timezone_set('Brazil/East');
	mysqli_query($conn, "SET NAMES 'utf8'");
?>