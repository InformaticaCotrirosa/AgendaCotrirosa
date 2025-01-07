<?php
session_start();

require_once("./_functions/conecta.php");
require_once("./consultas/processaLista.php");
require_once("./_functions/montaCampos.php");
require_once("./save/cadastro.php");
require_once("./_functions/postArmazenar.php");
include 'conexao.php';

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

//Traz os dados do formulario 
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$codloc = $dados['local'];
$id = $dados['id'];
$titage = $dados['title'];
$codcli = $dados['responsavel'];
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

//Select que busca a cor conforme a seleção do local
$stidcor = $conec->query("SELECT USU_COR FROM USU_TCADLOC WHERE USU_CODLOC = $codloc");
while ($row = oci_fetch_array($stidcor, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $cor = strtoupper($row["USU_COR"]);
}


//Converter a data e hora do formato brasileiro para o formato do Banco de Dados
$data_start = str_replace('/', '-', $dados['start']);
$data_start_conv = date("Y/m/d H:i:s", strtotime($data_start));

$data_end = str_replace('/', '-', $dados['end']);
$data_end_conv = date("Y/m/d H:i:s", strtotime($data_end));


//Fazer um select trazendo o usuario que criou o evento 
$stiduser = $conec->query("SELECT * FROM USU_TAGELOC WHERE USU_IDEVE = '$id'");
while ($row = oci_fetch_array($stiduser, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $user = strtoupper($row["USU_CODUSU"]);
}

$user = intval($user);
$codusu = intval($codusu);
$conec->desconecta();

//VERIFICA SE O USUARIO QUE CROIU O EVENTO É O MESMO QUE ESTÁ EDITANDO 
if ($user == $codusu) {

    $datage = $data_start_conv;
    $datagefim = $data_end_conv;

    $conec = new conexao;
    $conec->conecta();

    //verifica se existe algum evento com o mesmo horário
    $stidevent = $conec->query("SELECT count(*) AS DADOS FROM USU_TAGELOC WHERE USU_CODLOC = $codloc AND
    (
         (USU_DATAGE > TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss') AND USU_DATAGE < TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss'))
    OR	(USU_DATAGEFIN > TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss') AND USU_DATAGEFIN < TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss'))
    OR 	(USU_DATAGE < TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss') AND USU_DATAGEFIN > TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss'))
    )");

    while ($row = oci_fetch_array($stidevent, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $count = strtoupper($row["DADOS"]);
    }

    //verifica se a variavel de retorno do select tem alguma informação 
    if (empty($count)) {
        $codloc = $dados['local'];
        $id = $dados['id'];
        $titage = $dados['title'];
        $codcli = $dados['responsavel'];

        $sql = "UPDATE USU_TAGELOC
        SET USU_TITAGE='$titage',
        USU_COR='$cor',
        USU_CODLOC='$codloc',
        USU_CODCLI='$codcli',
        USU_DATAGE=TO_DATE('$datage', 'yyyy/mm/dd hh24:mi:ss'),
        USU_DATAGEFIN=TO_DATE('$datagefim', 'yyyy/mm/dd hh24:mi:ss') WHERE USU_IDEVE='$id'";

        if ($sql !== "") {
            $stid = $conec->query($sql);

            // ALERT DE UPDATE DO EVENTO
            $linhafec = oci_num_rows($stid);
            if ($linhafec > 0) {
                $retorna = ['sit' => true, 'msg' => '<div class="alert alert-success" role="alert">Evento editado com sucesso!</div>'];
                $_SESSION['msg'] = '<div class="alert alert-success" role="alert">Evento editado com sucesso!</div>';
            } else {
                $retorna = ['sit' => false, 'msg' => '<div class="alert alert-danger" role="alert">Erro: Evento não foi editado!</div>'];
            }
        }
    } else {

        //AQUI FAZER UM ALERT EVENTO JA CADASTRADO PARA ESSE HORÁRIO
        $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: Já existe um evento cadastrado para esse horário!</div>';
        header("Location: index.php");

    }
} else {

    //AQUI FAZER UM ALERT USUARIO QUE ESTÁ TENTANDO EDITAR É DIFERENTE DO LOGADO 
    $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: Você está tentando editar um evento que não é seu!</div>';
    header("Location: index.php");

}

header('Content-Type: application/json');
echo json_encode($retorna);
$conec->desconecta();
