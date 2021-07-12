<?php
/**
 * @package Email class
 *
 * @author Patricio Díaz / Legaltec
 *
 * @email  pdiazl@legaltec.cl
 *   
 */
include("DBConnection.php");
$confApp = include('../class/ConfigApp.php');
 class Email
 {
    private $_itemId;
    private $_userId;
    private $_type;
    private $_title;
    private $_content;
    private $_created;
    private $_modified;
    private $_status;

	public function setItemId($itemId) {
        $this->_itemId = $itemId;
    }

	public function setUserId($userId) {
        $this->_userId = is_array($userId) ? reset($userId) : $userId;
    }

    public function setUserEmail($userEmail) {
        $this->_userEmail = $userEmail;
    }

    public function setType($type) {
        $this->_type = $type;
    }

    public function setTitle($title) {
        $this->_title = $title;
    }

    public function setContent($content) {
        $this->_content = $content;
    }

    public function setCreated() 
    {
        $dataTime= date("Y-m-d H:i:s");
        $this->_created = $dataTime;
    }

    public function setModified() 
    {
        $dataTime= date("Y-m-d H:i:s");
        $this->_modified = $dataTime;
    }

    public function setStatus() 
    {
        $this->_status = true; // active
    }

    public function __construct() {
        $this->db = new DBConnection();
        $this->db = $this->db->returnConnection();
    }
     
    public function updateSendEmailStatusByItemId() {
        try {
		    $sql = "UPDATE MondayGestionDiaria SET sendEmail=1 WHERE itemId=:itemId";
		    $data = [
			    'itemId' => $this->_itemId
			];
			$stmt = $this->db->prepare($sql);
			$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;
		} catch (Exception $e) {
			die("There's an error in the query!");
		}
    }

    public function getReportByUser() {
        try {
            $sql = "
            SELECT t1.itemId, t1.userEmail, t1.nameBoard, t1.itemName, t1.duration, t1.TPP, t1.milestone, t1.date, t1.postText, t1.responseText, t1.sendEmail
            FROM MondayGestionDiaria t1
            INNER JOIN
            (
                SELECT `itemName`, MAX(id) AS max_id
                FROM MondayGestionDiaria
                where userEmail=:userEmail
                GROUP BY `itemName`
            ) t2 ON t1.`itemName` = t2.`itemName` AND t1.id = t2.max_id
            where YEARWEEK(STR_TO_DATE(t1.date,'%d-%m-%Y'))=YEARWEEK(CURDATE());
            ";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'userEmail' => $this->_userEmail
			];
		    $stmt->execute($data);
            // where sendEmail is null and userId=:userId
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    public function getAllReportByWeek() {
        try {
            $sql = "
            SELECT t1.itemId, t1.userEmail, t1.nameBoard, t1.itemName, t1.duration, t1.TPP, t1.milestone, t1.date, t1.postText, t1.responseText, t1.sendEmail
            FROM MondayGestionDiaria t1
            INNER JOIN
            (
                SELECT `itemName`, MAX(id) AS max_id
                FROM MondayGestionDiaria
                GROUP BY `itemName`
            ) t2 ON t1.`itemName` = t2.`itemName` AND t1.id = t2.max_id
            where YEARWEEK(STR_TO_DATE(t1.date,'%d-%m-%Y'))=YEARWEEK(CURDATE())
            order by date, userEmail desc;
            ";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
            // where sendEmail is null and userId=:userId
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    public function getAllReportByMonth() {
        try {
            $sql = "
            SELECT t1.itemId, t1.userEmail, t1.nameBoard, t1.itemName, t1.duration, t1.TPP, t1.milestone, t1.date, t1.postText, t1.responseText, t1.sendEmail
            FROM MondayGestionDiaria t1
            INNER JOIN
            (
                SELECT `itemName`, MAX(id) AS max_id
                FROM MondayGestionDiaria
                GROUP BY `itemName`
            ) t2 ON t1.`itemName` = t2.`itemName` AND t1.id = t2.max_id
            where YEARWEEK(STR_TO_DATE(t1.date,'%d-%m-%Y'))=YEARWEEK(CURDATE())
            order by date, userEmail desc;
            ";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
            // where sendEmail is null and userId=:userId
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    public function getAllReportDataByUser() {
        try {
            $sql = "
            SELECT t1.itemId, t1.userEmail, t1.nameBoard, t1.itemName, t1.duration, t1.TPP, t1.milestone, t1.date, t1.postText, t1.responseText
            FROM MondayGestionDiaria t1
            INNER JOIN
            (
                SELECT `itemName`, MAX(id) AS max_id
                FROM MondayGestionDiaria
                where sendEmail is null
                GROUP BY `itemName`
            ) t2 ON t1.`itemName` = t2.`itemName` AND t1.id = t2.max_id;
            ";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
            // where sendEmail is null and userId=:userId
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    public function getWeekReportByUser() {
        try {
            $sql = "
            SELECT t1.itemId, t1.userEmail, t1.nameBoard, t1.itemName, t1.duration, t1.TPP, t1.milestone, t1.date, t1.postText, t1.responseText, t1.sendEmail
            FROM MondayGestionDiaria.MondayGestionDiaria t1
            INNER JOIN
            (
                SELECT `itemName`, MAX(id) AS max_id
                FROM MondayGestionDiaria.MondayGestionDiaria
                GROUP BY `itemName`
            ) t2 ON t1.`itemName` = t2.`itemName` AND t1.id = t2.max_id
            where YEARWEEK(STR_TO_DATE(t1.date,'%d-%m-%Y'))=YEARWEEK(CURDATE())
            ";
		    $stmt = $this->db->prepare($sql);
		    $stmt->execute();
            // where sendEmail is null and userId=:userId
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }


    public function sendEmail($to, $cc, $headers, $arrayData, $subject, $message){
        $fileName = md5(date('Y-m-d H:i:s:u')) . '.csv';
        $fp = fopen($confApp['filesDir'] . $fileName .'', 'w');
        fputcsv($fp, $headers);

        foreach ($arrayData as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        $urlFile = $confApp['urlFile'] . $fileName ."";
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
        $result = file_get_contents($confApp['clientEmail'], false, $context);
        return $result;
    }



 }

?>