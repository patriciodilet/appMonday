<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
include('../class/multiSort.php');
 

$requestMethod = $_SERVER["REQUEST_METHOD"];
switch($requestMethod) {
	case 'GET':

	 
		// $bodyHtml = "<html>";
    	// $bodyHtml .= "<body>";
    	// $bodyHtml .= '<h3>Rporte de Horas Extras</h3>';
    	// $bodyHtml .= "<p>Reporte disponible en el link: </p>";
    	// $bodyHtml .= "<a href='http://3.211.203.97/ApiMonday/report/activityLog.php' download>";
      
		// $bodyHtml .= "</body></html>";

		//echo $bodyHtml;


		// $filename =  time().".xls";      
        // header("Content-Type: application/vnd.ms-excel");
        // header("Content-Disposition: attachment; filename=\"$filename\"");
        // $filetosend = ExportFile($activityList);



	//	print_r($_activityList);



		//REFACTORIZAR
		$sender = 'test@8x.cl';
		$senderName = 'Legaltec Monday';
		 
		$usernameSmtp = 'test@8x.cl';
		$passwordSmtp = 'legaltec';
		$configurationSet = 'ConfigSet';
		$host = '8x.cl';
		$port = 587;

		$subject = 'Informe registro de horas extras';
		$bodyText =  "Registro de horas extras\r\n";
		
		// $bodyHtml = "<html>";
		// $bodyHtml .= "<body>";
		// $bodyHtml .= '<h1>Registro de actividades</h1>';
		// $bodyHtml .= '<span>Usuario consultado: '. $userEmail .'</span>';
		// $bodyHtml .= '<table rules="all" style="border-color: #666; width:100%;" cellpadding="10">';
    	// $bodyHtml .= "<tr style='background: #eee;'>
        //             <th>nameBoard</th>
        //             <th>itemName</th>
        //             <th>duration</th>
        //             <th>createdAt</th>
        //         </tr>";
		// $bodyHtml .= displayResultsAsTable($activityList);
		// $bodyHtml .= '</table>';
		// $bodyHtml .= "</body></html>";
        
		$bodyHtml = "<html>";
    	$bodyHtml .= "<body>";
    	$bodyHtml .= '<h3>Reporte de Horas Extras</h3>';
    	$bodyHtml .= "<p>Reporte disponible en el link: </p>";
    	$bodyHtml .= "<a href='http://3.211.203.97/ApiMonday/report/activityLog.php'> Descargar reporte</a>";
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
			$mail->addAddress('pdiazl@legaltec.cl');
			$mail->AddCC('pdiazl@legaltec.cl', 'Patricio Diaz');
			// $mail->AddCC('ksandoval@legaltec.cl', 'Keyla Sandoval');
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







        		 
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

function ExportFile($records) {
	$heading = false;
		if(!empty($records))
		  foreach($records as $row) {
			if(!$heading) {
			  // display field/column names as a first row
			  echo implode("\t", array_keys($row)) . "\n";
			  $heading = true;
			}
			echo implode("\t", array_values($row)) . "\n";
		}
	exit;
}

function getTimeDiff($dtime,$atime)
    {
        $nextDay = $dtime>$atime?1:0;
        $dep = explode(':',$dtime);
        $arr = explode(':',$atime);
        $diff = abs(mktime($dep[0],$dep[1],0,date('n'),date('j'),date('y'))-mktime($arr[0],$arr[1],0,date('n'),date('j')+$nextDay,date('y')));
        $hours = floor($diff/(60*60));
        $mins = floor(($diff-($hours*60*60))/(60));
        $secs = floor(($diff-(($hours*60*60)+($mins*60))));
        if(strlen($hours)<2){$hours="0".$hours;}
        if(strlen($mins)<2){$mins="0".$mins;}
        if(strlen($secs)<2){$secs="0".$secs;}
        return $hours.':'.$mins.':'.$secs;
    }

function setTimeZoneTo($dateToChange, $format){
	$original_timezone = new DateTimeZone('UTC');
	$datetime = new DateTime($dateToChange, $original_timezone);
	$target_timezone = new DateTimeZone('America/Santiago');
	$datetime->setTimeZone($target_timezone);

	switch($format) {
		case 1:
			return $datetime->format('H:i:s');
			break;
		case 2:
			return $datetime->format('d-m-Y');
			break;
		default;
			return $datetime->format('d-m-Y H:i:s');
			break;
		}
}

function sec_to_decimal($sec)
{
	# Time to Decimal Conversion 
	$init = $sec;
	$hours = floor($init / 3600);
	$minutes = floor(($init / 60) % 60);
	$seconds = $init % 60;

	#coming to formula
	$hh = $hours * (1 / 1);
	$mm = $minutes / 60;
	$ss = $seconds / 3600;

	#so total hours in decimal
	$totalHours = $hh + $mm + $ss;
	return round($totalHours, 1);
}

function masort($data, $sortby){
    if(is_array($sortby)){
        $sortby = join(',',$sortby);
    }

    uasort($data,create_function('$a,$b','$skeys = split(\',\',\''.$sortby.'\');
        foreach($skeys as $key){
            if( ($c = strcasecmp($a[$key],$b[$key])) != 0 ){
                return($c);
            }
        }
        return($c); '));
}

function decimalHours($time)
{
    $hms = explode(":", $time);
	$hours = ($hms[0] + ($hms[1]/60) + ($hms[2]/3600));
	
    return round($hours, 1, PHP_ROUND_HALF_EVEN);
}

function array_preg_filter_keys($arr, $regexp) {
	$keys = array_keys($arr);
	$match = array_filter($keys, function($k) use($regexp) {
	  return preg_match($regexp, $k) === 1;
	});
	return array_intersect_key($arr, array_flip($match));
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

 