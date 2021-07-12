<?php
include('../class/EmailTemplate.php');
session_start();
echo $_POST['update'];
echo $_POST['submit'];
if (isset($_POST['update'])) {
	$id = $_POST['id'];
	$templateName = $_POST['templateName'];
	$subject = $_POST['subject'];
	$content = $_POST['content'];

    $emailTemplate = new EmailTemplate();
    $emailTemplate->setId($id);
    $record = $emailTemplate->updateEmailTemplate();
     if(!empty($record)) {
        $_SESSION['status'] = 'succ';
        $_SESSION['msg'] = 'Email template has been created successfully.';
      } else {
        $_SESSION['status'] = 'succ';
        $_SESSION['msg'] = "Error, can't update record";
    }

	 
}
header("Location: add-template.php");
?>