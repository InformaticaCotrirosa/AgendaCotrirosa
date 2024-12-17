<?php 

class user {
	private $nCodUsu;
	private $aNomUsu;
	private $aSenUsu;
	private $aLogUsu;
	private $aPerUsu;
	private $aLocTrb;
	private $aVerErp;
	private $aPerCmp;
	
	private $aStaLog = false;
	
	function nCodUsu(){
		return $this->nCodUsu; 
	}
	
	function aNomUsu(){
		return $this->aNomUsu; 
	}
	
	function aPerUsu(){
		return $this->aPerUsu; 
	}
	
	function aStaLog(){
		return $this->aStaLog; 
	}
	
	function aLocTrb(){
		return $this->aLocTrb; 
	}
	
	function aVerErp(){
		return $this->aVerErp; 
	}
	
	function aLogUsu(){
		return $this->aLogUsu; 
	}
	
	function aSenUsu(){
		return $this->aSenUsu; 
	}
	
	
	function login($eLogUsu,$eSenUsu){
		
		$wsdl = 'http://192.168.0.149:8080/g5-senior-services/sapiens_Synccom_senior_g5_co_ger_cad_usuario?wsdl'; 
		
		
		if (empty($eLogUsu) OR empty($eSenUsu) OR is_null($eLogUsu) OR is_null($eSenUsu)) {
			exit;
		}
		echo 'tste2';
		$function = 'loginHelpDesk';	
		$client = new SoapClient($wsdl);
		$arguments= array(
			'user'        => $eLogUsu,
			'password'    => $eSenUsu,
			'encryption'  => '0',
			'parameters'  => array(
				'ALogUsu'  => $eLogUsu
			)
		);
		$result = $client->__soapCall($function, $arguments);
		$result = json_decode(json_encode($result), True);
	
		$this->aNomUsu = $result['ANomUsu'];
		$this->nCodUsu = $result['NCodUsu'];
		$this->aVerErp = $result['ASapVer'];
		
		
		if($this->nCodUsu > 0){
			$this->aSenUsu = $eSenUsu;
			$this->aLogUsu = $eLogUsu;
			$this->getPermissoes();
			$this->aStaLog = true;
		} else {
			echo "<script>alert('Login Recusado ".$_POST['usuario']."');</script>"; 
		} 
	
	}
	
	function logout(){
		unset($this->nCodUsu);
		unset($this->aNomUsu);
		unset($this->aSenUsu);
		unset($this->aLogUsu);
		unset($this->aPerUsu);
		unset($this->aPerCmp);	
		$this->aStaLog = false;	
		unset($_SESSION['user']);
	}
	
	function getPermissoes(){
		unset($this->aPerUsu);
		$conec = new conexao;
		$conec->conecta();
		$stid = $conec->query("SELECT UPPER(R910ENT.NomEnt) NOMENT FROM R910MGP,R910ENT
										WHERE R910MGP.CODGRP = R910ENT.CODENT and R910MGP.CODMBR=".$this->nCodUsu);
		while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
			$this->aPerUsu[] = strtoupper($row["NOMENT"]);
		}
		$conec->desconecta();
	}
	
	function getPermissao($aNomPer){
		if (in_array(strtoupper($aNomPer), $this->aPerUsu)) { 
			return true;
		}
		return false;
	}
	function setPermissaoCampo($aNomCmp){
		$this->aPerCmp[] = strtoupper($aNomCmp);		
	}

	function getPermissaoCampo($aNomCmp){
		if (in_array(strtoupper($aNomCmp), $this->aPerCmp)) { 
			return true;
		}
		return false;
	}

	function getPermissaoHtml($aNomCmp){
		if (in_array(strtoupper($aNomCmp), $this->aPerCmp)) { 
			return '';
		}
		return "disabled";
	}

	function clrPermissaoCampo(){
		unset($this->aPerCmp);
	}
}

class conexao {
	var $con;
	var $retorno;

	function conecta(){
			
		try {
			$this->con = oci_connect('erp_prd', 'a5hdg83JHY87', 'PROD','AL32UTF8');//'WE8ISO8859P1');
			if (!$this->con) {
				$this->retorno = oci_error()['message'];
	        	$this->retorno = "ERRO DE CONEXÃO - SERVIDOR!<br>".$this->retorno ;	
				$this->alert($this->retorno);
				$this->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY'");
			} else { 
	            $this->retorno = "";
			}
		} catch(Exception $e) {
			$this->retorno = oci_error()['message'];
	        $this->retorno = "ERRO DE CONEXÃO - SERVIDOR!<br>".$this->retorno."<br>".$e->getMessage();
			$this->alert($this->retorno);
		}
	}

	function desconecta(){
		oci_close($this->con);
		if (!$this->con) {
			$this->retorno = oci_error()['message'];
	        $this->retorno = "ERRO DE CONEXÃO - SERVIDOR!<br>".$this->retorno ;		
			$this->alert($this->retorno);
		}
	}
	
	function query($aQueExc){
		$exec = oci_parse($this->con, $aQueExc);
		$reto = oci_execute($exec,OCI_COMMIT_ON_SUCCESS);
		
		if (!$reto) {
			$erro = oci_error($exec)['message'];
			$this->retorno = "ERRO DE QUERY!<br>".$erro;
			$this->alert($this->retorno);
		}
		
		return $exec;
	}

	function alert($mesg){
		echo "<script>alert('$mesg');</script>";
	}
}
?>