<?php

session_start();

require_once("./_functions/conecta.php");
include_once './conexao.php';

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

$registration = $cUsuCad->aNomUsu();
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

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

//Fazer um select trazendo o usuario que criou o evento 
$stiduser = $conec->query("SELECT * FROM USU_TAGELOC WHERE USU_IDEVE = '$id'");
while ($row = oci_fetch_array($stiduser, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $user = strtoupper($row["USU_CODUSU"]);
}

$user = intval($user);
$codusu = intval($codusu);

if ($user == $codusu) {

    $conec = new conexao;
    $conec->conecta();

    $sql = "DELETE FROM USU_TAGELOC WHERE USU_IDEVE='$id' AND USU_CODUSU ='$codusu'";

    if ($sql !== "") {
        $stid = $conec->query($sql);

        //ALERT SE O EVENTO FOI APAGADO
        $linhafec = oci_num_rows($stid);
        if ($linhafec > 0) {
            $_SESSION['msg'] = '<div class="alert alert-success" role="alert">O evento foi apagado com sucesso!</div>';
            header("Location: index.php");           
        } else {
            $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: O evento não foi apagado!</div>';
            header("Location: index.php");
        }
    }

} else {

    // ALERT USUARIO QUE ESTÁ TENTANDO DELETAR É DIFERENTE DO LOGADO 
    $_SESSION['msg'] = '<div class="alert alert-danger" role="alert">Erro: Você não pode apagar um evento, que não pertence ao seu usuário!</div>';
    header("Location: index.php");

}
$conec->desconecta();