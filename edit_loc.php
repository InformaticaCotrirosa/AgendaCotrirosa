<?php
session_start();
require_once("./_functions/conecta.php");
require_once("./consultas/processaLista.php");
require_once("./_functions/montaCampos.php");
require_once("./save/cadastro.php");
require_once("./_functions/postArmazenar.php");
include 'conexao.php';

$nCodLoc = filter_input(INPUT_GET, 'USU_CODLOC', FILTER_SANITIZE_NUMBER_INT);

//@@@@@@@@@@@@@@@ ORACLE @@@@@@@
$conec = new conexao;
$conec->conecta();

$stid = $conec->query("SELECT * FROM USU_TCADLOC WHERE USU_CODLOC = '$nCodLoc'");
$row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Agenda de Salas - Local</title>
    <style>
        body {
            font-family: Arial, Arial, Helvetica, sans-serif;
        }

        .box {
            position: inherit;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0);
            padding: 15px;
            border-radius: 15px;
        }

        fieldset {
            border: 3px solid dodgerblue;
        }



        .inputBoxLoc {
            position: relative;
            margin-top: 10px;

        }

        .inputUser {
            background: none;
            border: none;
            border-bottom: 1px solid white;
            outline: none;
            color: white;
            font-size: 15px;
            width: 100%;
            letter-spacing: 2px;
        }
    </style>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='_css/core/main.min.css' rel='stylesheet' />
    <link href='_css/daygrid/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="_css/form.css">
    <link rel="shortcut icon" href="http://192.168.0.167/rotinasweb/_imagens/calendaricon.ico" />
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src='js/core/main.min.js'></script>
    <script src='js/interaction/main.min.js'></script>
    <script src='js/daygrid/main.min.js'></script>
    <script src='js/core/locales/pt-br.js'></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

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
                    <li class="nav-item"><a href="index.php?" class="nav-link" data-target="login.php">Início</a></li>

                </ul>
            </div>

            <div class="login-button">
                <button type="button" class="" data-toggle="modal" data-target="">
                    <?php echo '<a href="logout.php?token=' . md5(session_id()) . '">Sair</a>'; ?>
                </button>
            </div>
    </header>


    <body>
        <div id='form'>
            <form method="POST" action="proc_edit_usuario.php">
                <fieldset>
                    <legend><b>Edite o local</b></legend>
                    <br>

                    <?php
                    if (isset($_SESSION['msg'])) {
                        echo $_SESSION['msg'];
                        unset($_SESSION['msg']);
                    }
                    ?>

                    <div class='inputBoxLoc'>
                        <input type="hidden" name="USU_CODLOC" value="<?php echo $row['USU_CODLOC']; ?>">
                        <label for="nomloc" class='labelInput'>Nome Local:</label>

                        <div class='input'>
                            <input type="text" name="USU_DESLOC" placeholder="Digite o nome do local" value="<?php echo $row['USU_DESLOC']; ?>">
                        </div>

                    </div>


                    <div class='inputBoxLoc'>
                        <label for='sitloc' class='labelInput'>Situação Local: </label>
                        <input type="text" name="USU_SITLOC" placeholder="<?php echo $row_events['USU_SITLOC']; ?>" value="<?php echo $row['USU_SITLOC']; ?>">
                    </div>


                    <div class='inputBoxLoc'>
                        <label for='corloc' class='labelInput'>Cor do Local: </label>
                        <input type="color" name="USU_COR" placeholder="" value="<?php echo $row['USU_COR']; ?>">
                    </div>


                    <div class="botoesGerais">
                        <button type="submit" value="Editar" class="btn btn-warning btn-canc-vis" data-dismiss="modal">Editar</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </body>


</body>

</html>