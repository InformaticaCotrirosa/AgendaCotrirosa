<?php 
 session_start();
 $token = md5(session_id());
 if(isset($_GET['token']) && $_GET['token'] === $token) {
    // limpe tudo que for necessário na saída.
    // Eu geralmente não destruo a seção, mas invalido os dados da mesma
    // para evitar algum "necromancer" recuperar dados. Mas simplifiquemos:
        header("Location:login.php");
    session_destroy();
    exit();
 } else {
    echo '<a href="logout.php?token='.$token.'>Confirmar logout</a>';
 }
?>