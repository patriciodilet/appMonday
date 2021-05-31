<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';
include('../class/multiSort.php');
include('../helper/Email.php');

 
$requestMethod = $_SERVER["REQUEST_METHOD"];
switch($requestMethod) {
	case 'GET':
		// Check running time every 10 minutes
		// #*/10 * * * 1-5 curl http://3.211.203.97/ApiMonday/control/timeRunning.php
		// http://3.211.203.97/ApiMonday/control/timeRunning.php

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
				items(newest_first: true, limit: 1000) {
				  id
				  column_values(ids: "seguimiento_de_tiempo") {
					value
				  }
				}
			  }';

		$_queryActivityLog = getMondayData($queryActivityLog2);
		$email = new Email();
		$itemsRunning[] = array();
		foreach (reset($_queryActivityLog) as $key => $data) {
			foreach ($data as $f_key => $f_val) {
			    //print_r($f_val);
				$itemId = $f_val['id'];
				$columnValue = $f_val['column_values'][0]['value'];
				$datadecoded = json_decode($columnValue, true);
				$running = $datadecoded['running'];
				if($running == 'true'){
					//print_r($datadecoded);

					$aditionalValue = $datadecoded['additional_value'];
					$_startDate =date('d-m-Y H:i:s', $datadecoded['startDate']);
					$_changedAt =date('d-m-Y H:i:s', $datadecoded['changed_at']);

					$TimeZoneNameFrom="UTC";
					$TimeZoneNameTo="America/Santiago";

					$startDate = date_create($_startDate, new DateTimeZone($TimeZoneNameFrom))
										->setTimezone(new DateTimeZone($TimeZoneNameTo))
										->format("d-m-Y H:i:s");

					$actualDate = date_create(date(), new DateTimeZone($TimeZoneNameFrom))
										->setTimezone(new DateTimeZone($TimeZoneNameTo))
										->format("d-m-Y H:i:s");

					$currentTime = RestarHoras($startDate, $actualDate);
					$maxTimeRunning = date('H:i:s', strtotime('00:00:10'));
				    if($currentTime > $maxTimeRunning){
						$userEmail = null;
						foreach ($aditionalValue as $g_key => $g_value){
							$startedUserId = $g_value['started_user_id'];
							foreach ($_queryGetIdByEmail['data']['users'] as $z_key => $z_val) {
								if($z_val['id'] == $startedUserId) {
									$userEmail = $z_val['email'];
								}
								//break;
							}
						}
						$itemUrl = "https://legaltec-desarrollo.monday.com/boards/1104694287/pulses/" . $itemId ."";//boardId base

						$bodyHtml = "<html>";
						$bodyHtml .= "<body>";
						$bodyHtml .= '<h3>Aviso de tiempo corriendo</h3>';
						$bodyHtml .= '<p>¿Sigues trabajando en éste ítem? Tu tiempo está corriendo <a href="' . $itemUrl . '">aquí</a></p>';
						$bodyHtml .= "</body></html>";

		                $cc = array("ksandoval@legaltec.cl", "mvenegas@legaltec.cl", "pdiazl@legaltec.cl");
		                $emailAviso = $email->sendEmail($userEmail, $cc, "", "", "Aviso de tiempo corriendo", $bodyHtml);
						$itemsRunning[] = array(
							"boardId" => $boardId,
							"itemId" => $itemId,
							"startDate" => $startDate,
							"actualDate" => $actualDate,
							"currentTime" => $currentTime,
							"email" => $userEmail,
							"emailAviso" => 1
						);
					}
				} 
			}
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

function getElapsedTime($datetime)
{
      if( empty($datetime) )
      {
            return;
      }
 
      // check datetime var type
	  date_default_timezone_set("America/Santiago");

      $strTime = ( is_object($datetime) ) ? $datetime->format('d-m-Y G:i:s') : $datetime;
 
      $time = strtotime($strTime);
      $time = time() - $time;
      $time = ($time<1)? 1 : $time;
 
      $tokens = array (
            31536000 => 'año',
            2592000 => 'mes',
            604800 => 'semana',
            86400 => 'día',
            3600 => 'hora',
            60 => 'minuto',
            1 => 'segundo'
      );
 
      foreach ($tokens as $unit => $text)
      {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            $plural = ($unit == 2592000) ? 'es' : 's';
            return $numberOfUnits . ' ' . $text . ( ($numberOfUnits > 1) ? $plural : '' );
      }
}

function RestarHoras($horaini,$horafin)
{
    $f1 = new DateTime($horaini);
    $f2 = new DateTime($horafin);
    $d = $f1->diff($f2);
    return $d->format('%H:%I:%S');
}
 
?>

 