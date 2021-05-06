<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
include('../class/multiSort.php');
 
$requestMethod = $_SERVER["REQUEST_METHOD"];
switch($requestMethod) {
	case 'GET':

		//required** Get records by period of time ex:20-03 / 19-04    25-03/24-04 if ecl is set
		$period = $_GET['period'];

		// Includes records from today to period  
		$startToday = $_GET['period'];

		// includes records from yesterday
		$daily = $_GET['daily'];

		$ecl = $_GET['ecl'];

		 
		$userEmail = $_GET['useremail'];
		
		$queryGetIdByEmail = '
		{
		  users {
			id
			email 
		  }
		}
		';
		$_queryGetIdByEmail = getMondayData($queryGetIdByEmail);

		$queryActivityLog2 = '{
			boards {
			 id
				name
				items {
				id
				name
				column_values(ids: "seguimiento_de_tiempo") {
					text
					value
				}
				}
			}
			}
			';

		// $queryActivityLog2 = '{
		// 	boards (ids:1185834313) {
		// 		id
		// 		name
		// 		items {
		// 		id
		// 		name
		// 		column_values(ids: "time_tracking") {
		// 			text
		// 			value
		// 		}
		// 		}
		// 	}
		// 	}
		// 	';
		$_queryActivityLog = getMondayData($queryActivityLog2);

		$eclList[] = array();
		$staticsBoards[] = array();
		array_push($staticsBoards, array("boardId" => 796494829, "columnECLId" => "status7"));
		array_push($staticsBoards, array("boardId" => 1191780774, "columnECLId" => "status5"));
		array_push($staticsBoards, array("boardId" => 1137903278, "columnECLId" => "status6"));
		array_push($staticsBoards, array("boardId" => 1159710697, "columnECLId" => "status8"));
		array_push($staticsBoards, array("boardId" => 1023823823, "columnECLId" => "status8"));
		array_push($staticsBoards, array("boardId" => 1104728730, "columnECLId" => "status5"));
		array_push($staticsBoards, array("boardId" => 1104728730, "columnECLId" => "status5"));
		array_push($staticsBoards, array("boardId" => 1191461662, "columnECLId" => "status1"));
		array_push($staticsBoards, array("boardId" => 1238935655, "columnECLId" => "status9"));
		array_push($staticsBoards, array("boardId" => 1170246217, "columnECLId" => "status7"));
		array_push($staticsBoards, array("boardId" => 1104915179, "columnECLId" => "status6"));
		array_push($staticsBoards, array("boardId" => 1138711490, "columnECLId" => "status5"));
		array_push($staticsBoards, array("boardId" => 1113527699, "columnECLId" => "status9"));
		array_push($staticsBoards, array("boardId" => 1185834313, "columnECLId" => "status9"));
		
	 
		foreach (reset($_queryActivityLog) as $key => $data) {
			//print_r($data);
			foreach ($data as $f_key => $f_val) {
				//print_r($f_val);
				$nameBoard = $f_val['name'];
				$boardId = $f_val['id'];
				$items = $f_val['items'];

				//Get "sistema" column id 
				foreach($staticsBoards as $el){		
					if($el['boardId'] == $boardId){
						$colECL = $el['columnECLId'];
						break;
					} else {
						$colECL = "status1";
					}
				}

				foreach ($items as $key => $value) {
					$itemId = $value['id'];
					$itemName = $value['name'];
					$columnValues = $value['column_values'];
					$totalDuration = $columnValues[0]['text'];
					$logTime = $columnValues[0]['value'];

					$datadecoded = json_decode($logTime, true);
					$aditionalValue = $datadecoded['additional_value'];

					// if(isset($_GET['ecl'])) {
						//ECL code
					$queryBuscaECL = getMondayData('{boards (ids: ' . $boardId . ') {items (ids: '. $itemId .' ) {id column_values(ids: "' . $colECL . '") {text}}}}');
					$txtECL = getValuesByKey("text", $queryBuscaECL);

					if($txtECL == "ECL"){
						$eclList[] = $itemId;
					}
					// }
					
					
					$startedAt = null;
					$endedAt = null;
					$userEmail = null;
					foreach ($aditionalValue as $key => $valueo){
						//print_r($valueo);
						$entryId = $valueo['id'];
						$endedAt =date('d-m-Y H:i:s', strtotime($valueo['ended_at']));
						$createdAt =date('d-m-Y H:i:s', strtotime($valueo['created_at']));
						$updatedAt =date('d-m-Y H:i:s', strtotime($valueo['updated_at']));
						$startedAt =date('d-m-Y H:i:s', strtotime($valueo['started_at']));
						$finJornada = date('H:i:s', strtotime('18:30:00'));
						$fechaRegistro = setTimeZoneTo($endedAt, 2);
						$lastUpdate = setTimeZoneTo($updatedAt,2);
						//Check period of time (1, 2, 3, etc..) 1 = 20/currentMonth to 19/currentMonth -1
						$currentMonth = date('m-Y');
						$startedCurrentMonth = date("m-Y", strtotime("-" . $period . " months"));
						$endPeriod = isset($_GET["startToday"]) ? date('d-m-Y') : $endPeriod = '19-' . $currentMonth;
						$startedPeriod = '20-' . $startedCurrentMonth; 
						
						if (isset($_GET["daily"])){
							$yesterday = date("d-m-Y", strtotime("-1 day"));
							$endPeriod = $yesterday;
							$startedPeriod = $yesterday;
						}  

						$dayFechRegistro = date("d", strtotime($fechaRegistro));

						// Check items between period of 20/m to 19/m
						$boolPeriod = (strtotime($fechaRegistro) <= strtotime($endPeriod)) && 
									  (strtotime($fechaRegistro) >= strtotime($startedPeriod)) ? true : false;

						if($boolPeriod){
							// Check period  of change
							if(($dayFechRegistro >= 1) && ($dayFechRegistro <= 24) ){
								$monthStartedPeriod = date("m-Y", strtotime($fechaRegistro . "-1 months"));
								$monthEndPeriod = date("m-Y", strtotime($fechaRegistro));
							}

							if(($dayFechRegistro >= 25) && ($dayFechRegistro <= 31)){
								$monthStartedPeriod = date("m-Y", strtotime($fechaRegistro));
								$monthEndPeriod  = date("m-Y", strtotime($fechaRegistro . "+1 months"));
							} 				

							$dateEndPeriod= '24-' . $monthEndPeriod; 
							$dateStartedPeriod  = '25-' . $monthStartedPeriod; 

							// time registered out of period
							$entryHoursOutPeriod = (strtotime($lastUpdate) >= strtotime($dateStartedPeriod)) && 
												(strtotime($lastUpdate) <= strtotime($dateEndPeriod) ) ? false : true;
	
							$horasExtras = null;
							$horaInicio = setTimeZoneTo($startedAt,1);
							$horaFin = setTimeZoneTo($endedAt,1);
							$partialDuration =getTimeDiff($horaInicio,$horaFin);
							$horasDentroDeJornada = getTimeDiff($horaInicio,$finJornada);

							if (setTimeZoneTo($startedAt,1) > $finJornada || setTimeZoneTo($endedAt,1) > $finJornada) {
								if($horaInicio < $finJornada){
									$horasExtras = getTimeDiff($horasDentroDeJornada, $partialDuration );
								} else {
									$horasExtras = getTimeDiff( $horaInicio, $horaFin);
									$horasDentroDeJornada = 0;
								}
							} else {
								$horasDentroDeJornada = $partialDuration;
							} 

							// Get user email 
							$startedUserId = $valueo['started_user_id'];
							foreach ($_queryGetIdByEmail['data']['users'] as $z_key => $z_val) {
								if($z_val['id'] == $startedUserId) {
									$userEmail = $z_val['email'];
								}
							}

							 
							// if(decimalHours($horasExtras) > 0){
							$activityList[] = array(
								"boardId" => $boardId,
								"itemId" => $itemId,
								"entryId" => $entryId,
								"nameBoard" => $nameBoard,
								"userEmail" => $userEmail,
								"itemName" => $itemName,
								"date" => $fechaRegistro,
								"startedAt" => $horaInicio,
								"endedAt" => setTimeZoneTo($endedAt, 1),
								"HorasJornada" => decimalHours($horasDentroDeJornada),
								"HorasExtras" => decimalHours($horasExtras),
								"entryHourOutPeriod" => $entryHoursOutPeriod,
								"updatedAt" => setTimeZoneTo($updatedAt,2),
								// "isECL" => "",
								"link" => "https://legaltec-desarrollo.monday.com/boards/". $boardId . "/pulses/" . $itemId .""
							);
							// } 
							
						} 
					}
				}
			}
		}

		$activityListECL[] = null;
		foreach ($activityList as $index => $val) {			 
			if (in_array_multi($val['itemId'], $eclList)) {
				$activityList[$index]["isECL"] = "ECL";
				$activityListECL[] = $val;
			} 
		}

		uasort($activityListECL, function ($a, $b) { 
			return ( $a['userEmail'] < $b['userEmail'] ? 1 : -1 ); 
		});

		$activityListECL[] = array(
			"boardId" => "boardId",
			"itemId" => "itemId",
			"entryId" => "id entrada",
			"nameBoard" => "Proyecto",
			"userEmail" => "Usuario",
			"itemName" => "Tarea",
			"date" => "Fecha Registro",
			"startedAt" => "Hora Inicio",
			"endedAt" => "Hora Fin",
			"HorasJornada" => "Hora jornada",
			"HorasExtras" => "Horas extras",
			"entryHourOutPeriod" => "Horas fuera de periodo",
			"updatedAt" => "Ultima actualizacion",
			"link" => ""
		);
		//$csvHeader = "IdEntrada,Proyecto,Usuario,Tarea,FechaRegistro,HoraInicio,HoraFin,Horasextras,HorasFueradePeriodo,UltimaActualizacion";
		$cantHorasExtras = count($activityListECL) - 1;

		$bodyHtml = "<html>";
    	$bodyHtml .= "<body>";
    	$bodyHtml .= '<h1>Registro de horas ECL</h1>';
		$bodyHtml .= '<p>Desde: ' . $startedPeriod . '</p>';
		$bodyHtml .= '<p>Hasta: ' . $endPeriod . '</p>';
		$bodyHtml .= '<p>Cantidad: ' . $cantHorasExtras . '</p>';

		$bodyHtml .= '<table rules="all" style="border-color: #666; width:100%;" cellpadding="10">';
		$bodyHtml .= "<tr style='background: #eee;'>
					<th>boardId</th>
					<th>itemId</th>
					<th>IdEntrada</th>
					<th>Proyecto</th>
					<th>Usuario</th>
					<th>Tarea</th>
					<th>FechaRegistro</th>
					<th>HoraInicio</th>
					<th>HoraFin</th>
					<th>Horas Jornada</th>
					<th>HorasExtras</th>
					<th>HorasFueradePeriodo</th>
					<th>UltimaActualizacion</th>
					<th>link</th>
				</tr>";
		$bodyHtml .= displayResultsAsTable($activityListECL);
		$bodyHtml .= '</table>';

		$bodyHtml .= "</body></html>";

		echo $bodyHtml;
 
		
		
		$dir = '/var/www/html/ApiMonday/report/files/';
		$filename = md5(date('Y-m-d H:i:s:u'));
		$fp = fopen($dir . $filename .'.csv', 'w');
		foreach ($activityListECL as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);


		$attachment = $dir . $filename .'.csv';
		$sender = 'test@8x.cl';
		$senderName = 'Legaltec Monday';
		 
		$usernameSmtp = 'test@8x.cl';
		$passwordSmtp = 'legaltec';
		$configurationSet = 'ConfigSet';
		$host = '8x.cl';
		$port = 587;

		$subject = 'Informe registro de horas extras';
		$bodyText =  "Registro de horas extras\r\n";
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
			$mail->AddAttachment($attachment , 'Reporte Horas Extras ' . $startedPeriod . ' a ' . $endPeriod . '.csv');
			 
			$mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);
			// $mail->addAddress('pdiazl@legaltec.cl');
			$mail->addAddress('ksandoval@legaltec.cl');
			// $mail->AddCC('pdiazl@legaltec.cl', 'Patricio Diaz');
			// $mail->AddCC('ksandoval@legaltec.cl', 'Keyla Sandoval');
			$mail->AddCC('mvenegas@legaltec.cl', 'Manuel Venegas');
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

