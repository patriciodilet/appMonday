<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Timetrack.php');
$timetrack = new Timetrack();
switch($requestMethod) {	
	case 'POST':
		$timetrackID = $_POST['id'];
		$timetrack = $_POST['timetrack']; 

		$timetrack->setTimetrackID($timetrackID);
	    $timetrack->setTimetrack($timetrack); 
		$timetrackInfo = $timetrack->updateTimetrack();
		if(!empty($timetrackInfo)) {
	      $js_encode = json_encode(array('status'=>TRUE, 'message'=>'timetrack updated Successfully'), true);
        } else {
            $js_encode = json_encode(array('status'=>FALSE, 'message'=>'timetrack updation failed.'), true);
        }
		header('Content-Type: application/json');
		echo $js_encode;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
?>