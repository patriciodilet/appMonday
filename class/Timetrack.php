<?php
/**
 * @package Timetrack class
 *
 * @author Patricio Díaz
 *
 * @email  pdiazl@legaltec.cl
 *   
 */

include("DBConnection.php");
class Timetrack 
{
    protected $db;
    private $_timetrackID;
    private $_timetrack;
	private $_userEmail;
	private $_TPP;
	private $_itemName;
	private $_duration;
	private $_date;
	private $_milestone;
	private $_postText;
	private $_responseText;
	private $_lastResponseId;
	private $_creatorIdResponse;
	private $_creatorIdPost;
    private $_nameBoard;

    public function setTimeTrackID($timetrackID) {
        $this->_timetrackID = $timetrackID;
    }

    public function setBoardId($boardId) {
        $this->_boardId = $boardId;
    }

	public function setItemId($itemId) {
        $this->_itemId = $itemId;
    }

	public function setUserId($userId) {
        $this->_userId = $userId;
    }

	public function setUserEmail($userEmail){
		$this->_userEmail = $userEmail;
	}

	public function setTPP($TPP){
		$this->_TPP = $TPP;
	}

	public function setItemName($itemName){
		$this->_itemName = $itemName;
	}

	public function setDuration($duration){
		$this->_duration = $duration;
	}
    
	public function setMilestone($milestone){
		$this->_milestone = $milestone;
	}

	public function setPostText($postText) {
        $this->_postText = $postText;
    }

	public function setResponseText($responseText) {
        $this->_responseText = $responseText;
    }

	public function setLastResponseId($lastResponseId) {
        $this->_lastResponseId = $lastResponseId;
    }

	public function setCreatorIdResponse($creatorIdResponse) {
        $this->_creatorIdResponse = $creatorIdResponse;
    }

	public function setCreatorIdPost($creatorIdPost) {
        $this->_creatorIdPost = $creatorIdPost;
    }

	public function setNameBoard($nameBoard) {
        $this->_nameBoard = $nameBoard;
    }
	
	public function setDate(){
		$this->_date = date("d-m-Y");
	}

    public function __construct() {
        $this->db = new DBConnection();
        $this->db = $this->db->returnConnection();
    }

    // create Timetrack
    public function createTimetrack() {
		try {
    		$sql = 'INSERT INTO MondayGestionDiaria (boardId, itemId, userId, userEmail, TPP, itemName, duration, milestone, date, postText, responseText, lastResponseId, creatorIdResponse, creatorIdPost, nameBoard)  VALUES (:boardId, :itemId, :userId, :userEmail, :TPP, :itemName, :duration, :milestone, :date, :postText, :responseText, :lastResponseId, :creatorIdResponse, :creatorIdPost, :nameBoard)';
    		// $sql = 'INSERT INTO MondayGestionDiaria (boardId, itemId, userId, userEmail, TPP, itemName, duration, milestone, date)  VALUES (:boardId, :itemId, :userId, :userEmail, :TPP, :itemName, :duration, :milestone, :date)';
    		$data = [
			    'boardId' => $this->_boardId,
			    'itemId' => $this->_itemId,
			    'userId' => $this->_userId,
			    'userEmail' => $this->_userEmail,
			    'TPP' => $this->_TPP,
			    'itemName' => $this->_itemName,
			    'duration' => $this->_duration,
				'milestone' => $this->_milestone,
			    'date' => $this->_date,
			    'postText' => $this->_postText,
			    'responseText' => $this->_responseText,
			    'lastResponseId' => $this->_lastResponseId,
			    'creatorIdResponse' => $this->_creatorIdResponse,
			    'creatorIdPost' => $this->_creatorIdPost,
			    'nameBoard' => $this->_nameBoard 
			];
	    	$stmt = $this->db->prepare($sql);
	    	$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;

		} catch (Exception $e) {
    		die("There's an error in the query! " . $e);
		}

    }

    // update Timetrack
    public function updateTimetrack() {
        try {
		    $sql = "UPDATE MondayGestionDiaria SET timetrack=:timetrack WHERE id=:timetrackId";
		    $data = [
			    'timetrack' => $this->_timetrack,
                'timetrackId' => $this->_timetrackId,
			];
			$stmt = $this->db->prepare($sql);
			$stmt->execute($data);
			$status = $stmt->rowCount();
            return $status;
		} catch (Exception $e) {
			die("There's an error in the query!");
		}
    }
   
    // getAll timetrack
    public function getAllTimetrack() {
    	try {
    		$sql = "SELECT * FROM MondayGestionDiaria";
		    $stmt = $this->db->prepare($sql);

		    $stmt->execute();
		    $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    // get Timetrack
    public function getTimetrack() {
    	try {
    		$sql = "SELECT * FROM MondayGestionDiaria WHERE id=:timetrackId";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'timetrackId' => $this->_timetrackID
			];
		    $stmt->execute($data);
		    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result;
		} catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }

    // delete Timetrack
    public function deleteTimetrack() {
    	try {
	    	$sql = "DELETE FROM MondayGestionDiaria WHERE id=:timetrackId";
		    $stmt = $this->db->prepare($sql);
		    $data = [
		    	'timetrackId' => $this->_timetrackID
			];
	    	$stmt->execute($data);
            $status = $stmt->rowCount();
            return $status;
	    } catch (Exception $e) {
		    die("There's an error in the query!");
		}
    }


}
?>