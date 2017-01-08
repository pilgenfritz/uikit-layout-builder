<?php
require("../../class/class.MySQLDb.php");
require("../../db.php");

if (isset($_POST['email']))
{
	if(mysql_num_rows(mysql_query("SELECT * FROM newsletter WHERE email='" . $_POST['email'] . "'")) > 0)
	{
		//html
		echo '<p>Seu e-mail já está cadastrado. Obrigado!  :)</p>';		
	}
	else
	{
		$sql = "INSERT INTO newsletter VALUES (NULL, '" . $_POST['nome'] . "', '" . $_POST['email'] . "','" . date('Y-m-d H:i:s') . "')";
		mysql_query($sql) or die(mysql_error());
		
		//html
		echo '<p>Seu e-mail foi cadastrado.  Obrigado!  :)</p>';
	}
}
 
else
{
	echo "<p>Erro ao enviar a mensagem. :(</p>";
	
}

?>