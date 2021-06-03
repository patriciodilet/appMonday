<?php

//start session
session_start();
if(!empty($_SESSION['status'])){
    //get status from session
    $status = $_SESSION['status'];
    $msg = $_SESSION['msg'];
    
    //remove status from session
    unset($_SESSION['status']);
    unset($_SESSION['msg']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Email Template</title>    
    <!-- Add TinyMCE editor to textarea -->
    <script src="https://cdn.tiny.cloud/1/huc9qil6fqwtc81pirva1f9b66brfatrsjmwvekhk82bvukv/tinymce/5/tinymce.min.js"></script>
    <script>tinymce.init({ selector:'textarea' });</script>
</head>
<body>
    <?php
    if(!empty($status) && $status == 'succ'){
        echo '<p style="color: green;">'.$msg.'</p>';
    }elseif(!empty($status) && $status == 'err'){
        echo '<p style="color: red;">'.$msg.'</p>';
    }
    ?>
    <form method="post" action="templateSubmit.php">
        <p>
            Nombre template: <input type="text" name="templateName" />
        </p>
        <p>
            Asunto: <input type="text" name="subject" />
        </p> 
        <p>
            Contenido: <textarea name="content"></textarea>
        </p>
        <p>Ejemplo de variables: [USER_NAME] [USER_EMAIL]</p>
        <p>
            <input type="submit" name="submit" value="Crear Template">
        </p>
    </form>
</body>
</html>