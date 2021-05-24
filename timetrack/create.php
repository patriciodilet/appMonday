<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Timetrack.php');
$timetrack = new Timetrack();
switch($requestMethod) {
	case 'POST':
		$json = file_get_contents('php://input');
		$oArray = json_decode($json, true);
		if($json === false || $oArray === null){
			echo '{"result":"FALSE","message":"Error to get data from Monday.com API"}';
			break;
		}
		
		$boardId = getValuesByKey('boardId', $oArray);
	    $itemId = getValuesByKey('itemId', $oArray);
	    $userId = getValuesByKey('userId', $oArray);
	    $colAId = getValuesByKey('texto', $oArray);
	    $colTpp = getValuesByKey('columnId', $oArray);
	    $colBId = getValuesByKey('columnId', $oArray);
	    $colCId = getValuesByKey('cronogramaId', $oArray);

		$queryGeneral = '{
			users(ids: ' . reset($userId) . ') {
			  email
			}
			boards(ids: '. reset($boardId) . ') {
			  nameBoard: name
			  items(ids: '. reset($itemId) .') {
				itemName: name
				updates {
				  postText: text_body
				  creatorIdPost: creator_id
				  replies {
					idResponse: id
					responseText: text_body
					creatorIdResponse: creator_id
				  }
				}
			  }
			}
		  }';
		$_queryGeneral = getMondayData($queryGeneral);
		$emailUsuario = getValuesByKey('email', $_queryGeneral);
		$itemName = getValuesByKey('itemName', $_queryGeneral);
		$nameBoard = getValuesByKey('nameBoard', $_queryGeneral);
		$postText = getValuesByKey('postText', $_queryGeneral);
		$responseText = getValuesByKey('responseText', $_queryGeneral);
		$lastResponseId = getValuesByKey('idResponse', $_queryGeneral); //usar end()
		$creatorIdResponse = getValuesByKey('creatorIdResponse', $_queryGeneral); //usar end()
		$creatorIdPost = getValuesByKey('creatorIdPost', $_queryGeneral); //usar end()

		$queryCronograma = '{
			items(ids: '. reset($itemId) .') {
			  column_values(ids: ["'. reset($colCId) .'"]) {
				value
			  }
			}
		  }
		  ';
		$_queryCronograma = getMondayData($queryCronograma);
		$cronograma = getValuesByKey('value', $_queryCronograma);
		$hito = json_decode($cronograma, true);
		
		$queryTpp = '{
			items(ids: '. reset($itemId) .') {
			  column_values(ids: ["'. reset($colTpp) .'"]) {
				text
				value
			  }
			}
		  }
		  ';
		$_queryTpp = getMondayData($queryTpp);
		$tpp = getValuesByKey('text', $_queryTpp);

		$queryTimeTracking = '{
			items(ids: '. reset($itemId) .') {
			  column_values(ids: ["'. reset($colAId) .'"]) {
				value
			  }
			}
		  }
		  ';
		$_queryTimeTracking = getMondayData($queryTimeTracking);
		$timeTrackingData = getValuesByKey('value', $_queryTimeTracking);
		$timeTracking = json_decode($timeTrackingData, true);
 
		// retorno de ejemplo de columna Cronograma
		//"{\"from\":\"2021-03-12\",\"to\":\"2021-03-12\",\"visualization_type\":\"milestone\",\"changed_at\":\"2021-03-12T14:08:52.537Z\"}"
        $hito = is_array($hito) ? $hito : array();
		if(array_key_exists("visualization_type", $hito)){
			$milestone = 1;
		} else {
			$milestone = 0;
		}
	 
		if($timeTracking["running"] === 'false'){
			$timetrack->setBoardId(reset($boardId));
			$timetrack->setItemId(reset($itemId));
			$timetrack->setUserId(reset($userId));
			$timetrack->setUserEmail($emailUsuario);
			$timetrack->setTPP($tpp);
			$timetrack->setItemName($itemName);
			$timetrack->setDuration($timeTracking["duration"]);
			$timetrack->setMilestone($milestone);
			$timetrack->setDate();
			$timetrack->setResponseText($responseText);
			$timetrack->setLastResponseId($lastResponseId);
			$timetrack->setCreatorIdResponse($creatorIdResponse);
			$timetrack->setCreatorIdPost($creatorIdPost);
			$timetrack->setPostText($postText); 
			$timetrack->setNameBoard($nameBoard); 

			$now = date('d-m-Y');
			$isHolyday = checkHoliday($now);
			$timetrack->setIsHoliday($isHolyday); 
			$timetrackInfo = $timetrack->createTimetrack();

			if(!empty($timetrackInfo)) {
				$js_encode = json_encode(array('status'=>TRUE, 'message'=>'TimeTrack created Successfully'), true);
			} else {
				$js_encode = json_encode(array('status'=>FALSE, 'message'=>'TimeTrack creation failed.'), true);
			}
			header('Content-Type: application/json');
			echo $js_encode;
		}
		
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
 
function checkHoliday($date){
	if(date('l', strtotime($date)) == 'Saturday'){
		return false;
	//   return "Saturday";
	}else if(date('l', strtotime($date)) == 'Sunday'){
		return true;
	//   return "Sunday";
	}else{
	  $receivedDate = date('d M', strtotime($date));
  
	  $holiday = array(
		'01 Jan' => 'New Year Day',
		'02 Apr' => 'Viernes Santo',
		'03 Apr' => 'Sabado Santo',
		'01 May' => 'Dia del Trabajador',
		'15 May' => 'Eleccion Alcaldes',
		'16 May' => 'Eleccion Alcaldes',
		'13 Jun' => 'Segunda Vuelta Gobernadores Regionales',
		'28 Jun' => 'San Pedro y San Pablo',
		'16 Jul' => 'Dia de la Virgen del Carmen',
		'18 Jul' => 'Elecciones Primarias Presidenciales',
		'15 Ago' => 'Asuncion de la Virgen', 
		'17 Sep' => 'Feriado Adicional',
		'18 Sep' => 'Independencia Nacional',
		'19 Sep' => 'Dia de las Glorias del Ejercito',
		'11 Oct' => 'Dia de la Raza',
		'31 Oct' => 'Dia de las Iglesias Evangélicas', 
		'01 Nov' => 'Dia de Todos los Santos',
		'21 Nov' => 'Elecciones Presidenciales y Parlamentarias',
		'08 Dec' => 'Inmaculada Concepcion',
		'19 Dec' => 'Segunda Vuelta Elecciones Presidenciales',
		'25 Dec' => 'Navidad',
		'31 Dec' => 'Feriado Bancario'
	  );
  
	  foreach($holiday as $key => $value){
		if($receivedDate == $key){
			return true;
		//   return $value;
		}
	  }
	}
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

 

?>