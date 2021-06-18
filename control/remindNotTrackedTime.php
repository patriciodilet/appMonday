<?php
include('../class/Control.php');
include('../helper/Email.php');
include('../class/EmailTemplate.php');
$configApp = include('../class/ConfigApp.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];
switch($requestMethod) {
	case 'GET':
		//  send email to users who have not registered time tracking
		// 0 9 * * 1-5 curl http://3.211.203.97/ApiMonday/control/remindNotTrackedTime.php
        $control = new Control();
		$controlInfo = $control->getUsersUntrackedTime();
		if(!empty($controlInfo)) {
			$js_encode = json_encode(array('status'=>TRUE, 'controlInfo'=>$controlInfo), true);
			$datadecoded = json_decode($js_encode, true);
			$userList = $datadecoded["controlInfo"];

			foreach ($userList as $key => $value) {
				$emailTemplate = new EmailTemplate();
                $params = array(
                    'USER_EMAIL' => $value['userEmail']
                );
		        $emailType = "remindNotTrackedTime";
		        $emailData = $emailTemplate->getEmailData($emailType, $params);

		        $Email = new Email();
		        $emailAviso = $Email->sendEmail($configApp['to'], $configApp['cc'], "", "", $emailData["subject"], $emailData["emailContent"]);
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