<?php
session_start();

require_once("./_functions/conecta.php");
require_once("./consultas/processaLista.php");
require_once("./_functions/montaCampos.php");
require_once("./save/cadastro.php");
require_once("./_functions/postArmazenar.php");
include 'conexao.php';


//BUSCA O USUARIO DA SESSÃO
$cUsuCad = unserialize($_SESSION['userCadastro']);
if ($cUsuCad === false) {
    if (isset($_POST['btnEntrar']) && $_POST['login'] != "" && $_POST['password'] != "") {

        $cUsuCad = new user;
        $cUsuCad->login($_POST['login'], $_POST['password']);

        if ($cUsuCad->aNomUsu() != 'undefined' && $cUsuCad->aNomUsu() != null && $cUsuCad->aNomUsu() != "" && $cUsuCad->aNomUsu() != " ") {
            $_SESSION['userCadastro'] = serialize($cUsuCad);
            echo '<script>window.location = "./index.php";</script>';
        }
    }
}

//GET DOS DADOS FORMULARIO
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$codloc = $dados['local'];
$registration = $cUsuCad->aNomUsu();

//Select para buscar o cod do usuario atraves do nome completo passado como parametro 
$conec = new conexao;
$conec->conecta();

$stid = $conec->query("SELECT E099USU.INTNET,R910USU.NOMCOM,R910USU.DATCRE,R034FUN.DATADM, 
E099USU.CODUSU                                               
FROM E099USU                                                      
LEFT JOIN R910ENT ON R910ENT.CODENT = E099USU.CODUSU AND     
                     R910ENT.NOMEXB = E099USU.NOMUSU         
LEFT JOIN R910USU ON R910USU.CODENT = R910ENT.CODENT         
LEFT JOIN RH_PRD.R034FUN R034FUN ON                          
              upper(R034FUN.NOMFUN) = upper(R910USU.NOMCOM)  
WHERE upper(R910USU.NOMCOM) = upper('$registration')");
while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $codusu = strtoupper($row["CODUSU"]);
}

// VALIDAÇÃO DE PERMISSÃO PARA SALA 7 
if ($codloc == 7 && $codusu != 1095 && $codusu != 668) {
    echo json_encode([
        "sit" => false,
        "msg" => "Erro: Temporariamente  (nas férias da Eduarda), solicitar a inclusão no ramal 339."
    ]);
    exit;
}


//Select que busca a cor conforme a seleção do local
$conec = new conexao;
$conec->conecta();

$stid = $conec->query("SELECT USU_COR FROM USU_TCADLOC WHERE USU_CODLOC = $codloc");
while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $cor = strtoupper($row["USU_COR"]);
}

//Converter a data e hora do formato brasileiro para o formato do Banco de Dados
$data_start = str_replace('/', '-', $dados['start']);
$data_start_conv = date("Y/m/d H:i:s", strtotime($data_start));

$data_end = str_replace('/', '-', $dados['end']);
$data_end_conv = date("Y/m/d H:i:s", strtotime($data_end));

$datage = $data_start_conv;
$datagefim = $data_end_conv;

// Verifica se a data de término é menor ou igual que a data de início e se a hora é a mesma
if (strtotime($data_end_conv) <= strtotime($data_start_conv)) {
    echo json_encode(array("sit" => false, "msg" => "A data de término não pode ser menor que a data de início."));
    exit;
}

//@@@@@@@@@@@@@@@@  BUSCA INFORMAÇÕES DE TODOS OS EVENTOS @@@@@@@@@@@@@@@@@@
//verifica se existe algum evento com o mesmo horário
$stidevent = $conec->query("SELECT count(*) AS DADOS FROM USU_TAGELOC WHERE USU_CODLOC = $codloc AND
(
         (USU_DATAGE >= TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss') AND USU_DATAGE < TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss'))
    OR	(USU_DATAGEFIN >= TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss') AND USU_DATAGEFIN < TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss'))
    OR 	(USU_DATAGE <= TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss') AND USU_DATAGEFIN >= TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss'))
    OR  (USU_DATAGE >= TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss') AND USU_DATAGEFIN < TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss'))
)
");

while ($row = oci_fetch_array($stidevent, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $count = strtoupper($row["DADOS"]);
}

//verifica se a variavel de retorno do select tem alguma informação 
if (empty($count)) {

    $ideve = 0; //Para incrementar o ID do evento é necessário que tenha pelo menos 1 evento cadastrado para que ele possa continuar a contagem!! 
    $codloc = $dados['local'];
    $titage = $dados['titulo'];
    $datage = $data_start_conv;
    $datagefim = $data_end_conv;
    $codcli = $dados['responsavel'];
    $obsage = $dados['descricao'];
    $cor = $cor;
    $codusu = $codusu;
    $horini = 0;
    $horfim = 0;

    $sql = "INSERT INTO USU_TAGELOC (USU_IDEVE,USU_CODLOC,USU_TITAGE,USU_DATAGE,USU_DATAGEFIN,USU_CODCLI,USU_OBSAGE,USU_COR, USU_CODUSU,USU_HORINI,USU_HORFIM)
    VALUES ((SELECT MAX (USU_IDEVE) +1 FROM USU_TAGELOC),'$codloc','$titage',TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss'), TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss'),
    '$codcli','$obsage','$cor','$codusu',0,0)";

    if ($sql !== "") {
        $stid = $conec->query($sql);

        // ALERT PARA VERIFICAR SE HOUVE ALGUMA ALTERAÇÃO NO BANCO ORACLE COM INSERT, FAZER MAIS UM IF PARA VERIFICAR
        $linhafec = oci_num_rows($stid);
        if ($linhafec > 0) {
            $retorna = ['sit' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>'];
            $_SESSION['msg'] = '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>';
        } else {
            $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Evento não foi cadastrado com sucesso!</div>'];
        }

    } else {

        //AQUI FAZER UM ALERT CASO A QUERY NÃO ESTIVER COMPLETA
        $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: Falta de informações na Query!</div>';
        header("Location: index.php");

    }
    header('Content-Type: application/json');
    echo json_encode($retorna);
} else {

    //AQUI FAZER UM ALERT DE EVENTO NO MESMO HORÁRIO
    $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: Já existe um evento cadastrado para esse horário!</div>';
    header("Location: index.php");
}

$conec->desconecta();