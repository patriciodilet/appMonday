<?php
//GRAPHQL ACCESS
$token = 'eyJhbGciOiJIUzI1NiJ9.eyJ0aWQiOjEwMTY4OTU4MSwidWlkIjoyMDY0NzE3NSwiaWFkIjoiMjAyMS0wMy0wM1QxNToxMDoyMy41NDBaIiwicGVyIjoibWU6d3JpdGUiLCJhY3RpZCI6NzI5MzkzOSwicmduIjoidXNlMSJ9._dUd7R_8tLFW7Cg6sL6MFBX7xUuFGlM78XbukwI3V0c';
$apiUrl = 'https://api.monday.com/v2';
$headers = ['Content-Type: application/json', 'Authorization: ' . $token];
//

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
	    $colBId = getValuesByKey('columnId', $oArray);

		$query = '{ users(ids: ' . reset($userId) . ') {id name email} boards(ids: '. reset($boardId) . ') { items (ids: '. reset($itemId) .') { itemName: name column_values (ids: ["'. reset($colAId) .'", "'. reset($colBId) .'"]) { timeTrackingValues: value }}}}';
		 
		$data = @file_get_contents($apiUrl, false, stream_context_create([
			'http' => [
			 'method' => 'POST',
			 'header' => $headers,
			 'content' => json_encode(['query' => $query]),
			]
		   ]));

		   
		$response = json_decode($data, true);
		$mondayData = getValuesByKey(0, $response);
		
		$userEmail = $mondayData[1];
		$itemName = $mondayData[3];
	    $timeTrackingValues = $mondayData[4];
		$TPPcolumn = $mondayData[5];
		$result = json_decode($timeTrackingValues, true);

		if($result['running'] === 'false'){
		 
			$timetrack->setBoardId(reset($boardId));
			$timetrack->setItemId(reset($itemId));
			$timetrack->setUserId(reset($userId));
			$timetrack->setUserEmail($userEmail);
			$timetrack->setTPP($TPPcolumn);
			$timetrack->setItemName($itemName);
			$timetrack->setDuration($result['duration']);
			$timetrack->setDate();
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

function objectToArray( $object ){
	if( !is_object( $object ) && !is_array( $object ) ){
	 return $object;
  }
 if( is_object( $object ) ){
	 $object = get_object_vars( $object );
 }
	 return array_map( 'objectToArray', $object );
 }

 function filter_array_keys(array $array, $keys)
{
    if (is_callable($keys)) {
        $keys = array_filter(array_keys($array), $keys);
    }

    return array_intersect_key($array, array_flip($keys));
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

function array_key_first(array $array)
{
    return key(array_slice($array, 0, 1));
}

?>