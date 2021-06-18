<?php
//start session
session_start();
if(!empty($_SESSION['status'])){
$configApp = include('../class/ConfigApp.php');

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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- Add TinyMCE editor to textarea  tinyMCEiD -->
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
    <div class="container">
    <form method="post" action="templateSubmit.php">
        <div class="form-group col-sm-10">

            <label for="templateName" class="form-label">Nombre template</label>

            <input type="text" class="form-control" name="templateName" id="templateName" aria-describedby="templateNameHelp">
            <div id="templateNameHelp" class="form-text">Nombre ejemplo: miTemplate.</div>
        </div>
        <div class="form-group col-sm-10">
            <label for="subject" class="form-label">Asunto</label>
            <input type="text" class="form-control" name="subject" id="subject" aria-describedby="subjectHelp">
            <div id="subjectHelp" class="form-text">Asunto de correo electrónico.</div>
        </div>        
        <div class="form-group col-sm-10">
            <label for="content" class="form-label">Contenido</label>
            <textarea class="form-control" name="content" id="content"></textarea>
            <div id="contentHelp" class="form-text">Ejemplo de variables: [USER_NAME] [USER_EMAIL] (Contenido del correo electrónico). </div>
        </div>     
        <div class="form-group">
            <input type="submit" class="btn btn btn-primary" name="submit" Value="Crear Template">
        </div>   
        
    </form>
    </div>
</body>
</html>