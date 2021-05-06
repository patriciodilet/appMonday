<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Control.php');
$control = new Control();
switch($requestMethod) {
	case 'GET':
		$controlInfo = $control->getUsersUntrackedTime();
		if(!empty($controlInfo)) {
			$js_encode = json_encode(array('status'=>TRUE, 'controlInfo'=>$controlInfo), true);
			$datadecoded = json_decode($js_encode, true);
			$userList = $datadecoded["controlInfo"];
			 
			foreach ($userList as $key => $value) {
				sendEmail($value['userEmail']);
			}
        } else {
            $js_encode = json_encode(array('status'=>FALSE, 'message'=>'There is no record yet.'), true);
        }
		header('Content-Type: application/json');
		// echo $js_encode;
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

function sendEmail($from){
	 
	$bodyHtml = "<html>";
	$bodyHtml .= "<body>";
	$bodyHtml .= '<h3>Aviso no registro de tiempo</h3>';
	$bodyHtml .= '<p>Por favor registra tu seguimiento de tiempo en <a href="https://legaltec-desarrollo.monday.com/">Monday.com</a></p>';
	
	$bodyHtml .= "</body></html>";

	$sender = 'test@8x.cl';
	$senderName = 'Legaltec Monday';
		
	$usernameSmtp = 'test@8x.cl';
	$passwordSmtp = 'legaltec';
	$configurationSet = 'ConfigSet';
	$host = '8x.cl';
	$port = 587;

	$subject = 'Aviso de tiempo no registrado';
	$bodyText =  "Aviso de tiempo no registrado\r\n";
	$mail = new PHPMailer(true);

	try {
		$mail->isSMTP();
		$mail->setFrom($sender, $senderName);
		$mail->Username   = $usernameSmtp;
		$mail->Password   = $passwordSmtp;
		$mail->Host       = $host;
		$mail->Port       = $port;
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = 'tls';
		$mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);
		$mail->addAddress($from);
		$mail->isHTML(true);
		$mail->Subject    = $subject;
		$mail->Body       = $bodyHtml;
		$mail->AltBody    = $bodyText;
		$mail->CharSet    = 'UTF-8';
		$mail->Send();
		return true;
	} catch (phpmailerException $e) {
		return false;
	} catch (Exception $e) {
		return false;
	}
}
 
?>