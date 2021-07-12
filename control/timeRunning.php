<?php
include('../helper/Email.php');
include('../class/Monday.php');
include('../class/EmailTemplate.php');
$configApp = include('../class/ConfigApp.php');

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
		$monday = new Monday();
        $_queryGetIdByEmail = $monday->getMondayData($queryGetIdByEmail);
			
		$queryActivityLog2 = '{
				items(newest_first: true, limit: 1000) {
				  id
				  column_values(ids: "seguimiento_de_tiempo") {
					value
				  }
				}
			  }';
		
        $_queryActivityLog = $monday->getMondayData($queryActivityLog2);
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
					$maxTimeRunning = date('H:i:s', strtotime($configApp['maxTimeRunning']));
				    if($currentTime > $maxTimeRunning){
						$userEmail = null;
						//print_r($aditionalValue);
						//echo $aditionalValue;
/*
						foreach ($aditionalValue as $g_key => $g_value){
							$startedUserId = $g_value['started_user_id'];
							foreach ($_queryGetIdByEmail['data']['users'] as $z_key => $z_val) {
								if($z_val['id'] == $startedUserId) {
									$userEmail = $z_val['email'];
								}
							}
						}
						*/

						$itemUrl = "https://legaltec-desarrollo.monday.com/boards/1104694287/pulses/" . $itemId ."";//boardId base
 
						$emailTemplate = new EmailTemplate();
                        $params = array(
                            'ITEM_URL' => $itemUrl
                        );
		                $emailType = "timeRunning";
		                $emailData = $emailTemplate->getEmailData($emailType, $params);
      
		                $Email = new Email();
		                $emailAviso = $Email->sendEmail($configApp['to'], $configApp['cc'], "", "", $emailData["subject"], $emailData["emailContent"]);
		                
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


function RestarHoras($horaini,$horafin)
{
    $f1 = new DateTime($horaini);
    $f2 = new DateTime($horafin);
    $d = $f1->diff($f2);
    return $d->format('%H:%I:%S');
}
 
?>

 