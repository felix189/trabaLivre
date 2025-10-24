<?php
	if (!session_start()) {
		die("Não foi possível iniciar a sessão!");
	}
	if (!isset($_SESSION['id'])) {
		ob_clean();
		header("location: index.php");
	}
?>