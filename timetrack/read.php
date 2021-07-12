<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];
include('../class/Timetrack.php');
$timetrack = new Timetrack();
switch($requestMethod) {
	case 'GET':
		$timetrackID = '';	
		if($_GET['id']) {
			$timetrackID = $_GET['id'];
			$timetrack->setTimetrackID($timetrackID);
			$timetrackInfo = $timetrack->getTimetrack();
		} else {
			$timetrackInfo = $timetrack->getAllTimetrack();
		}
		if(!empty($timetrackInfo)) {
	      $js_encode = json_encode(array('status'=>TRUE, 'timetrackInfo'=>$timetrackInfo), true);
        echo $timetrackInfo;
		} else {
            $js_encode = json_encode(array('status'=>FALSE, 'message'=>'There is no record yet.'), true);
        }
		header('Content-Type: application/json');
		echo $js_encode;
		break;
	default:
	header("HTTP/1.0 405 Method Not Allowed");
	break;
}

function console_log( $data ){
	echo '<script>';
	echo 'console.log('. json_encode( $data ) .')';
	echo '</script>';
  }
?>