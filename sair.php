<?php 
	session_destroy();
	unset($_SESSION['id']);
	unset($_SESSION['operador']);
	unset($_SESSION['nomeOperador']);
	header("Location: index.php");
	exit();
?>