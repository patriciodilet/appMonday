<?php
include('../class/EmailTemplate.php');

session_start();
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
header("Location: add-template.php");
?>