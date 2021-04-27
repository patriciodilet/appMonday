<?php
/**
 * @package Activity class
 *
 * @author Patricio Díaz / Legaltec
 *
 * @email  pdiazl@legaltec.cl
 *   
 */
include("DBConnection.php");
 class Activity
 {
    private $_userId;
	private $_userEmail;
    private $_nameBoard;
 	private $_itemName;
	private $_duration;
	private $_date;
    
    

	public function setUserId($userId) {
        $this->_userId = $userId;
    }

    public function setUserEmail($userEmail) {
        $this->_userEmail = $userEmail;
    }

    public function setNameBoard($nameBoard) {
        $this->_nameBoard = $nameBoard;
    }    

    public function setItemName($itemName) {
        $this->_itemName = $itemName;
    }   

    public function setduration($duration) {
        $this->_duration = $duration;
    } 

    public function setDate($date) {
        $this->_date = $date;
    } 

    public function __construct() {

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

 }

?>