<?php
    require_once ("../_functions/conecta.php"); 

    function mascara(string $valor, string $mascara) {
        for ($i = 0; $i < strlen($valor); $i++) {
            $mascara[strpos($mascara, "#")] = $valor[$i];
        }
        return $mascara;
    }


    $nCodCli = $_REQUEST["nCodCli"];

    
    if ($nCodCli !== "") {
        $character = array(".", "/", "-", " ");
        $nCodCli = str_replace($character, "", $nCodCli);    
    }

    $nDocCli = "";
    $aNomCli = "";
 
    if(isset($nCodCli)){
      
        $conec = new conexao;
        $conec->conecta();
        
        $stid = $conec->query("SELECT CODCLI,NOMCLI,CGCCPF,TIPCLI FROM E085CLI WHERE CODCLI = $nCodCli");
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $nCodCli= strtoupper($row["CODCLI"]);
            $aNomCli= strtoupper($row["NOMCLI"]);
            $nDocCli= strtoupper($row["CGCCPF"]);
            $aTipCli= strtoupper($row["TIPCLI"]);
        } 

        
        $conec->desconecta();
        
        
        if($aTipCli == "F"){
            $nDocCli = str_pad($nDocCli, 11, "0", STR_PAD_LEFT);
            $nDocCli = mascara($nDocCli, "###.###.###-##"); // 123.456.789-01
        } elseif($aTipCli == "J"){
            $nDocCli = str_pad($nDocCli, 14, "0", STR_PAD_LEFT);
            $nDocCli = mascara($nDocCli, "##.###.###/####-##"); // 12.345.678/0001-90
        } else{
            $nDocCli = "0";
            $nCodCli = "";
            $aNomCli = "";
        }
        
    }
    echo $nDocCli."||".$nCodCli."||".$aNomCli;
    
?>