function array_value_recursive($key, array $arr){
    $val = array();
    array_walk_recursive($arr, function($v, $k) use($key, &$val){
        if($k == $key) array_push($val, $v);
    });
    return count($val) > 1 ? $val : array_pop($val);
}


function getValuesByKey($key, array $arr){
    $val = array();
    array_walk_recursive($arr, function($v, $k) use($key, &$val){
        if($k == $key) array_push($val, $v);
    });
    return count($val) > 1 ? $val : array_pop($val);
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
			return $datetime->format('H:i');
			break;
		case 2:
			return $datetime->format('d-m-Y');
			break;
		case 3:
			return $datetime->format('d-m-Y H:i:s');
			break;
		case 4:
			return $datetime->format('m');
			break;
		case 5:
			return $datetime->format('m-y');
			break;
		default:
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

function in_array_multi($needle, $haystack) {
    foreach ($haystack as $item) {
        if ($item === $needle || (is_array($item) & in_array_multi($needle, $item))) {
            return true;
        }
    }
 
    return false;
}


function create_csv_string($data) {
   
    mysql_connect(HOST, USERNAME, PASSWORD);
    mysql_select_db(DATABASE);
   
    $data = mysql_query('SELECT id, company, name, company_account_number, email, phone_number, invoice FROM carlofontanos_table');

    // Open temp file pointer
    if (!$fp = fopen('php://temp', 'w+')) return FALSE;
   
    fputcsv($fp, array('ID', 'Company', 'Name', 'Company Account Number', 'Email', 'Phone Number', 'Invoice'));
   
    // Loop data and write to file pointer
    while ($line = mysql_fetch_assoc($data)) fputcsv($fp, $line);
   
    // Place stream pointer at beginning
    rewind($fp);

    // Return the data
    return stream_get_contents($fp);

}

function send_csv_mail1($csvData, $body, $to = 'pdiazl@legaltec.cl', $subject = 'Website Report', $from = 'noreply@legaltec.cl') {

    // This will provide plenty adequate entropy
    $multipartSep = '-----'.md5(time()).'-----';

    // Arrays are much more readable
    $headers = array(
        "From: $from",
        "Reply-To: $from",
        "Content-Type: multipart/mixed; boundary='$multipartSep'"
    );

    // Make the attachment
    $attachment = chunk_split(base64_encode(create_csv_string($csvData)));

    // Make the body of the message
    $body = "--$multipartSep\r\n"
        . "Content-Type: text/plain; charset=ISO-8859-1; format=flowed\r\n"
        . "Content-Transfer-Encoding: 7bit\r\n"
        . "\r\n"
        . "$body\r\n"
        . "--$multipartSep\r\n"
        . "Content-Type: text/csv\r\n"
        . "Content-Transfer-Encoding: base64\r\n"
        . "Content-Disposition: attachment; filename='Website-Report-' . date('F-j-Y') . '.csv'\r\n"
        . "\r\n"
        . "$attachment\r\n"
        . "--$multipartSep--";

    // Send the email, return the result
    return mail($to, $subject, $body, implode("\r\n", $headers));

}



function send_csv_mail($data, $body, $to = 'pdiazl@legaltec.cl', $subject = 'Website Report', $from = 'noreply@legaltec.cl') {

  
	if (!$fp = fopen('php://temp', 'w+')) echo "unable to create csv";
	foreach ($data as $row)
	{
		fputcsv($fp, $row);
	}
	rewind($fp);
	$csvData = stream_get_contents($fp);

		
	// Make the attachment
    $attachment = chunk_split(base64_encode($csvData)); 
	$filename = "report_".date('m-d-Y').".csv";


    $eol = PHP_EOL;
	$uid = md5(uniqid(time()));
	// Basic headers
	$header = "From: ".$from." <".$from.">".$eol;
	$header .= "Reply-To: ".$from.$eol;
	$header .= "MIME-Version: 1.0\r\n";
	$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"";

	// Put everything else in $message
	$message = "--".$uid.$eol;
	$message .= "Content-Type: text/html; charset=ISO-8859-1".$eol;
	$message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
	$message .= $body.$eol;
	$message .= "--".$uid.$eol;
	$message .= "Content-Type: application/csv; name=\"".$filename."\"".$eol;
	$message .= "Content-Transfer-Encoding: base64".$eol;
	$message .= "Content-Disposition: attachment; filename=\"".$filename."\"".$eol;
	$message .= $attachment.$eol;
	$message .= "--".$uid."--";

	if (mail($to, $subject, $message, $header))
	{
	    return "mail_success";
	}
	else
	{
	    return "mail_error";
	}

}


function array2csv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
{
    $f = fopen('php://memory', 'r+');
    foreach ($data as $item) {
        fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
    }
    rewind($f);
    return stream_get_contents($f);
}

function generateCsv($data, $delimiter = ',', $enclosure = '"') {
	$handle = fopen('php://temp', 'r+');
	foreach ($data as $line) {
			fputcsv($handle, $line, $delimiter, $enclosure);
	}
	rewind($handle);
	while (!feof($handle)) {
			$contents .= fread($handle, 8192);
	}
	fclose($handle);
	return $contents;
}
 
?>

 