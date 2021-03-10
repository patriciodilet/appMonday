<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Timetrack.php');
$timetrack = new Timetrack();
switch($requestMethod) {
	case 'GET':
		$empId = '';	
		if($_GET['id']) {
			$timetrackID = $_GET['id'];
			$timetrack->setTimetrackID($timetrackID);
		}
		$timetrackInfo = $timetrack->deleteTimetrack();
		if(!empty($timetrackInfo)) {
	      $js_encode = json_encode(array('status'=>TRUE, 'message'=>'TimeTrack deleted Successfully.'), true);
        } else {
            $js_encode = json_encode(array('status'=>FALSE, 'message'=>'TimeTrack delete failed.'), true);
        }
		header('Content-Type: application/json');
		echo $js_encode;
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}
?>