<?php
    
$requestMethod = $_SERVER["REQUEST_METHOD"];
switch($requestMethod) {
	case 'POST':
		$json = file_get_contents('php://input');
		$oArray = json_decode($json, true);
		if($json === false || $oArray === null){
			echo '{"result":"FALSE","message":"Error to get data from Monday.com API"}';
			break;
		}

        $_boardId = getValuesByKey('boardId', $oArray);
        $boarId = reset($_boardId);

	    $_itemId = getValuesByKey('itemId', $oArray);
        $itemId = reset($_itemId);
 
        $query = '{
            items(ids:' . $itemId . ') {
              id
              column_values(ids: "date") {
                value
              }
            }
          }';
        $_query = getMondayData($query);
        $_value = getValuesByKey('value', $_query);
		$fechaInicio = json_decode($_value, true);
		// print_r($fechaInicio['date']);

        $query = '{
            items(ids:' . $itemId . ') {
              id
              column_values(ids: "numbers") {
                value
              }
            }
          }';
        $_queryNumbers = getMondayData($query);
        $_hoursPerDay = getValuesByKey('value', $_queryNumbers);
		$hoursPerDay = json_decode($_hoursPerDay, true);

        $query = '{
            items(ids:' . $itemId . ') {
              id
              column_values(ids: "n_meros3") {
                value
              }
            }
          }';
        $_queryEstimatedHH = getMondayData($query);
        $_estimatedHH = getValuesByKey('value', $_queryEstimatedHH);
		$estimatedHH = json_decode($_estimatedHH, true);
        $estimatedDays = $estimatedHH / $hoursPerDay;

        $endedAt = date('Y-m-d', strtotime($fechaInicio['date'] . ' +' . $estimatedDays . ' day'));
    
        $queryCronograma = 'mutation {
            change_column_value (board_id: ' . $boarId . ', item_id: ' . $itemId . ', column_id: "cronograma", value: "{\"from\":\"' . $fechaInicio['date'] . '\",\"to\":\"' . $endedAt . '\"}") {
            id
            }
            }
          ';
        $_queryCronograma = getMondayData($queryCronograma);

        // print_r($_queryCronograma);

		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}



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
        'content' => json_encode(['query' => $query, 'variables' => $vars]),
        // 'content' => json_encode(['query' => $query]),
        ]
    ]));

    $result = json_decode($data, true);
    return $result;
}
?>