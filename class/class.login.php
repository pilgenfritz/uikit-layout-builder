<?php
error_reporting(0);

if (eregi("class.login-usuario.php",$PHP_SELF)) {
    Header("Location: /404.php");
    die();
}

session_start();

class Usuario {
	var $cnome;
	var $sessid;
	var $cuid;
	var $climite;
	var $slimite;
	var $ctempo;	
	var $agora;
	var $data;	
	var $uid;
	var $nome;
	var $unome;
	var $email;
	var $usrval;

	
	public function Usuario()
	{
		global $_GET, $_COOKIE;
		$this->cnome = "usercookie";
		$this->ip = $this->Get_IP();
		$this->agora = date("YmdHis");
		$this->data = date("Y-m-d");
		$this->slimite = date("YmdHis",time()+1440);

		if(isset($_COOKIE[$this->cnome])) {
			$this->Get_Usr_Cookie();
			if($this->Validar()) $this->Renov_Sess();
		}
	}
	
	public function Get_Usr_Cookie() {
		global $_COOKIE;
		$arr = explode('::',base64_decode($_COOKIE[$this->cnome]));
		$this->cuid   = $arr[0];
		$this->sessid = $arr[1];
		$this->ctempo = $arr[2];
		$this->cunome = $arr[3];
	}

	public function Get_Cookie() {
		$cookie = array($this->cuid,$this->sessid,$this->ctempo,$this->cunome);
		return $cookie;
	}
	
	public function Validar() { 
		$result = mysql_query("SELECT * FROM cadastros WHERE id='$this->cuid'");
		if(mysql_num_rows($result) == 1) {
			if($this->unome == $this->cunome) {
				if($this->Val_Sess()){
					$this->usrval = 1;
					return true;
				}
			}

			$arr = mysql_fetch_array($result);
			foreach ($arr as $key => $val) {
				$this->$key = $val;
			}
			
		}
		return false;
	}
	
	public function Val_Sess() {
		@mysql_query("DELETE FROM cadastros_sessao WHERE stempo < '$this->agora'");
		$result = mysql_query("SELECT * FROM cadastros_sessao WHERE sessid='$this->sessid' AND uid='$this->cuid' AND stempo='$this->ctempo' AND ip ='$this->ip'");
		if(mysql_num_rows($result) == 1) 
		return true;
		return false;
	}

	public function Renov_Sess() {
		@mysql_query("UPDATE cadastros_sessao SET stempo='$this->slimite' WHERE sessid='$this->sessid' AND uid='$this->uid'");
		$this->NovoCookie();
	}
	
	public function NovaSess(){
		$this->sessid = $this->Nova_Sessid();
		mysql_query("DELETE FROM cadastros_sessao WHERE uid='$this->uid'");
		mysql_query("INSERT INTO cadastros_sessao values('$this->sessid','$this->uid','$this->slimite','$this->ip')");
		$this->NovoCookie();
		$this->usrval = 1;
	}
	
	public function Nova_Sessid() {
    	srand((double)microtime()*1000000);
	    return(substr(md5(rand(0,999999)),0,32));
	}
	
	public function NovoCookie() {
		$s = base64_encode($this->uid."::".$this->sessid."::".$this->slimite."::".$this->unome);
		setcookie($this->cnome,$s);
	}
	
	public function Get_IP() {
		return getenv("REMOTE_ADDR");
	}

	public function usrval(){
		//echo '//' . $this->usrval  . '//';
		if(is_numeric($_SESSION['usrval'])){
			return true; 
		}
		return false;
	}

	public function userData()
	{
		$usr = mysql_fetch_array(mysql_query("SELECT * FROM cadastros WHERE id='" . $_SESSION['usrval'] . "' LIMIT 1"));
		return array($usr['nome'],$usr['login'],$usr['id']);		
	}


	public function Logout() {
		mysql_query("DELETE FROM cadastros_sessao WHERE uid='$this->uid'");
		mysql_query("DELETE FROM empresas_sessao WHERE uid='$this->uid'");
		setcookie($this->cnome,"");
		unset($_SESSION['usrval']);
	}
	
	public function Logar($login,$senha) {

		global $_COOKIE;
		$senha = md5($senha);
		$result = mysql_query("SELECT id FROM cadastros WHERE login='$login' AND senha='$senha'");
		$time = date("YmdHis", mktime(date(H),date(i),date(s), date(m), date(d)-2,date(Y)));


		if(mysql_num_rows($result) == 1) {
			list($id) = mysql_fetch_row($result);
			$this->uid = $id;
			$this->unome = $login;
			$this->cuid = $id;
			$_SESSION['usrval']=$id;
			$this->NovaSess();
			$this->Validar();
			
			return true;
		}
		return false;		
	}
	
	public function retorna($v){
		return $this->$v;
	}
	
}