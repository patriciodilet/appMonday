<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Email.php');
$email = new Email();

switch($requestMethod) {
	case 'POST':
		$json = file_get_contents('php://input');
		$oArray = json_decode($json, true);
		if($json === false || $oArray === null){
			echo '{"result":"FALSE","message":"Error to get data from Monday.com API"}';
			break;
		}
	    $itemId = getValuesByKey('itemId', $oArray);
	    $userId = getValuesByKey('userId', $oArray);
        $email->setUserId($userId); 
        $emailInfo = $email->getReportData();
        
        if(sizeof($emailInfo) > 0) {
            if(sendEmail($emailInfo)){
                foreach ($emailInfo as $item){
                    $email->setItemId($item["itemId"]);
                    $emailUpdate = $email->updateSendEmailStatusByItemId();
                }
                $js_encode = json_encode(array('status'=>TRUE, 'message'=>'email enviado'), true);
            }
        } else {
            $js_encode = json_encode(array('status'=>FALSE, 'message'=>'no se pudo enviar el email.'), true);
        }
        header('Content-Type: application/json');
        echo $js_encode;
		break;
	//default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;

    case 'GET':        
        $allReportData = $email->getAllReportData();

        $listEmail = array();
        foreach ($allReportData as $report) {
            array_push($listEmail, $report["userEmail"]);
        }
        $listEmail= array_unique($listEmail);
        foreach($listEmail as $val) {
            $email->setUserEmail($val);
            $reportData = $email->getReportData();

            if(sendEmail($reportData, $val)){
                foreach($reportData as $item){
                    $email->setItemId($item["itemId"]);
                    $emailUpdate = $email->updateSendEmailStatusByItemId();
                }
                print_r("email enviado");
            } else {
                print_r("email NO enviado");
            }
        }
        
		break;
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
 
/**
* Get all values from specific key in a multidimensional array
*
* @param $key string
* @param $arr array
* @return null|string|array
*/
function getValuesByKey($key, array $arr){
    $val = array();
    array_walk_recursive($arr, function($v, $k) use($key, &$val){
        if($k == $key) array_push($val, $v);
    });
    return count($val) > 1 ? $val : array_pop($val);
}

function getMondayData($query){
	//GRAPHQL ACCESS
	$token = 'eyJhbGciOiJIUzI1NiJ9.eyJ0aWQiOjEwMTY4OTU4MSwidWlkIjoyMDY0NzE3NSwiaWFkIjoiMjAyMS0wMy0wM1QxNToxMDoyMy41NDBaIiwicGVyIjoibWU6d3JpdGUiLCJhY3RpZCI6NzI5MzkzOSwicmduIjoidXNlMSJ9._dUd7R_8tLFW7Cg6sL6MFBX7xUuFGlM78XbukwI3V0c';
	$apiUrl = 'https://api.monday.com/v2';
	$headers = ['Content-Type: application/json', 'Authorization: ' . $token];
	//

	$data = @file_get_contents($apiUrl, false, stream_context_create([
		'http' => [
		'method' => 'POST',
		'header' => $headers,
		'content' => json_encode(['query' => $query]),
		]
	]));

	$result = json_decode($data, true);
	return $result;
}

function sendEmail($reportData, $email){
    //$userEmail = getValuesByKey('userEmail', $emailInfo);

    $sender = 'legaltec.linux@legaltec.cl';
    $senderName = 'Legaltec Monday';
    $recipient = $email;
    //$recipient = is_array($userEmail) ? reset($userEmail) : $userEmail;


    $usernameSmtp = 'legaltec.linux@legaltec.cl';
    $passwordSmtp = 'Ju87mWq#a#';
    $configurationSet = 'ConfigSet';
    $host = 'legaltec.cl';
    $port = 587;

    $subject = 'Informe registro de horas';
    $bodyText =  "Registro de Horas\r\n";
    
    $bodyHtml = "<html>";
    $bodyHtml .= "<body>";
    $bodyHtml .= '<h1>Registro de Horas</h1>';

    $bodyHtml .= '<table rules="all" style="border-color: #666; width:100%;" cellpadding="10">';
    $bodyHtml .= "<tr style='background: #eee;'>
                    <th>id Tarea</th>
                    <th>Usuario</th>
                    <th>Tablero</th>
                    <th>Tarea</th>
                    <th>Duración</th>
                    <th>TPP</th>
                    <th>Es Hito</th>
                    <th>Fecha de registro</th>
                    <th>Ultima actualización</th>
                    <th>Ultima respuesta</th>
                </tr>";
    $bodyHtml .= displayResultsAsTable($reportData);
    $bodyHtml .= '</table>';
    $bodyHtml .= "</body></html>";

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

        $mail->addAddress($recipient);
        $mail->AddCC('pdiazl@legaltec.cl', 'Patricio Diaz');
        //$mail->AddCC('ksandoval@legaltec.cl', 'Keyla Sandoval');
        //$mail->AddCC('mvenegas@legaltec.cl', 'Manuel Venegas');
        // $recipients = array(
        //     'person1@domain.com' => 'Person One',
        //     'person2@domain.com' => 'Person Two',
        //     // ..
        //  );
        //  foreach($recipients as $email => $name)
        //  {
        //     $mail->AddCC($email, $name);
        //  }

        $mail->isHTML(true);
        $mail->Subject    = $subject;
        $mail->Body       = $bodyHtml;
        $mail->AltBody    = $bodyText;
        $mail->CharSet    = 'UTF-8';
        $mail->Send();
        echo "Email sent!" , PHP_EOL;
        return true;
    } catch (phpmailerException $e) {
        echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
        return false;
    } catch (Exception $e) {
        echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from LEGALTEC API.
        return false;
    }
}

function displayResultsAsTable($resultsArray) {
    // argument must be an array
    if (is_array($resultsArray)) {
        foreach ($resultsArray as $key => $value) {
            $val .= '<tr>';
                foreach ($value as $f_key => $f_val) {
                    $val .= '<td>'. $f_val .'</td>';
                }
            $val .= '</tr>';
            }
        }
        return $val;
    }

?>