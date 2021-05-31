<?php
class Email {

    public function sendEmail($to, $cc, $headers, $arrayData, $subject, $message){
        $dir = "/var/www/html/ApiMonday/report/files/";
        $fileName = md5(date('Y-m-d H:i:s:u')) . '.csv';

        $fp = fopen($dir . $fileName .'', 'w');

        //Add the headers
        fputcsv($fp, $headers);

        foreach ($arrayData as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        $urlFile = "http://3.211.203.97/ApiMonday/report/files/" . $fileName ."";
        $url = 'http://204.93.172.87/soap/clienteMonday.php';
        $data = array('urlFile' => $urlFile, 
                    'fileName' => $fileName,
                    'to' => $to,
                    'cc' => $cc,
                    'subject' => $subject,
                    'message' => $message 
                    );
        
        $options = array('http' => array(
            'method'  => 'POST',
            'content' => http_build_query($data)
        ));
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        return $result;
        
    }
    
}

?>