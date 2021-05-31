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
 class Control
 {
    protected $db;
	private $_userId;
	private $_userEmail;


    public function setUserId($userId) {
        $this->_userId = $userId;
    }

    public function setUserEmail($userEmail){
		$this->_userEmail = $userEmail;
	}

    public function __construct() {
        $this->db = new DBConnection();
        $this->db = $this->db->returnConnection();
    }
     
    // update Timetrack
    public function upsertUser() {
        try {
            $sql = 'INSERT INTO User (userId,userEmail) 
                            VALUES (:userId, :userEmail) 
                        ON DUPLICATE KEY UPDATE userEmail=:userEmail';
            $data = [
                'userId' => $this->_userId,
                'userEmail' => $this->_userEmail
            ];
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            $status = $stmt->rowCount();
            return $status;
        } catch (Exception $e) {
            die("There's an error in the query!");
        }
    }
     

    public function getUsersUntrackedTime() {
        try {
            $sql = "
            SELECT  T1.userEmail  FROM MondayGestionDiaria.User T1
            WHERE T1.userEmail NOT IN (
            select distinct T2.userEmail
            from MondayGestionDiaria.MondayGestionDiaria T2
            where T2.date = DATE_FORMAT(subdate(curdate(), 2), '%d-%m-%Y')
            ) AND T1.isActive = 1
            UNION
            select distinct T2.userEmail
            from MondayGestionDiaria.MondayGestionDiaria T2
            where T2.date = DATE_FORMAT(subdate(curdate(), 2), '%d-%m-%Y')
            AND T2.userEmail NOT IN (SELECT T1.userEmail FROM MondayGestionDiaria.User T1)
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