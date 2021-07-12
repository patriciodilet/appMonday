<?php
include('../../class/EmailTemplate.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Email Template</title>    
    <link rel="stylesheet" type="text/css" href="../style.css">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> -->
    <!-- Add TinyMCE editor to textarea  tinyMCEiD -->
    <!-- <script src="https://cdn.tiny.cloud/1/huc9qil6fqwtc81pirva1f9b66brfatrsjmwvekhk82bvukv/tinymce/5/tinymce.min.js"></script> -->
    <!-- <script>tinymce.init({ selector:'textarea' });</script> -->
</head>
<body>
    

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
				<a href="update.php?id=<?=$res['id']; ?>" class="edit_btn" >Edit</a>
			</td>
			<td>
				<a href="server.php?del=<?=$res['id']; ?>" class="del_btn">Delete</a>
			</td>
		</tr>
        <?php endforeach; ?>
    </table>

     
    </div>
</body>
</html>