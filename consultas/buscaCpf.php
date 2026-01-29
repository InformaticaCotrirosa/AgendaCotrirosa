<?php
    require_once ("../_functions/conecta.php"); 

    function mascara(string $valor, string $mascara) {
        for ($i = 0; $i < strlen($valor); $i++) {
            $mascara[strpos($mascara, "#")] = $valor[$i];
        }


        return $mascara;
    }

    function validarCPF($cpf) {
        $cpf = str_pad($cpf, 11, "0", STR_PAD_LEFT);
 
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
        
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }

    function validarCnpj($cnpj)
    {
        $cnpj = str_pad($cnpj, 14, "0", STR_PAD_LEFT);

        $invalidos = [
            '00000000000000',
            '11111111111111',
            '22222222222222',
            '33333333333333',
            '44444444444444',
            '55555555555555',
            '66666666666666',
            '77777777777777',
            '88888888888888',
            '99999999999999'
        ];
        
        // Verifica se o CNPJ está na lista de inválidos
        if (in_array($cnpj, $invalidos)) {	
            return false;
        }

        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        
        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;	

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
?>


<?php

    $nDocCli = $_REQUEST["nDocCli"];

    
    if ($nDocCli !== "") {
        $character = array(".", "/", "-", " ");
        $nDocCli = str_replace($character, "", $nDocCli);    
    }

    $validar = true;
    if(strlen($nDocCli)  <= 11){
        if(validarCPF($nDocCli)) {
            $nDocCli = str_pad($nDocCli, 11, "0", STR_PAD_LEFT);
            $nDocCli = mascara($nDocCli, "###.###.###-##"); // 123.456.789-01
            $validar = false;
        }
    }
                                         
    if (($nDocCli == 191) || ($validar)){
        if(validarCnpj($nDocCli)){
            $nDocCli = str_pad($nDocCli, 14, "0", STR_PAD_LEFT);
            $nDocCli = mascara($nDocCli, "##.###.###/####-##"); // 12.345.678/0001-90
        } else {
            $nDocCli = "";
        }
    }

    if($nDocCli !== ""){
        $character = array(".", "/", "-", " ");
        $nDocPur = str_replace($character, "", $nDocCli); 
        $nCodCli = 0;

        $conec = new conexao;
	    $conec->conecta();
        
        $stid = $conec->query("SELECT CODCLI,NOMCLI FROM E085CLI WHERE CGCCPF = $nDocPur");
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $nCodCli= strtoupper($row["CODCLI"]);
            $aNomCli= strtoupper($row["NOMCLI"]);
        }

        if($nCodCli == 0){
            $stid = $conec->query("SELECT (ULTNUM + 1) AS CODCLI FROM E078ULT WHERE CodEmp=0 and CodFil=0 AND Cambas='CODCLI'");
            while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
                $nCodCli= strtoupper($row["CODCLI"]);
                $aNomCli= "CADASTRO NOVO";
            }
        }

        $conec->desconecta();



        
    }

    echo $nDocCli."||".$nCodCli."||".$aNomCli;

?>