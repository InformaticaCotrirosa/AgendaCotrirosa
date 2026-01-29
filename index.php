<?php
require_once("./_functions/conecta.php");
require_once("./consultas/processaLista.php");
require_once("./_functions/montaCampos.php");
require_once("./save/cadastro.php");
require_once("./_functions/postArmazenar.php");
include 'conexao.php';


session_start();

$cUsuCad = unserialize($_SESSION['userCadastro']);
if ($cUsuCad === false) {
    echo '<script>window.location = "./login.php";</script>';
}

if (isset($_POST['BSair'])) {
    session_unset();
    echo '<script>window.location = "./login.php";</script>';
}

if (isset($_POST['BCancelar'])) {
    unset($_POST['BSalvar']);
    echo '<script>limparTodasTelas();</script>';
}

if (isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Agenda - Cotrirosa</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='_css/core/main.min.css' rel='stylesheet' />
    <link href='_css/daygrid/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="_css/personalizado.css">
    <link rel="stylesheet" href="_css/teste.css">
    <link rel="shortcut icon" href="http://192.168.0.167/rotinasweb/_imagens/calendaricon.ico" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src='js/core/main.min.js'></script>
    <script src='js/interaction/main.min.js'></script>
    <script src='js/daygrid/main.min.js'></script>
    <script src='js/core/locales/pt-br.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/personalizado.js"></script>

</head>

<body>
    <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Nav Bar @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->
    <header>
        <nav class="nav-bar">
            <div class="logo">
                <h1>
                    <img src="_images\logoCotrirosaPequena.png" alt="logo" />
                </h1>
            </div>
            <div class="nav-list">
                <ul>
                    <li class="nav-item"><a href="#" class="nav-link">Início</a></li>
                    <li class="nav-item"><a href="#" class="nav-link" data-toggle="modal"
                            data-target="#cadUsuarioModal">Cadastro</a></li>
                    <li class="nav-item"><a href="#" class="nav-link" data-toggle="modal"
                            data-target="#editLocListModal">Editar Local</a></li>
                    <li class="nav-item"><a href="#" class="nav-link" data-toggle="modal"
                            data-target="#sobreModal">Sobre</a></li>
                </ul>
            </div>


            <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Inicio Modal cadastro de Local @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->
            <div class="modal fade" id="cadUsuarioModal" tabindex="-1" aria-labelledby="cadUsuarioModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" style="font-weight: bold; color: #e27d34;"
                                id="cadUsuarioModalLabel">Cadastrar Local</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>

                        </div>
                        <div class="modal-body">
                            <span id="msgAlertaErro"></span>
                            <form class="row g-3" id="cad-usuario-form">
                                <span id="msgAlertErroCad"></span>


                                <div class="col-12">
                                    <label for="nomloc" class="form-label" style="font-weight: bold;">Nome do
                                        Local</label>
                                    <input type="text" name="nomloc" class="form-control" id="nomloc"
                                        placeholder="Nome do Local" required>
                                </div>

                                <div class="col-12">
                                    <label for="cor" class="form-label" style="font-weight: bold;">Cor</label>
                                    <input type="color" name="cor" class="form-control" id="cor" placeholder="cor"
                                        required>
                                </div>

                                <div class="col-12">
                                    <label for="sitloc" class="form-label" style="font-weight: bold;">Situação</label>
                                    <select name="sitloc" class="form-control" id="sitloc" required>
                                        <option value="A">Ativo</option>
                                        <option value="I">Inativo</option>
                                    </select>
                                </div>


                                <div class="col-12" style="margin: 15px;">
                                    <button type="submit" name="CadLoc" id="CadLoc" value="CadLoc"
                                        class="btn btn-success" style="margin-right: 12px;">Cadastrar</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                </div>
                            </form>

                            <script src="js/custom.js"></script>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fim Modal CadLoc ->


            <!- Inicio - Lista de Locais do DB -->
            <div class="modal fade" id="editLocListModal" tabindex="-1" aria-labelledby="editLocListModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" style="font-weight: bold; color: #e27d34;"
                                id="editLocListModalLabel">
                                Locais Cadastrados:</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <span id="msgAlertaErro"></span>
                            <form class="row g-3" id="cad-usuario-form" style="font-weight: bold; text-align: center;">
                                <div class="col-12">

                                    <?php
                                    $conec = new conexao;
                                    $conec->conecta();

                                    $stid = $conec->query("SELECT * FROM USU_TCADLOC");
                                    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                                        $nCodLoc = strtoupper($row["USU_CODLOC"]);
                                        $aDesLoc = strtoupper($row["USU_DESLOC"]);
                                        $nSitLoc = strtoupper($row["USU_SITLOC"]);
                                        $aNomCor = strtoupper($row["USU_COR"]);

                                        echo "<p style=background-color:$aNomCor;> $aDesLoc <br>";

                                        echo "<td>                              
                                                <a class='editarLocButton' href='edit_loc.php?USU_CODLOC=" . $row['USU_CODLOC'] . "'>
                                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil-fill' viewBox='0 0 16 16'>
                                                <path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z'/>
                                                </svg>
                                            </a>
                                        </td>";

                                        echo "<td>
                                                <a class='editarLocButton' href='proc_apagar_local.php?USU_CODLOC=" . $row['USU_CODLOC'] . "'>
                                                <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash' viewBox='0 0 16 16'>
                                                <path d='M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z'/>
                                                <path fill-rule='evenodd' d='M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z'/>
                                                </svg>
                                            </a>
                                        </td>";
                                    }
                                    $conec->desconecta();
                                    ?>
                                </div>

                                <div class="col-12" style="margin: 10px; text-align: center;">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Final Editar o Local Modal -->

            <!--Modal para EditLoc -->

            <body>
                <div class="modal fade" id="editLocModal" tabindex="-1" aria-labelledby="editLocModal"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" style="font-weight: bold; color: #e27d34;" id="editLocModal">
                                    Edite o Local</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>

                            </div>
                            <div class="modal-body">
                                <form class="row g-3" id="edit-usuario-form" method="POST" action="edit_loc.php">
                                    <div class="col-12">
                                        <label for="codloc" class="form-label" style="font-weight: bold;">Código do
                                            local</label>
                                        <input type="text" name="codloc" class="form-control" id="codloc"
                                            placeholder="Código de Local"
                                            value="<?php echo $row_events['USU_CODLOC']; ?>">
                                    </div>

                                    <div class="col-12">
                                        <label for="nomloc" class="form-label" style="font-weight: bold;">Nome do
                                            Local</label>
                                        <input type="text" name="nomloc" class="form-control" id="nomloc"
                                            placeholder="Nome do Local">
                                    </div>

                                    <div class="col-12">
                                        <label for="cor" class="form-label" style="font-weight: bold;">Cor</label>
                                        <input type="color" name="cor" class="form-control" id="cor" placeholder="cor">
                                    </div>

                                    <div class="col-12">
                                        <label for="sitloc" class="form-label"
                                            style="font-weight: bold;">Situação</label>
                                        <select name="sitloc" class="form-control" id="sitloc">
                                            <option value="A">Ativo</option>
                                            <option value="I">Inativo</option>
                                        </select>
                                    </div>


                                    <div class="botoesGerais">
                                        <button type="submit" name="CadLoc" id="CadLoc" value="CadLoc"
                                            class="btn btn-success" style="margin-right: 12px;">Atualizar Dados</button>
                                    </div>

                                </form>
                            </div>
                            <div class="botoesGerais">
                                <button type="button" class="btn btn-dismiss btn-danger"
                                    data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </body>
            <!-- Fim do Modal de EditLoc -->

            <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Inicio Modal de Texto (Sobre a Agenda) @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->

            <body>
                <div class="modal fade" id="sobreModal" tabindex="-1" aria-labelledby="sobreModal" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title" style="font-weight: bold; color: #e27d34; font-size: 20px;"
                                    id="sobreModal">
                                    Manual de como utilizar a Agenda:</h1>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>

                            </div>
                            <div class="modal-body">
                                <h2 class="modal-title" style="font-weight: bold; font-size: 17px;">
                                    -> Após entrar na sua conta do sistema através da agenda, o usuário já pode adicionar
                                    um evento!
                                </h2><br>
                                <h2 class="modal-subtitle" style="font-weight: bold; font-size: 18px; color: #28a745;"
                                    st>
                                    Como funciona:
                                </h2><br>

                                <h3 style="font-size: 18px;">
                                    1. O usuário deve verificar as datas desejadas, locais e os eventos existentes; <br>
                                    2. Caso o horário esteja disponível;

                                    <h2 style="font-size: 16px;">
                                        + Deve clicar na data; <br>
                                        + Inserir as informações, sobre a sua reserva de local e data, organizadas por:
                                        <br>
                                        Local; Responsável; Título; Descrição; Início do evento; Final do evento; <br>
                                    </h2>

                                    <h3 class="modal-subtitle"
                                        style="font-weight: bold; font-size: 16px; color: #ADFF2F;"> Imagem de
                                        demonstração:</h3><br>

                                    <img src="_images\demoResize.png" alt="logo" />

                                    <h3 style="font-size: 18px;"> 3. Após preencher deve clicar em cadastrar, se estiver
                                        tudo certo vai
                                        aparecer uma mensagem e o evento será cadastrado no calendário;
                                        <h2 style="font-size: 16px;">
                                            + Ao clicar em cima do evento que esta inserido, vai aparecer todos os detalhes
                                            sobre ele;<br>
                                        </h2>
                                    </h3><br>
                                </h3>


                                <h2 class="modal-subtitle" style="font-weight: bold; font-size: 18px; color: #28a745;"
                                    st>
                                    Como editar um evento cadastrado:
                                </h2><br>

                                <h3 style="font-size: 18px;">
                                    1. O usuário deve clicar sobre o evento que deseja para abrir os detalhes;
                                    <br>
                                    2. Caso houver alguma informação para alterar, pode
                                    clicar sobre o botão editar;

                                    <h2 style="font-size: 16px;">
                                        + Inserir todas as informações e salvar novamente: <br>
                                    </h2>

                                    <h3 class="modal-subtitle"
                                        style="font-weight: bold; font-size: 16px; color: #ADFF2F;"> Imagem de
                                        demonstração:</h3><br>
                                    <img src="_images\demoEdit1Resize.png" alt="logo" />

                                   <br>
                                   <h3 class="modal-subtitle"
                                        style="font-weight: bold; font-size: 16px; color: #ADFF2F;"> Imagem de
                                        demonstração:</h3><br>
                                    <img src="_images\detalhesEventCad.png" alt="logo" />
                            </div>

                            <div class="botoesGerais">
                                <button type="button" class="btn btn-dismiss btn-danger"
                                    data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </body>
            <!-- FIM MODAL SOBRE -->

            <!-- BOTÃO DE SAIR NAVBAR -->
            <div class="login-button">
                <button type="button" class="" data-toggle="modal" data-target="">
                    <?php echo '<a href="logout.php?token=' . md5(session_id()) . '">Sair</a>'; ?>
                </button>
            </div>

            <div class="mobile-menu-icon">
                <button onclick="menuShow()"><img class="icon" src="assets/img/menu_white_36dp.svg" alt=""></button>
            </div>
        </nav>
        <div class="mobile-menu">
            <ul>
                <li class="nav-item"><a href="#" class="nav-link">Início</a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-toggle="modal"
                        data-target="#cadUsuarioModal">Cadastro</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Editar Locais</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Sobre</a></li>
            </ul>

            <div class="login-button">
                <button><a href="#">Sair</a></button>
            </div>
        </div>
    </header>
    <script src="assets/js/script.js"></script>


    <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Sumário @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->
    <div id="menu">
        <div class="row">
            <div class="card green">

                <h2>Sumário</h2>
                <p>Descrição das cores:</p>
                <?php
                $conec = new conexao;
                $conec->conecta();

                $stid = $conec->query("SELECT * FROM USU_TCADLOC WHERE USU_SITLOC!='I'");
                while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    $nCodLoc = strtoupper($row["USU_CODLOC"]);
                    $aDesLoc = strtoupper($row["USU_DESLOC"]);
                    $nSitLoc = strtoupper($row["USU_SITLOC"]);
                    $aNomCor = strtoupper($row["USU_COR"]);

                    echo "<p style=background-color:$aNomCor; font-weight: italic; > $aDesLoc <br>";

                    //echo " <option value=\"$nCodLoc\">$aNomCor</option>";
                
                }
                $conec->desconecta();
                ?>
            </div>
        </div>
    </div>


    <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Detalhes do evento @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->
    <div id='calendar'></div>
    <div class="modal fade" id="visualizar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detalhes do Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="visevent">
                        <dl class="row">
                            <dt class="col-sm-3">ID do evento</dt>
                            <dd class="col-sm-9" id="id"></dd>

                            <dt class="col-sm-3">Responsável</dt>
                            <dd class="col-sm-9" id="responsible"></dd>

                            <dt class="col-sm-3">Título do evento</dt>
                            <dd class="col-sm-9" id="title"></dd>

                            <dt class="col-sm-3">Descrição</dt>
                            <dd class="col-sm-9" id="description"></dd>

                            <dt class="col-sm-3">Local</dt>
                            <dd class="col-sm-9" id="place"></dd>

                            <dt class="col-sm-3">Início do evento</dt>
                            <dd class="col-sm-9" id="start"></dd>

                            <dt class="col-sm-3">Fim do evento</dt>
                            <dd class="col-sm-9" id="end"></dd>
                        </dl>
                        <div class="botoesGerais">
                            <button class="btn btn-warning btn-canc-vis" style="margin-right: 15px;">Editar</button>
                            <a href="" id="apagar_evento" class="btn btn-danger">Apagar</a>
                        </div>
                    </div>

                    <!--@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Editar Evento @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->
                    <div class="formedit">
                        <span id="msg-edit"></span>
                        <form id="editevent" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id">

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Local</label>
                                <div class="col-sm-10">
                                    <select name="local" class="form-control" id="local" required>

                                        <!--é preciso fazer um select pra pegar o local daquele id  e depois filtrar no select local do oracle -->
                                        <option value="">Selecione</option>
                                        <?php
                                        $conec = new conexao;
                                        $conec->conecta();

                                        $variavelphp = "<script>document.write(variaveljs)</script>";

                                        $stid = $conec->query("SELECT * FROM USU_TCADLOC WHERE USU_SITLOC!='I'");
                                        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                                            $nCodLoc = strtoupper($row["USU_CODLOC"]);
                                            $aDesLoc = strtoupper($row["USU_DESLOC"]);
                                            $nSitLoc = strtoupper($row["USU_SITLOC"]);
                                            echo " <option value=\"$nCodLoc\">$aDesLoc</option>";
                                        }
                                        $conec->desconecta();
                                        ?>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Responsável</label>
                                <div class="col-sm-10">
                                    <select name="responsavel" class="form-control" id="responsavel" required>
                                        <option value="">Selecione para encontar</option>
                                        <?php
                                        $conec = new conexao;
                                        $conec->conecta();
                                        $stid = $conec->query("SELECT * FROM E085CLI WHERE TIPCLI='F' AND SITCLI='A' AND TIPMER='I' AND CODCNV=1 OR CODCNV=500  ORDER BY NOMCLI ASC");
                                        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                                            $nCodCli = strtoupper($row["CODCLI"]);
                                            $aNomCli = strtoupper($row["NOMCLI"]);
                                            $aSitCli = strtoupper($row["SITCLI"]);
                                            echo " <option value=\"$nCodCli\">$aNomCli</option>";
                                        }
                                        $conec->desconecta();
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Título</label>
                                <div class="col-sm-10">
                                    <input type="text" name="title" class="form-control" id="title"
                                        placeholder="Título do evento">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Início do evento</label>
                                <div class="col-sm-10">
                                    <input type="datetime-local" name="start" class="form-control" id="start"
                                        min="2023-01-01T00:00" onkeypress="DataHora(event, this)" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Final do evento</label>
                                <div class="col-sm-10">
                                    <input type="datetime-local" name="end" class="form-control" id="end"
                                        onkeypress="DataHora(event, this)" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="botoesGerais" style="margin: 10px;">
                                    <button type="submit" name="CadEvent" id="CadEvent" value="CadEvent"
                                        class="btn btn-success" style="margin-right: 12px;">Salvar</button>
                                    <button type="button" class="btn btn-danger btn-canc-edit">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ Cadastrar Evento @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@-->
    <div class="modal fade" id="cadastrar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastrar Evento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="msg-cad"></span>
                    <form id="addevent" method="POST" enctype="multipart/form-data">

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Local</label>
                            <div class="col-sm-10">
                                <select name="local" class="form-control" id="local" required>
                                    <option value="">Selecione</option>

                                    <?php
                                    $conec = new conexao;
                                    $conec->conecta();

                                    $stid = $conec->query("SELECT * FROM USU_TCADLOC WHERE USU_SITLOC!='I'");
                                    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                                        $nCodLoc = strtoupper($row["USU_CODLOC"]);
                                        $aDesLoc = strtoupper($row["USU_DESLOC"]);
                                        $nSitLoc = strtoupper($row["USU_SITLOC"]);
                                        echo " <option value=\"$nCodLoc\">$aDesLoc</option>";
                                    }
                                    $conec->desconecta();
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Responsável</label>
                            <div class="col-sm-10">
                                <select name="responsavel" class="form-control" id="responsavel" required>
                                    <option value="">Selecione</option>
                                    <?php
                                    $conec = new conexao;
                                    $conec->conecta();
                                    $stid = $conec->query("SELECT * FROM E085CLI WHERE TIPCLI='F' AND SITCLI='A' AND TIPMER='I' AND CODCNV=1 OR CODCNV=500  ORDER BY NOMCLI ASC ");
                                    while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                                        $nCodCli = strtoupper($row["CODCLI"]);
                                        $aNomCli = strtoupper($row["NOMCLI"]);
                                        $aSitCli = strtoupper($row["SITCLI"]);
                                        echo " <option value=\"$nCodCli\">$aNomCli</option>";
                                    }
                                    $conec->desconecta();
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Título</label>
                            <div class="col-sm-10">
                                <input type="text" name="titulo" class="form-control" id="titulo"
                                    placeholder="Título do evento" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Descrição</label>
                            <div class="col-sm-10">
                                <textarea type="text" name="descricao" class="form-control" id="descricao"
                                    placeholder="Descrição"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Início do evento</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="start" class="form-control" id="start"
                                    min="2023-01-01T00:00" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Final do evento</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="end" class="form-control" id="end" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <button type="submit" name="CadEvent" id="CadEvent" value="CadEvent" class="btn btn-success"
                                style="margin-right: 15px;">Cadastrar</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<html>