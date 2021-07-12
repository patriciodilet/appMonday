<?php
 
include('../class/EmailTemplate.php');
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

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;

    $emailTemplate = new EmailTemplate();
    $emailTemplate->setId($id);
    $record = $emailTemplate->getEmailTemplateById();

    if (count($record) > 0 ) {
        // $n = mysqli_fetch_array($record);
        $type = $record['type'];
        $title = $record['title'];
        $content = $record['content'];
        echo $content;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Email Template</title>    
    <link rel="stylesheet" type="text/css" href="style.css">
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


    <table>
	<thead>
		<tr>
			<th>Template</th>
			<th>Asunto</th>
			<th colspan="2">Action</th>
		</tr>
	</thead>
	
	<?php 
        $emailTemplate = new EmailTemplate();
		$results = $emailTemplate->getEmailTemplateData();
        foreach ($results as $res):
    ?>
		<tr>
			<td><?=$res['type']?></td>
			<td><?=$res['title']; ?></td>
			<td>
				<a href="add-template.php?edit=<?=$res['id']; ?>" class="edit_btn" >Edit</a>
			</td>
			<td>
				<a href="server.php?del=<?=$res['id']; ?>" class="del_btn">Delete</a>
			</td>
		</tr>
        <?php endforeach; ?>
    </table>

    <form method="post" action="templateSubmit.php">
        <div class="form-group col-sm-10">
            <label for="templateName" class="form-label">Nombre template</label>
            <input type="text" class="form-control" value="<?=$type; ?>" name="templateName" id="templateName" aria-describedby="templateNameHelp">
            <div id="templateNameHelp" class="form-text">Nombre ejemplo: miTemplate.</div>
        </div>
        <div class="form-group col-sm-10">
            <label for="subject" class="form-label">Asunto</label>
            <input type="text" class="form-control" value="<?=$title; ?>" name="subject" id="subject" aria-describedby="subjectHelp">
            <div id="subjectHelp" class="form-text">Asunto de correo electrónico.</div>
        </div>        
        <div class="form-group col-sm-10">
            <label for="content" class="form-label">Contenido</label>
            <textarea class="form-control" name="content" id="content"></textarea>
            <div id="contentHelp" class="form-text">Ejemplo de variables: [USER_NAME] [USER_EMAIL] (Contenido del correo electrónico). </div>
        </div>     
        <div class="form-group">

        <?php 
        if ($update == true): ?>
	        <button class="btn btn btn-primary" type="submit" value='update' name="update" style="background: #556B2F;" >Actualizar</button>
        <?php else: ?>
	        <button class="btn btn btn-primary" type="submit" name="save" >Guardar</button>
        <?php endif ?>

        </div>   
        
    </form>
    </div>
</body>
</html>