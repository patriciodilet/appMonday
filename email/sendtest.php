<?php
include('../class/Email.php');  // emailTemplate
$configApp = include('../class/configApp.php');

echo $configApp['tinyMCEiD'];

$userName  = 'Patricio Diaz';
$userEmail = 'patricio.dilet@gmail.com';



//replace template var with value
$params = array(
    'USER_NAME' => $userName
    // 'USER_EMAIL'=> $userEmail,
    //'CONTENT_TEST'=> $testing
);

$pattern = '[%s]';
foreach($params as $key=>$val){
    // concatenate pattern & val (space between words)
    $paramsPattern[sprintf($pattern,$key)] = $val;
}

$Email = new Email();
//get email template data from database
$Email->setType("test2");
$dataTemplate = $Email->getEmailTemplate();

$emailContent = strtr($dataTemplate['content'], $paramsPattern);
$subject = strtr($dataTemplate['title'],$paramsPattern);

//$res = $Email->sendEmail($userEmail, $cc, "", "", $subject, $emailContent);
echo $res;
//Send email
// if($res):
//     $successMsg = 'Email has sent successfully.';
// else:
//     $errorMsg = 'Email sending fail.';
// endif;


?>