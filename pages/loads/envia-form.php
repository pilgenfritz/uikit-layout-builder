<?php

date_default_timezone_set('America/Sao_Paulo');

//includes
require_once('../../db.php');
require('../../class/class.setup.php');
require '../../class/PHPMailer-master/PHPMailerAutoload.php';

//intances
$Setup = New Setup;
$config = $Setup->GeneralConfigVar();

//if campo-controle is empty (isn't a robot)
if (empty($_POST['campo-controle']))
{
	$dontList = array('MAX_FILE_SIZE','campo-controle','from','default-subject'); unset($mensagem);
	foreach ($_POST as $key => $value)
	{
		if(count(preg_split('/\n|\r/',$value)) > 1) $value = nl2br($value); //se textarea
		if(!in_array($key,$dontList)) $mensagem .= '<p><strong>' . ucfirst(str_replace(array('-','_'),' ',$key)) . ':</strong> ' . $value . '</p>';
	}

	//set is empty
	if(empty($_POST['assunto'])) $_POST['assunto'] = $_POST['default-subject'];

	//enviando
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
	$mail->Host = "smtp.cliente.com.br";
	$mail->Port = 587;
	$mail->SMTPAuth = true;
	$mail->Username = "site@cliente.com.br";
	$mail->Password = "Qweiop12";
	$mail->setFrom('site@cliente.com.br', 'Nome do Cliente');
	$mail->addReplyTo($_POST['email'], $_POST['nome']);
	//$mail->addAddress($config['company-contato'], 'Nome do Cliente');
	$mail->addAddress('paulo@agenciaready.com.br', 'Nome do Cliente');
	$mail->Subject = $_POST['assunto'];
	$mail->msgHTML($mensagem);
	foreach ($_FILES as $key => $value){
		$mail->addAttachment($_FILES[$key]['tmp_name']);
	}

	if (!$mail->send())
	{
	    echo 'false'; // . $mail->ErrorInfo;
	} else {
	    echo 'true';
	}
}
else
{
	echo 'false';
}