<?php
session_start();
require_once("./_functions/conecta.php");
require_once("./consultas/processaLista.php");
require_once("./_functions/montaCampos.php");
require_once("./save/cadastro.php");
require_once("./_functions/postArmazenar.php");
include_once "conexao.php";

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
$registration = $cUsuCad->aNomUsu();
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (empty($dados['nomloc'])) {
    $retorna = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: nomloc!</div>"];
} elseif (empty($dados['cor'])) {
    $retorna = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: cor!</div>"];
} elseif (empty($dados['sitloc'])) {
    $retorna = ['erro' => true, 'msg' => "<div class='alert alert-danger' role='alert'>Erro: sitloc!</div>"];
} else {

    $conec = new conexao;
    $conec->conecta();


    $stidcodusu = $conec->query("SELECT E099USU.INTNET,R910USU.NOMCOM,R910USU.DATCRE,R034FUN.DATADM, 
    E099USU.CODUSU                                               
    FROM E099USU                                                      
    LEFT JOIN R910ENT ON R910ENT.CODENT = E099USU.CODUSU AND     
                     R910ENT.NOMEXB = E099USU.NOMUSU         
    LEFT JOIN R910USU ON R910USU.CODENT = R910ENT.CODENT         
    LEFT JOIN RH_PRD.R034FUN R034FUN ON                          
              upper(R034FUN.NOMFUN) = upper(R910USU.NOMCOM)     
    WHERE upper(R910USU.NOMCOM) = upper('$registration')");
    while ($row = oci_fetch_array($stidcodusu, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $codusu = strtoupper($row["CODUSU"]);
    }

    $stid = $conec->query("SELECT UPPER(R910ENT.NomEnt) NOMENT FROM R910MGP,R910ENT
										WHERE R910MGP.CODGRP = R910ENT.CODENT and R910ENT.NomEnt= 'SENIOR' and R910MGP.CODMBR='$codusu' ");
    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
        $aPerUsu = strtoupper($row["NOMENT"]);
    }

    if (is_null($aPerUsu)) {
        $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: Apenas permisão SENIOR consegue cadastrar!</div>';
        header("Location: index.php");
    } else {

        $codloc = $dados['codloc'];
        $desloc = $dados['nomloc'];
        $sitloc = $dados['sitloc'];
        $cor = $dados['cor'];

        $sql = " INSERT INTO USU_TCADLOC (USU_CODLOC, USU_DESLOC, USU_SITLOC, USU_COR) VALUES ((SELECT MAX (USU_CODLOC) +1 FROM USU_TCADLOC),'$desloc','$sitloc','$cor')";

        if ($sql !== "") {
            $stid = $conec->query($sql);

            //AQUI FAZER UM ALERT CASO TENHA CADASTRADO O LOCAL CORRETAMENTE
            $linhafec = oci_num_rows($stid);
            if ($linhafec > 0) {
                $_SESSION['msg'] = '<div class="alert alert-success" role="alert">O Local foi Cadastrado com sucesso!</div>';
                header("Location: index.php");
            } else {
                $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: O local não foi cadastrado!</div>';
                header("Location: index.php");
            }
        }
    }
}
echo json_encode($stid);
$conec->desconecta();