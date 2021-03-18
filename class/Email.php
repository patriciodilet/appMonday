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
 class Email
 {
    private $_itemId;
    private $_userId;

	public function setItemId($itemId) {
        $this->_itemId = $itemId;
    }

	public function setUserId($userId) {
        $this->_userId = is_array($userId) ? reset($userId) : $userId;
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

    public function getReportData() {
        try {
            $sql = "
            SELECT t1.itemId, t1.userEmail, t1.nameBoard, t1.itemName, t1.duration, t1.TPP, t1.milestone, t1.date, t1.postText, t1.responseText
            FROM MondayGestionDiaria t1
            INNER JOIN
            (
                SELECT `itemName`, MAX(id) AS max_id
                FROM MondayGestionDiaria
                where sendEmail is null and userId=:userId	
                GROUP BY `itemName`
            ) t2 ON t1.`itemName` = t2.`itemName` AND t1.id = t2.max_id;
            ";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'userId' => $this->_userId
			];
		    $stmt->execute($data);
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		    //$result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }
 }

?>