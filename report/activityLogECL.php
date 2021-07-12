<?php
include('../helper/Email.php');
include('../class/EmailTemplate.php');
include('../class/Monday.php');
$configApp = include('../class/ConfigApp.php');
require_once '../helper/Functions.php';
 
$requestMethod = $_SERVER["REQUEST_METHOD"];
switch($requestMethod) {
	case 'GET':

		// send every 26 at 8 am
		// 0 8 26 * * curl http://3.211.203.97/ApiMonday/report/activityLogECL.php?period=1&ecl=1&startToday=1

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
		$monday = new Monday();
        $_queryGetIdByEmail = $monday->getMondayData($queryGetIdByEmail);
			
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

		// $_queryActivityLog = getMondayData($queryActivityLog2);
		$_queryActivityLog = $monday->getMondayData($queryActivityLog2);


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

					$queryBuscaECL = $monday->getMondayData('{boards (ids: ' . $boardId . ') {items (ids: '. $itemId .' ) {id column_values(ids: "' . $colECL . '") {text}}}}');
					$txtECL = Functions::getValuesByKey("text", $queryBuscaECL);

					if($txtECL == "ECL"){
						$eclList[] = $itemId;
					}
					
					
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
						$finJornada = date('H:i:s', strtotime($configApp['finJornada']));
						$fechaRegistro = Functions::setTimeZoneTo($endedAt, 2);
						$lastUpdate = Functions::setTimeZoneTo($updatedAt,2);
						//Check period of time (1, 2, 3, etc..) 1 = 20/currentMonth to 19/currentMonth -1
						$currentMonth = date('m-Y');
						$startedCurrentMonth = date("m-Y", strtotime("-" . $period . " months"));
						$startedPeriod = $configApp['dayStartPeriodECL'] . '-' . $startedCurrentMonth;
						$endMonth= date('m-Y', strtotime("+" . $period . " month", strtotime($startedPeriod)));
						$endPeriod = $configApp['dayEndPeriodECL'] . '-' . $endMonth;

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
							if(($dayFechRegistro >= 1) && ($dayFechRegistro <= 19) ){
								$monthStartedPeriod = date("m-Y", strtotime($fechaRegistro . "-1 months"));
								$monthEndPeriod = date("m-Y", strtotime($fechaRegistro));
							}

							if(($dayFechRegistro >= 20) && ($dayFechRegistro <= 31)){
								$monthStartedPeriod = date("m-Y", strtotime($fechaRegistro));
								$monthEndPeriod  = date("m-Y", strtotime($fechaRegistro . "+1 months"));
							} 				

							$dateEndPeriod= $configApp['dayEndPeriodECL'] . '-' . $monthEndPeriod; 
							$dateStartedPeriod  = $configApp['dayStartPeriodECL'] . '-' . $monthStartedPeriod; 

							// time registered out of period
							$entryHoursOutPeriod = (strtotime($lastUpdate) >= strtotime($dateStartedPeriod)) && 
												(strtotime($lastUpdate) <= strtotime($dateEndPeriod) ) ? false : true;
	
							$horasExtras = null;
							$horaInicio = Functions::setTimeZoneTo($startedAt,1);
							$horaFin = Functions::setTimeZoneTo($endedAt,1);
							$partialDuration = Functions::getTimeDiff($horaInicio,$horaFin);
							$horasDentroDeJornada = Functions::getTimeDiff($horaInicio,$finJornada);

							if (Functions::setTimeZoneTo($startedAt,1) > $finJornada || Functions::setTimeZoneTo($endedAt,1) > $finJornada) {
								if($horaInicio < $finJornada){
									$horasExtras = Functions::getTimeDiff($horasDentroDeJornada, $partialDuration );
								} else {
									$horasExtras = Functions::getTimeDiff( $horaInicio, $horaFin);
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

							 
							$activityList[] = array(
								"boardId" => $boardId,
								"itemId" => $itemId,
								"entryId" => $entryId,
								"nameBoard" => $nameBoard,
								"userEmail" => $userEmail,
								"itemName" => $itemName,
								"date" => $fechaRegistro,
								"startedAt" => $horaInicio,
								"endedAt" => Functions::setTimeZoneTo($endedAt, 1),
								"HorasJornada" => Functions::decimalHours($horasDentroDeJornada),
								"HorasExtras" => Functions::decimalHours($horasExtras),
								"entryHourOutPeriod" => $entryHoursOutPeriod,
								"updatedAt" => Functions::setTimeZoneTo($updatedAt,2),
								"link" => "https://legaltec-desarrollo.monday.com/boards/". $boardId . "/pulses/" . $itemId .""
							);							
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

	 
		$headers =[
			"boardId",
			"itemId",
			"entryId",
			"nameBoard",
			"userEmail",
			"itemName",
			"date",
			"startedAt",
			"endedAt",
			"HorasJornada",
			"HorasExtras",
			"entryHourOutPeriod",
			"updatedAt",
			"link"];

		$cantHorasExtras = count($activityListECL) - 1;
 
	    $emailTemplate = new EmailTemplate();
        $params = array(
            'STARTED_PERIOD' => $startedPeriod,
            'END_PERIOD' => $endPeriod,
			'HORAS_EXTRA' => $cantHorasExtras
        );
		$emailType = "activityLogECL";
		$emailData = $emailTemplate->getEmailData($emailType, $params);
      
		$Email = new Email();
		$res = $Email->sendEmail($configApp['to'], $configApp['cc'], $headers, $activityListECL, $emailData["subject"], $emailData["emailContent"]);
		echo $res;

		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

function in_array_multi($needle, $haystack) {
    foreach ($haystack as $item) {
        if ($item === $needle || (is_array($item) & in_array_multi($needle, $item))) {
            return true;
        }
    }
 
    return false;
}


 
?>

 