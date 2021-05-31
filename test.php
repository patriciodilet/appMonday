<?php

include('../helper/Email.php');
$bodyHtml = '<h1>Registro de horas</h1>';
$to = "patricio.dilet@gmail.com";
$subject = "testing";
$message = "email message";
$arrayData = [];


$email = new Email();
$emailList = array("patricio.dilet@gmail.com");
$res = $email->sendEmail($emailList, $arrayData, "Registro de horas", $bodyHtml);
echo $res;


/*
// if(count($arrayData) > 0){
    $dir = "/var/www/html/ApiMonday/report/files/";
    $fileName = md5(date('Y-m-d H:i:s:u')) . '.csv';
    $fp = fopen($dir . $fileName .'', 'w');
    foreach ($arrayData as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
    $urlFile = "http://3.211.203.97/ApiMonday/report/files/" . $fileName ."";
// }   
*/
//http://204.93.172.87/soap/clienteMonday.php?email=patricio.dilet@gmail.com&urlFile=%27http://3.211.203.97/ApiMonday/report/files/%27&fileName=00a3a4fa8d8e93463fdd717377c26870.csv&subject=test

/*
$urlFile = 'http://3.211.203.97/ApiMonday/report/files/'; 
$fileName = '1a25e0c738f2071056d5ca502301ef95.csv';

$url = 'http://204.93.172.87/soap/clienteMonday.php';
$data = array('urlFile' => $urlFile, 
            'fileName' => $fileName,
            'to' => $to,
            'subject' => $subject,
            'message' => $message 
            );

$options = array('http' => array(
    'method'  => 'POST',
    'content' => http_build_query($data)
));
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
print_r($result);
echo "=> " . $result;
*/
?>