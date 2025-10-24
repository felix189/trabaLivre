<?php
	require("SessionStart.php");
	$CPF=$_SESSION['operador'];
	$nomeCompleto=$_SESSION['nomeOperador'];
	echo("<h1>Olá $nomeCompleto</h1>");
?>
<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function mandarEmail($nomeDestinatario,$To,$Subject,$Message) {

	require('./src/PHPMailer.php');
	require('./src/Exception.php');
	require('./src/SMTP.php');

	$mail = new PHPMailer;
	//$mail->SMTPDebug = 2;									// Enable verbose debug output
	$mail->isSMTP();										// Set mailer to use SMTP
	$mail->Charset = 'UTF-8';
	$mail->Host = 'smtp.gmail.com';  		  				// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;									// Enable SMTP authentication
	//$mail->SMTPSecure = 'tls';							// Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;										// TCP port to connect to
	$mail->Username = '';         	// SMTP username
	$mail->Password = ''; // senha do email				// SMTP password
	$mail->From = '';
	$mail->FromName = utf8_decode('Recuperação de Senha');
	if (gettype($To)=="array") {
		foreach ($To as $key => $value) {
			$mail->addAddress($value);  // Add a recipient
		}
	} else {
		$mail->addAddress($To);  // Add a recipient
	}
	$mail->isHTML(true);									// Set email format to HTML
	//$mail->addAttachment('/var/tmp/file.tar.gz');			// Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name

	$Message.="<br><br>Equipe de contas do servidor do Trabalivre"; // Coloque seu RM aqui
	$mail->Subject = utf8_decode($Subject);
	$mail->Body    = utf8_decode($Message);
	$mail->AltBody = 'Seu email precisa ser capaz de usar HTML para mostrar essa mensagem! Verifique!';

	if(!$mail->send()) {
	    echo('Mensagem nao pode ser enviada: Mailer Error: ' . $mail->ErrorInfo);
	    return false;
	} else {
	    echo("Email enviado para $nomeDestinatario, $Subject");
	    return true;
	}	
	//print_r($mail);
	//die();
} // termina aqui o mandarEmail

?>