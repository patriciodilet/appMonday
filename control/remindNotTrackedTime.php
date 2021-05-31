<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Control.php');
include('../helper/Email.php');

$control = new Control();
switch($requestMethod) {
	case 'GET':
		//  send email to users who have not registered time tracking
		// 0 9 * * 1-5 curl http://3.211.203.97/ApiMonday/control/remindNotTrackedTime.php

		$bodyHtml = "<html>";
		$bodyHtml .= "<body>";
		$bodyHtml .= '<h3>Aviso registro de tiempo</h3>';
		$bodyHtml .= '<p>Por favor registra tu tiempo en <a href="https://legaltec-desarrollo.monday.com/">Monday.com</a></p>';
		$bodyHtml .= "</body></html>";

	 
		$controlInfo = $control->getUsersUntrackedTime();
		if(!empty($controlInfo)) {
			$js_encode = json_encode(array('status'=>TRUE, 'controlInfo'=>$controlInfo), true);
			$datadecoded = json_decode($js_encode, true);
			$userList = $datadecoded["controlInfo"];

			foreach ($userList as $key => $value) {
			    $email = new Email();
				// $cc = array("ksandoval@legaltec.cl", "mvenegas@legaltec.cl", "pdiazl@legaltec.cl");
				// $res = $email->sendEmail($value['userEmail'], $cc, "", "", "Aviso de no registro de tiempo", $bodyHtml);
				$bodyHtml .= $value['userEmail'];
				$res = $email->sendEmail("patricio.dilet@gmail.com", $cc, "", "", "Aviso de no registro de tiempo", $bodyHtml);
				
			}
        } else {
            $js_encode = json_encode(array('status'=>FALSE, 'message'=>'There is no record yet.'), true);
        }


		header('Content-Type: application/json');
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}


 
?>