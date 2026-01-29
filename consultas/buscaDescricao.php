<?php
	require_once ("../_functions/conecta.php");

  
    if (isset($_GET['eNomTbl'])) { $aNomTbl = $_GET['eNomTbl']; } else { $aNomTbl = ''; }
	if (isset($_GET['eCmpFil'])) { $aCmpFil = $_GET['eCmpFil']; } else { $aCmpFil = ''; }
	if (isset($_GET['eValFil'])) { $aValFil = $_GET['eValFil']; } else { $aValFil = ''; }
	if (isset($_GET['eCmpRet'])) { $aCmpRet = $_GET['eCmpRet']; } else { $aCmpRet = ''; }
	if (isset($_GET['eWheAdd']) and ! $_GET['eWheAdd'] == "undefined") { $aWheAdd = $_GET['eWheAdd']; } else { $aWheAdd = ''; }


    $conec = new conexao;
	$conec->conecta();
	

	$stid = $conec->query("SELECT MSKFLD FROM R996FLD WHERE upper(TBLNAM) = upper('$aNomTbl') AND upper(FLDNAM) = upper('$aCmpFil')");
	while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
		/*
		-Tipo de Dados Mapeados
		* A = Alfa
		* I = Integer
		* D = Date
		* F = Float, Double ...	
		*/
        $aMskFld = strtoupper($row["MSKFLD"]);

		$aFilBnc = " $aCmpFil = $aValFil ";

		
		
		$pos = strrpos($aMskFld, ",");
		if (strrpos($aMskFld, ",") !== false) {
			$aTipBnc = "F";
		}
		if (strrpos($aMskFld, "D") !== false) {
			$aFilBnc = " $aCmpFil = to_date('$aValFil','DD/MM/YYYY')";
		}
		if (strrpos($aMskFld, "A") !== false) {
			$aFilBnc = " $aCmpFil = UPPER('$aValFil') ";
		}
		
     
    }
   


    $aFilBnc .= $aWheAdd;

    $aCmpPrc = "";
    $stid = $conec->query("SELECT $aCmpRet  AS RETCMP FROM $aNomTbl WHERE $aFilBnc");
    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
        $aCmpPrc = strtoupper($row['RETCMP']);
    }

    echo $aCmpPrc;
    
    $conec->desconecta();
    

?>

