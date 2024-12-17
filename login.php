<?php
require_once("../_functions/conecta.php");

session_start();

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
} else {
    echo '<script>window.location = "./index.php";</script>';
}
?>


<html>

<head>
    <!--<script rel="text/javascript" src="./js/loading.js"></script>-->
    <link rel='stylesheet' href='./_css/login.css' />
    <link rel='stylesheet' href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.1/css/fontawesome.min.css" />

</head>

<body>
    <div class="container">
        <form id="FormIndex" autocomplete="off" action=" <?php echo $_SERVER['REQUEST_URI'] ?>" method="post" onsubmit="openLoading()">
            <div class="login-box">
                <h1>Login</h1>

                <div class="textbox">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516  
                          10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z" />
                    </svg>
                    <input placeholder="Usuário" type="text" name="login" id="idLogin">
                </div>
                <div class="textbox">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                    </svg>
                    <input placeholder="Senha" type="password" name="password" id="idSenha">
                </div>
                <div class="btn">
                    <input id="btnEntrar" name="btnEntrar" type="submit" value="Entrar">
                </div>
            </div>
        </form>
    </div>
</body>

</html>

<?php echo '<script>closeLoading();</script>'; ?>