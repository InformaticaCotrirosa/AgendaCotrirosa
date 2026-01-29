<?php

  
    if (isset($_GET['eNomLis'])) { $aNomLis = $_GET['eNomLis']; } else { $aNomLis = ''; }
	if (isset($_GET['eValLis'])) { $aValLis = $_GET['eValLis']; } else { $aValLis = ''; }

    
    if(isset($aNomLis) && isset($aValLis)){
        echo retornaLista($aNomLis,$aValLis);
    }   

    function retornaLista($aNomLis,$aValLis){
        $aValKey = "";
        $conec = new conexao;
        $conec->conecta();

        $stid = $conec->query("SELECT VALKEY FROM R996LSF WHERE UPPER(LSTNAM) = UPPER('$aNomLis') AND UPPER(KEYNAM) = UPPER('$aValLis')");
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $aValKey= strtoupper($row["VALKEY"]);
        } 
        $conec->desconecta();

        return $aValKey;
    }

    
    function optionLista($aNomLis,$aDefLis,$empty){
        
        $aValKey = "";
        $conec = new conexao;
        $conec->conecta();

        if(isset($empty) && $empty){
            $OptRet.= "<option value=''";
            if("" == $aDefLis){ 
                $OptRet.= " selected ";	
            }
            $OptRet.= "> </option>";
        }

        $stid = $conec->query("SELECT KEYNAM,VALKEY FROM R996LSF WHERE UPPER(LSTNAM) = UPPER('$aNomLis')");
        while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $aKeyNam= strtoupper($row["KEYNAM"]);
            $aValKey= strtoupper($row["VALKEY"]);

            $OptRet.= "<option value='".$aKeyNam."'";
            if($aKeyNam == $aDefLis){ 
                $OptRet.= " selected ";	
            }
            $OptRet.= ">$aKeyNam - $aValKey</option>";
        } 
        $conec->desconecta();
        
        return $OptRet; 
      
    }

?>