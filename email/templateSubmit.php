<?php

include('../class/EmailTemplate.php');

//session_start();

// if (isset($_POST['update'])) {
    echo $_POST['templateName'];
	$id = $_POST['id'];
	$templateName = $_POST['templateName'];
	$subject = $_POST['subject'];
	$content = $_POST['content'];

    $emailTemplate = new EmailTemplate();
    $emailTemplate->setId($id);
    $emailTemplate->setType($templateName);
    $emailTemplate->setTitle($subject);
    $emailTemplate->setContent($content);
    $emailTemplate->setCreated();
    $emailTemplate->setModified();
    $emailTemplate->setStatus();
    $record = $emailTemplate->updateEmailTemplate();
    print_r($record);
    /*
     if(!empty($record)) {
        $_SESSION['status'] = 'succ';
        $_SESSION['msg'] = 'Email template has been created successfully.';
      } else {
        $_SESSION['status'] = 'err';
        $_SESSION['msg'] = "Error, can't update record";
    }
*/
	 
// }

/*
if(isset($_POST['submit'])){
    if(!empty($_POST['templateName']) && !empty($_POST['subject']) && !empty($_POST['content'])){
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setType($_POST['templateName']);
        $emailTemplate->setTitle($_POST['subject']);
        $emailTemplate->setContent($_POST['content']);
        $emailTemplate->setCreated();
        $emailTemplate->setModified();
        $emailTemplate->setStatus();

        $result = $emailTemplate->createEmailTemplate();

		if(!empty($result)) {
            $_SESSION['status'] = 'succ';
            $_SESSION['msg'] = 'Email template has been created successfully.';
		} else {
            $_SESSION['status'] = 'err';
            $_SESSION['msg'] = 'Some problem occurred, please try again.';
		}

    }else{
        $_SESSION['status'] = 'err';
        $_SESSION['msg'] = 'All fields are mandatory, please fill all the fields.';
    }
}
*/
// header("Location: add-template.php");
?>