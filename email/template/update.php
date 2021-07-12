<?php
 
include('../../class/EmailTemplate.php');
//start session
// session_start();
// if(!empty($_SESSION['status'])){
// // $configApp = include('../class/ConfigApp.php');

//     //get status from session
//     $status = $_SESSION['status'];
//     $msg = $_SESSION['msg'];
    
//     //remove status from session
//     unset($_SESSION['status']);
//     unset($_SESSION['msg']);
// }

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $id = $_POST['id'];
// 	$templateName = $_POST['templateName'];
// 	$subject = $_POST['subject'];
// 	$content = $_POST['content'];

//     // $id = $_GET['edit'];
//     // $update = true;

//     $emailTemplate = new EmailTemplate();
//     $emailTemplate->setId($id);
//     $emailTemplate->setType($templateName);
//     $emailTemplate->setTitle($subject);
//     $emailTemplate->setContent($content);
//     $emailTemplate->setCreated();
//     $emailTemplate->setModified();
//     $emailTemplate->setStatus();
//     $record = $emailTemplate->updateEmailTemplate();
// echo $records;
//     // if (count($record) > 0 ) {
//     //     // $n = mysqli_fetch_array($record);
//     //     $type = $record['type'];
//     //     $title = $record['title'];
//     //     $content = $record['content'];
//     //     echo $content;
//     // }
// }

$id = $_GET['id'];
echo $id;

$emailTemplate2 = new EmailTemplate();
$emailTemplate2->setId($id);
$res = $emailTemplate2->getEmailTemplateById();
?>

<html>
<head>
    <title>Create Email Template</title>    
    
</head>
<body>
   <form method="post" action="submit.php">
        <div class="form-group col-sm-10">
            <label for="templateName" class="form-label">Nombre template</label>
            <input type="text" class="form-control" value="<?=$res['type']; ?>" name="templateName" id="templateName" aria-describedby="templateNameHelp">
            <div id="templateNameHelp" class="form-text">Nombre ejemplo: miTemplate.</div>
        </div>
        <div class="form-group col-sm-10">
            <label for="subject" class="form-label">Asunto</label>
            <input type="text" class="form-control" value="<?=$res['title']; ?>" name="subject" id="subject" aria-describedby="subjectHelp">
            <div id="subjectHelp" class="form-text">Asunto de correo electrónico.</div>
        </div>        
        <div class="form-group col-sm-10">
            <label for="content" class="form-label">Contenido</label>
            <textarea class="form-control" name="content" id="content"></textarea>
            <div id="contentHelp" class="form-text">Ejemplo de variables: [USER_NAME] [USER_EMAIL] (Contenido del correo electrónico). </div>
        </div>     
        <div class="form-group">
	        <button class="btn btn btn-primary" type="submit" value='update' name="update" style="background: #556B2F;" >Actualizar</button>
        </div>   
        
    </form>
    </div>
</body>
</html>
