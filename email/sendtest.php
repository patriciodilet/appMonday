<?php
// include('../class/Email.php');  //
include('../class/EmailTemplate.php');  // 
include('../class/Control.php');
// include('../class/Monday.php');
//$configApp = include('../class/ConfigApp.php');


$emailTemplate = new EmailTemplate();
$params = array(
    'ITEM_URL' => "google.cl"
);
$emailType = "timeRunning";
$emailData = $emailTemplate->getEmailData($emailType, $params);
print_r($emailTemplate);



// $userName  = 'Patricio Diaz';
// $userEmail = 'patricio.dilet@gmail.com';

// $query = '
// 		{
// 		  users {
// 			id
// 			email 
// 		  }
// 		}
// 		';
// $monday = new Monday();
//$monday->setQuery($query);
// $_queryGetIdByEmail2 = $monday->getMondayData($query);
// print_r($_queryGetIdByEmail2);



// $Email = new Email();
// $emailData = $Email->getEmailData("test2");
// print_r($emailData);
// print_r($emailData["emailContent"]);


/*
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
*/

//$emailContent = $emailData["emailContent"];
//$subject = $emailData["subject"];
//echo $subject;

 //$res = $Email->sendEmail($userEmail, $cc, "", "", $subject, $emailContent);

//$dataTemplate = $Email->getEmailData("test2");
// $Email = new Email();
// $emailTemplate = new EmailTemplate();

// $res = $Email->sendEmail($configApp['to'], $configApp['cc'], "", "", "mi asunto", "mi contenido");
// $res = $Email->sendEmail($configApp['to'], $configApp['cc'], "", "", $dataTemplate['title'], $dataTemplate['content']);


 
 //echo $res;

?>