<?php
require_once("./_functions/conecta.php");
require_once("./consultas/processaLista.php");
require_once("./_functions/montaCampos.php");
require_once("./save/cadastro.php");
require_once("./_functions/postArmazenar.php");
include 'conexao.php';
header('Content-Type: text/html; charset=utf-8');

//Select que busca os eventos 
$conec = new conexao;
$conec->conecta();

$stid = $conec->query("SELECT A.USU_IDEVE,A.USU_TITAGE,A.USU_COR,
TO_CHAR(A.USU_DATAGE,'YYYY/MM/DD HH24:mi:ss') AS INICIAL ,
TO_CHAR(A.USU_DATAGEFIN,'YYYY/MM/DD HH24:mi:ss') AS FINAL,
A.USU_CODCLI,A.USU_OBSAGE,A.USU_CODLOC, 
B.USU_DESLOC,C.NOMCLI 
FROM USU_TAGELOC  A 
INNER JOIN USU_TCADLOC B ON A.USU_CODLOC = B.USU_CODLOC
INNER JOIN E085CLI C ON A.USU_CODCLI = C.CODCLI");

$eventos = [];
while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $id = strtoupper($row["USU_IDEVE"]);
    $title = strtoupper($row["USU_TITAGE"]);
    $color = strtoupper($row["USU_COR"]);
    $start = strtoupper($row["INICIAL"]);
    $end = strtoupper($row["FINAL"]);
    $responsible = strtoupper($row["USU_CODCLI"]);
    $description = strtoupper($row["USU_OBSAGE"]);
    $place = strtoupper($row["USU_CODLOC"]);
    $nameplace = strtoupper($row["USU_DESLOC"]);
    $nameresponsible = strtoupper($row["NOMCLI"]);

    //Converte a data para o formato que a API do calendario aceita 
    $datetime = date(DATE_ISO8601, strtotime($start));
    $datetimeend = date(DATE_ISO8601, strtotime($end));
    /*
    //Busca o nome do local 
    $stid = $conec->query("SELECT * FROM USU_TCADLOC WHERE USU_CODLOC='$place'");
    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $nDesLoc = strtoupper($row["USU_DESLOC"]);
    }

    
    //Busca o nome do reponsável 
    $conec->conecta();
    $stidcli = $conec->query("SELECT * FROM E085CLI 
    JOIN E085CNV ON E085CNV.CODCLI = E085CLI.CODCLI
    WHERE E085CNV.CODCNV=500 AND E085CLI.SITCLI != 'I' AND E085CLI.CODCLI= '$responsible' ");
    while ($row = oci_fetch_array($stidcli, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $nNomCli = strtoupper($row["NOMCLI"]);
    }
*/

    //Array que adiciona os eventos encontrados
    $eventos[] = [
        'id' =>  $id,
        'title' => $title,
        'color' => $color,
        'start' => $datetime,
        'end' => $datetimeend,
        'responsible' => $nameresponsible,
        'description' => $description,
        'place' => $nameplace
    ];
}

echo json_encode($eventos);
//$conec->desconecta();